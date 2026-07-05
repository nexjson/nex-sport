<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Models\Game;
use App\Models\GameRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', Game::class);

        return Inertia::render('admin/games/Index', [
            'games' => Game::with('roles')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameRequest $request): RedirectResponse
    {
        Gate::authorize('create', Game::class);

        Game::create($request->validated());

        return redirect()->route('admin.games.index')->with('success', 'Game created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameRequest $request, Game $game): RedirectResponse
    {
        Gate::authorize('update', $game);

        $game->update($request->validated());

        return redirect()->route('admin.games.index')->with('success', 'Game updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game): RedirectResponse
    {
        Gate::authorize('delete', $game);

        // Guard: check if game is used in event_games
        if ($game->eventGames()->exists()) {
            return redirect()->route('admin.games.index')->with('error', 'Cannot delete a game that is currently used in events.');
        }

        $game->delete();

        return redirect()->route('admin.games.index')->with('success', 'Game deleted successfully.');
    }

    /**
     * Store a role for the game.
     */
    public function storeRole(Request $request, Game $game): RedirectResponse
    {
        Gate::authorize('storeRole', $game);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        // Duplicate check
        $exists = $game->roles()->where('name', $validated['name'])->exists();
        if ($exists) {
            return redirect()->route('admin.games.index')->with('error', 'Role already exists for this game.');
        }

        $game->roles()->create($validated);

        return redirect()->route('admin.games.index')->with('success', 'Game role added successfully.');
    }

    /**
     * Remove a game role.
     */
    public function destroyRole(GameRole $gameRole): RedirectResponse
    {
        Gate::authorize('destroyRole', $gameRole->game);

        // Check if players are assigned to this role
        if ($gameRole->players()->exists()) {
            return redirect()->route('admin.games.index')->with('error', 'Cannot delete a role that is assigned to players.');
        }

        $gameRole->delete();

        return redirect()->route('admin.games.index')->with('success', 'Game role deleted successfully.');
    }
}
