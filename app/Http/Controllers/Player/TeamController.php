<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', Team::class);

        return Inertia::render('player/teams/Index', [
            'teams' => Team::where('user_id', auth()->id())->withCount('squads')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request): RedirectResponse
    {
        Gate::authorize('create', Team::class);

        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $validated['status'] = true;

        Team::create($validated);

        return redirect()->route('player.teams.index')->with('success', 'Team organization created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeamRequest $request, Team $team): RedirectResponse
    {
        Gate::authorize('update', $team);

        $team->update($request->validated());

        return redirect()->route('player.teams.index')->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team): RedirectResponse
    {
        Gate::authorize('delete', $team);

        // Guard: check if team has active squads
        if ($team->squads()->exists()) {
            return redirect()->route('player.teams.index')->with('error', 'Cannot delete team that has active squads.');
        }

        $team->delete();

        return redirect()->route('player.teams.index')->with('success', 'Team deleted successfully.');
    }
}
