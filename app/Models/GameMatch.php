<?php

namespace App\Models;

use App\Enums\MatchStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $event_games_id
 * @property int $round
 * @property int $match_order
 * @property int|null $squad_home_id
 * @property int|null $squad_away_id
 * @property int $score_home
 * @property int $score_away
 * @property int|null $winner_id
 * @property MatchStatus $status
 * @property Carbon|null $scheduled_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'event_games_id', 'round', 'match_order', 'squad_home_id',
    'squad_away_id', 'score_home', 'score_away', 'winner_id',
    'status', 'scheduled_at',
])]
class GameMatch extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'matches';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => MatchStatus::class,
            'round' => 'integer',
            'match_order' => 'integer',
            'score_home' => 'integer',
            'score_away' => 'integer',
            'scheduled_at' => 'datetime',
        ];
    }

    /**
     * Get the event game this match is scheduled in.
     *
     * @return BelongsTo<EventGame, $this>
     */
    public function eventGame(): BelongsTo
    {
        return $this->belongsTo(EventGame::class, 'event_games_id');
    }

    /**
     * Get the home squad.
     *
     * @return BelongsTo<Squad, $this>
     */
    public function squadHome(): BelongsTo
    {
        return $this->belongsTo(Squad::class, 'squad_home_id');
    }

    /**
     * Get the away squad.
     *
     * @return BelongsTo<Squad, $this>
     */
    public function squadAway(): BelongsTo
    {
        return $this->belongsTo(Squad::class, 'squad_away_id');
    }

    /**
     * Get the winning squad.
     *
     * @return BelongsTo<Squad, $this>
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(Squad::class, 'winner_id');
    }
}
