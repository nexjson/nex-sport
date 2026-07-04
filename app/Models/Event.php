<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Enums\TournamentType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $banner
 * @property int $organizer_id
 * @property TournamentType $tournament_type
 * @property string|null $location
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property Carbon $registration_start
 * @property Carbon $registration_end
 * @property EventStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'name', 'description', 'banner', 'organizer_id', 'tournament_type',
    'location', 'start_date', 'end_date', 'registration_start',
    'registration_end', 'status',
])]
class Event extends Model
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
            'tournament_type' => TournamentType::class,
            'status' => EventStatus::class,
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'registration_start' => 'datetime',
            'registration_end' => 'datetime',
        ];
    }

    /**
     * Get the organizer managing this event.
     *
     * @return BelongsTo<Organizer, $this>
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    /**
     * Get the games included in this event.
     *
     * @return HasMany<EventGame, $this>
     */
    public function eventGames(): HasMany
    {
        return $this->hasMany(EventGame::class);
    }

    /**
     * Get the payments verified for publishing this event.
     *
     * @return HasMany<EventPayment, $this>
     */
    public function eventPayments(): HasMany
    {
        return $this->hasMany(EventPayment::class);
    }

    /**
     * Get the sponsors for this event.
     *
     * @return HasMany<EventSponsor, $this>
     */
    public function eventSponsors(): HasMany
    {
        return $this->hasMany(EventSponsor::class);
    }
}
