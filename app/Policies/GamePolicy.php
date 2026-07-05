<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;

class GamePolicy
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
    public function view(User $user, Game $game): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Game $game): bool
    {
        return in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Game $game): bool
    {
        return $user->role?->name === 'super-admin';
    }

    /**
     * Determine whether the user can add a role to the game.
     */
    public function storeRole(User $user, Game $game): bool
    {
        return in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can remove a role from the game.
     */
    public function destroyRole(User $user, Game $game): bool
    {
        return in_array($user->role?->name, ['super-admin', 'admin']);
    }
}
