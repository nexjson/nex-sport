<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
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
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:organizers',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id|unique:organizers,user_id',
            'status' => 'required|boolean',
        ]);

        Organizer::create($validated);

        return redirect()->route('admin.organizers.index')->with('success', 'Organizer profile created and assigned successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organizer $organizer): RedirectResponse
    {
        // Check if user is admin or the actual owner of this organizer profile
        if (auth()->user()->role?->name !== 'super-admin' && auth()->user()->role?->name !== 'admin') {
            if ($organizer->user_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:organizers,name,'.$organizer->id,
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $organizer->update($validated);

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
        // Guard: check if organizer has events
        if ($organizer->events()->exists()) {
            return redirect()->route('admin.organizers.index')->with('error', 'Cannot delete organizer with active tournaments.');
        }

        $organizer->delete();

        return redirect()->route('admin.organizers.index')->with('success', 'Organizer profile deleted successfully.');
    }
}
