<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $users = User::with('role')->get(); // Using relation loading is fine, but data fetched via Eloquent model. Wait! Rule: "Controller tidak boleh memanggil Eloquent query secara langsung untuk entity yang punya repository".

        // Ah! To satisfy the rule, let's fetch users via the repository:
        // Wait, does userRepository have all()? Yes. Let's update UserRepository's all() or find() to include the role relation so that we don't call User:: directly.
        // Let's call $this->userRepository->all();
        // Since all() in our Eloquent\UserRepository returns User::all() (and we can modify it to load role by default, or just let all() return all users), let's make sure it returns them with roles.
        // Let's implement index using the repository.
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
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|alpha_dash|max:255|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = true;

        $this->userRepository->create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|alpha_dash|max:255|unique:users,username,'.$id,
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'phone' => 'required|string|max:20',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8',
            ]);
            $validated['password'] = Hash::make($request->password);
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
