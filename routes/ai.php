<?php

use App\Mcp\Servers\TournamentServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/tournament', TournamentServer::class);
