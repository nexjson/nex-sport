<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $nickname
 * @property string|null $photo
 * @property int $game_role_id
 * @property int|null $squad_id
 * @property int $game_id
 * @property int|null $jersey_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'name', 'nickname', 'photo', 'game_role_id', 'squad_id', 'game_id', 'jersey_number'])]
class Player extends Model
{
    use HasFactory;

    /**
     * Get the user profile associated with the player.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the game division of this player.
     *
     * @return BelongsTo<Game, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the role/position of this player.
     *
     * @return BelongsTo<GameRole, $this>
     */
    public function gameRole(): BelongsTo
    {
        return $this->belongsTo(GameRole::class);
    }

    /**
     * Get the squad this player belongs to.
     *
     * @return BelongsTo<Squad, $this>
     */
    public function squad(): BelongsTo
    {
        return $this->belongsTo(Squad::class);
    }

    /**
     * Get the transfer history logs for this player.
     *
     * @return HasMany<TransferHistory, $this>
     */
    public function transferHistories(): HasMany
    {
        return $this->hasMany(TransferHistory::class);
    }

    /**
     * Get the requests/invites for this player.
     *
     * @return HasMany<SquadRequest, $this>
     */
    public function squadRequests(): HasMany
    {
        return $this->hasMany(SquadRequest::class);
    }

    /**
     * Get the reward claims earned by this player.
     *
     * @return HasMany<RewardClaim, $this>
     */
    public function rewardClaims(): HasMany
    {
        return $this->hasMany(RewardClaim::class);
    }
}
