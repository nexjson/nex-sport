<?php

namespace App\Models;

use App\Enums\SquadStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $team_id
 * @property int $game_id
 * @property string $name
 * @property string $short_name
 * @property string|null $logo
 * @property int $max_players
 * @property SquadStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['team_id', 'game_id', 'name', 'short_name', 'logo', 'max_players', 'status'])]
class Squad extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SquadStatus::class,
            'max_players' => 'integer',
        ];
    }

    /**
     * Get the team (organization) this squad belongs to.
     *
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the game this squad plays.
     *
     * @return BelongsTo<Game, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the players currently in this squad.
     *
     * @return HasMany<Player, $this>
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Get the transfer histories where this squad was the origin.
     *
     * @return HasMany<TransferHistory, $this>
     */
    public function transferHistoriesFrom(): HasMany
    {
        return $this->hasMany(TransferHistory::class, 'from_squad_id');
    }

    /**
     * Get the transfer histories where this squad was the destination.
     *
     * @return HasMany<TransferHistory, $this>
     */
    public function transferHistoriesTo(): HasMany
    {
        return $this->hasMany(TransferHistory::class, 'to_squad_id');
    }

    /**
     * Get the requests/invites for this squad.
     *
     * @return HasMany<SquadRequest, $this>
     */
    public function squadRequests(): HasMany
    {
        return $this->hasMany(SquadRequest::class);
    }

    /**
     * Get the registrations of this squad to event games.
     *
     * @return HasMany<Registration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Get the standing records of this squad.
     *
     * @return HasMany<Standing, $this>
     */
    public function standings(): HasMany
    {
        return $this->hasMany(Standing::class);
    }

    /**
     * Get the matches played by this squad as home team.
     *
     * @return HasMany<Match, $this>
     */
    public function matchesHome(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'squad_home_id');
    }

    /**
     * Get the matches played by this squad as away team.
     *
     * @return HasMany<GameMatch, $this>
     */
    public function matchesAway(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'squad_away_id');
    }

    /**
     * Get the reward claims earned by this squad.
     *
     * @return HasMany<RewardClaim, $this>
     */
    public function rewardClaims(): HasMany
    {
        return $this->hasMany(RewardClaim::class);
    }
}
