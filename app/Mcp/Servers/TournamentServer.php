<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\GetTournamentStats;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Tournament Server')]
#[Version('0.0.1')]
#[Instructions('Instructions describing how to use the server and its features.')]
class TournamentServer extends Server
{
    protected array $tools = [
        GetTournamentStats::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
