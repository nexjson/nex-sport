<?php

namespace App\Policies;

use App\Models\Squad;
use App\Models\User;

class SquadPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Squad $squad): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Squad $squad): bool
    {
        return $squad->team->user_id === $user->id || in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Squad $squad): bool
    {
        return $squad->team->user_id === $user->id || $user->role?->name === 'super-admin';
    }

    /**
     * Determine whether the user can release a player from the squad.
     */
    public function releasePlayer(User $user, Squad $squad): bool
    {
        return $squad->team->user_id === $user->id || $user->role?->name === 'super-admin';
    }
}
