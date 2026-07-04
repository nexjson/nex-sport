<?php

namespace App\Enums;

enum SquadStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Disbanded = 'disbanded';
}
