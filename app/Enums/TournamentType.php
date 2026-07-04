<?php

namespace App\Enums;

enum TournamentType: string
{
    case SingleElimination = 'single_elimination';
    case DoubleElimination = 'double_elimination';
    case RoundRobin = 'round_robin';
    case Swiss = 'swiss';
}
