<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('player/teams/Index', [
            'teams' => Team::where('user_id', auth()->id())->withCount('squads')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams',
            'short_name' => 'required|string|max:50',
            'logo' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = true;

        Team::create($validated);

        return redirect()->route('player.teams.index')->with('success', 'Team organization created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team): RedirectResponse
    {
        if ($team->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,'.$team->id,
            'short_name' => 'required|string|max:50',
            'logo' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $team->update($validated);

        return redirect()->route('player.teams.index')->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team): RedirectResponse
    {
        if ($team->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Guard: check if team has active squads
        if ($team->squads()->exists()) {
            return redirect()->route('player.teams.index')->with('error', 'Cannot delete team that has active squads.');
        }

        $team->delete();

        return redirect()->route('player.teams.index')->with('success', 'Team deleted successfully.');
    }
}
