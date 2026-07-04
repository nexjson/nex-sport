<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('admin/games/Index', [
            'games' => Game::with('roles')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:games',
            'category' => 'required|string|max:100', // e.g. esports, sports
            'status' => 'required|boolean',
        ]);

        Game::create($validated);

        return redirect()->route('admin.games.index')->with('success', 'Game created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Game $game): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:games,name,'.$game->id,
            'category' => 'required|string|max:100',
            'status' => 'required|boolean',
        ]);

        $game->update($validated);

        return redirect()->route('admin.games.index')->with('success', 'Game updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game): RedirectResponse
    {
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
        // Check if players are assigned to this role
        if ($gameRole->players()->exists()) {
            return redirect()->route('admin.games.index')->with('error', 'Cannot delete a role that is assigned to players.');
        }

        $gameRole->delete();

        return redirect()->route('admin.games.index')->with('success', 'Game role deleted successfully.');
    }
}
