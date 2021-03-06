<?php

namespace App\Enum;

final class OrderStatuses
{
    public const PENDING     = 1;
    public const NEW         = 2;
    public const IN_PROGRESS = 3;
    public const FINISHED    = 4;

    public static function getAllowedStatuses():array
    {
        return [
            self::PENDING,
            self::NEW,
            self::IN_PROGRESS,
            self::FINISHED,
        ];
    }
}
