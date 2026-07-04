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
 * @property int $event_id
 * @property int $game_id
 * @property int $ticket_price
 * @property int $max_participants
 * @property int $admin_ticket_fee
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['event_id', 'game_id', 'ticket_price', 'max_participants', 'admin_ticket_fee'])]
class EventGame extends Model
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
            'ticket_price' => 'integer',
            'max_participants' => 'integer',
            'admin_ticket_fee' => 'integer',
        ];
    }

    /**
     * Get the event this game division belongs to.
     *
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the game for this event game.
     *
     * @return BelongsTo<Game, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the rewards/prizes defined for this event game.
     *
     * @return HasMany<Reward, $this>
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class, 'event_games_id');
    }

    /**
     * Get the matches scheduled for this event game.
     *
     * @return HasMany<GameMatch, $this>
     */
    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'event_games_id');
    }

    /**
     * Get the squad registrations for this event game.
     *
     * @return HasMany<Registration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'event_games_id');
    }

    /**
     * Get the standings for this event game.
     *
     * @return HasMany<Standing, $this>
     */
    public function standings(): HasMany
    {
        return $this->hasMany(Standing::class, 'event_games_id');
    }
}
