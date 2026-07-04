<?php

namespace App\Enums;

enum RegistrationPaymentStatus: string
{
    case Free = 'free';
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Refunded = 'refunded';
}
