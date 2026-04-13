<?php

declare(strict_types=1);

namespace CodelDev\LaravelMailLog\Enums;

enum MailHeaderEnum: string
{
    case FROM = 'From';
    case TO   = 'To';
    case CC   = 'Cc';
    case BCC  = 'Bcc';
}
