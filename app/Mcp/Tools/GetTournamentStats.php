<?php

namespace App\Mcp\Tools;

use App\Models\Event;
use App\Models\Game;
use App\Models\Organizer;
use App\Models\Player;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Get overall tournament platform statistics, including events, players, organizers, and game divisions count.')]
class GetTournamentStats extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $totalEvents = Event::count();
        $totalPlayers = Player::count();
        $totalOrganizers = Organizer::count();
        $totalGames = Game::count();

        $stats = "NEX-Sport Platform Statistics:\n".
                 "- Total Tournaments: {$totalEvents}\n".
                 "- Registered Players: {$totalPlayers}\n".
                 "- Active Organizers: {$totalOrganizers}\n".
                 "- Game Divisions: {$totalGames}\n";

        return Response::text($stats);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            //
        ];
    }
}
