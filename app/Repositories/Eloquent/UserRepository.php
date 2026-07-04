<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users.
     *
     * @return Collection<int, User>
     */
    public function all(): Collection
    {
        return User::with('role')->get();
    }

    /**
     * Find user by id.
     */
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Create user.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update user.
     */
    public function update(int $id, array $data): bool
    {
        $user = $this->find($id);

        if ($user) {
            return $user->update($data);
        }

        return false;
    }

    /**
     * Delete user (soft delete).
     */
    public function delete(int $id): bool
    {
        $user = $this->find($id);

        if ($user) {
            return $user->delete();
        }

        return false;
    }

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
