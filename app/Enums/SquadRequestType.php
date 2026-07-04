<?php

namespace App\Enums;

enum SquadRequestType: string
{
    case Apply = 'apply';
    case Invite = 'invite';
}
