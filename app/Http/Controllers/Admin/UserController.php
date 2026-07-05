<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', User::class);

        return Inertia::render('admin/users/Index', [
            'users' => $this->userRepository->all()->map(fn ($user) => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status,
                'last_login' => $user->last_login?->toDateTimeString(),
                'role' => $user->role?->name,
            ]),
            'roles' => Role::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = true;

        $this->userRepository->create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        $user = $this->userRepository->find($id);

        if (! $user) {
            abort(404);
        }

        Gate::authorize('update', $user);

        $validated = $request->validated();

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $this->userRepository->update($id, $validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = $this->userRepository->find($id);

        if (! $user) {
            abort(404);
        }

        Gate::authorize('delete', $user);

        // Super Admin cannot delete their own account
        if (auth()->id() === $id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        // Verify if user has active organizer or team profiles
        if ($user->organizer()->exists() || $user->player()->exists()) {
            // Check if organizer has active events, or player has active squads
            $hasActiveOrganizer = $user->organizer?->events()->whereIn('status', ['registration', 'ongoing'])->exists();
            $hasActivePlayer = $user->player?->squads()->exists();

            if ($hasActiveOrganizer || $hasActivePlayer) {
                return redirect()->route('admin.users.index')->with('error', 'Cannot delete user with active organizer/squad data.');
            }
        }

        $this->userRepository->delete($id);

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle status (ban/unban user).
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $user = $this->userRepository->find($id);

        if (! $user) {
            abort(404);
        }

        Gate::authorize('toggleStatus', $user);

        if (auth()->id() === $id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot ban yourself.');
        }

        $this->userRepository->update($id, [
            'status' => ! $user->status,
        ]);

        $statusMessage = $user->status ? 'User deactivated (banned).' : 'User activated.';

        return redirect()->route('admin.users.index')->with('success', $statusMessage);
    }
}
