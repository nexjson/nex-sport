<?php

namespace App\Policies;

use App\Models\EventGame;
use App\Models\GameMatch;
use App\Models\User;

class GameMatchPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can generate brackets.
     */
    public function generate(User $user, EventGame $eventGame): bool
    {
        return $eventGame->event->organizer->user_id === $user->id || in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can update the match score.
     */
    public function updateScore(User $user, GameMatch $match): bool
    {
        return $match->eventGame->event->organizer->user_id === $user->id || in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can update the match schedule.
     */
    public function updateSchedule(User $user, GameMatch $match): bool
    {
        return $match->eventGame->event->organizer->user_id === $user->id || in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can toggle match status.
     */
    public function toggleMatchStatus(User $user, GameMatch $match): bool
    {
        return $match->eventGame->event->organizer->user_id === $user->id || in_array($user->role?->name, ['super-admin', 'admin']);
    }
}
