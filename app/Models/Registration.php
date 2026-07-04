<?php

namespace App\Models;

use App\Enums\RegistrationPaymentStatus;
use App\Enums\RegistrationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $squad_id
 * @property int $event_games_id
 * @property RegistrationStatus $status
 * @property RegistrationPaymentStatus $payment_status
 * @property int $ticket_price
 * @property int $admin_fee
 * @property int $amount_paid
 * @property string|null $payment_method
 * @property string|null $payment_receipt
 * @property Carbon|null $paid_at
 * @property Carbon|null $refunded_at
 * @property string|null $refund_receipt
 * @property Carbon $registered_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'squad_id', 'event_games_id', 'status', 'payment_status',
    'ticket_price', 'admin_fee', 'amount_paid', 'payment_method',
    'payment_receipt', 'paid_at', 'refunded_at', 'refund_receipt',
    'registered_at',
])]
class Registration extends Model
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
            'status' => RegistrationStatus::class,
            'payment_status' => RegistrationPaymentStatus::class,
            'ticket_price' => 'integer',
            'admin_fee' => 'integer',
            'amount_paid' => 'integer',
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
            'registered_at' => 'datetime',
        ];
    }

    /**
     * Get the squad registered.
     *
     * @return BelongsTo<Squad, $this>
     */
    public function squad(): BelongsTo
    {
        return $this->belongsTo(Squad::class);
    }

    /**
     * Get the event game registered to.
     *
     * @return BelongsTo<EventGame, $this>
     */
    public function eventGame(): BelongsTo
    {
        return $this->belongsTo(EventGame::class, 'event_games_id');
    }
}
