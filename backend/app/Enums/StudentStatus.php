<?php

namespace App\Enums;

enum StudentStatus: string
{
    case Requested = 'requested';
    case Trial = 'trial';
    case Active = 'active';
    case PendingPayment = 'pending_payment';
    case Inactive = 'inactive';
}
