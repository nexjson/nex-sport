<?php

namespace App\Enums;

enum MatchStatus: string
{
    case Scheduled = 'scheduled';
    case Live = 'live';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
