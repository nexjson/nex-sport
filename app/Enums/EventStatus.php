<?php

namespace App\Enums;

enum EventStatus: string
{
    case Draft = 'draft';
    case WaitingPayment = 'waiting_payment';
    case WaitingVerification = 'waiting_verification';
    case Registration = 'registration';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
