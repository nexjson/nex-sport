<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $event_games_id
 * @property int $squad_id
 * @property int $wins
 * @property int $losses
 * @property int $draws
 * @property int $points
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['event_games_id', 'squad_id', 'wins', 'losses', 'draws', 'points'])]
class Standing extends Model
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
            'wins' => 'integer',
            'losses' => 'integer',
            'draws' => 'integer',
            'points' => 'integer',
        ];
    }

    /**
     * Get the event game this standing is for.
     *
     * @return BelongsTo<EventGame, $this>
     */
    public function eventGame(): BelongsTo
    {
        return $this->belongsTo(EventGame::class, 'event_games_id');
    }

    /**
     * Get the squad.
     *
     * @return BelongsTo<Squad, $this>
     */
    public function squad(): BelongsTo
    {
        return $this->belongsTo(Squad::class);
    }
}
