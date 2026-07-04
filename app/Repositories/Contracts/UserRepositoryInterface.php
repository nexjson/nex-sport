<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * Get all users.
     *
     * @return Collection<int, User>
     */
    public function all(): Collection;

    /**
     * Find user by id.
     */
    public function find(int $id): ?User;

    /**
     * Create user.
     */
    public function create(array $data): User;

    /**
     * Update user.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete user (soft delete).
     */
    public function delete(int $id): bool;

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User;
}
