<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrganizerRequest;
use App\Http\Requests\UpdateOrganizerRequest;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class OrganizerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', Organizer::class);

        // Find users with 'organizer' role who do not have an organizer profile yet
        $availableUsers = User::whereHas('role', fn ($q) => $q->where('name', 'organizer'))
            ->whereDoesntHave('organizer')
            ->get();

        return Inertia::render('admin/organizers/Index', [
            'organizers' => Organizer::with('user')->get(),
            'availableUsers' => $availableUsers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizerRequest $request): RedirectResponse
    {
        Gate::authorize('create', Organizer::class);

        Organizer::create($request->validated());

        return redirect()->route('admin.organizers.index')->with('success', 'Organizer profile created and assigned successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizerRequest $request, Organizer $organizer): RedirectResponse
    {
        Gate::authorize('update', $organizer);

        $organizer->update($request->validated());

        if (auth()->user()->role?->name === 'organizer') {
            return redirect()->back()->with('success', 'Organizer profile updated successfully.');
        }

        return redirect()->route('admin.organizers.index')->with('success', 'Organizer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organizer $organizer): RedirectResponse
    {
        Gate::authorize('delete', $organizer);

        // Guard: check if organizer has events
        if ($organizer->events()->exists()) {
            return redirect()->route('admin.organizers.index')->with('error', 'Cannot delete organizer with active tournaments.');
        }

        $organizer->delete();

        return redirect()->route('admin.organizers.index')->with('success', 'Organizer profile deleted successfully.');
    }
}
