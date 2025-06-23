<?php

namespace App\Enums;

enum BenefitDecisionEnum: int
{
    case PENDING = 0;
    case APPROVED = 1;
    case DENIED = 2;
}
