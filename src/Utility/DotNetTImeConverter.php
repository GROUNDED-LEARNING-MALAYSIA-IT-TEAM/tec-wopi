<?php

declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Utility;

use Cake\I18n\FrozenTime;
use DateTimeInterface;

class DotNetTimeConverter
{
    private const MULTIPLIER = 1e7;
    private const OFFSET = 621355968e9;

    public static function toDatetime(string $ticks): DateTimeInterface
    {
        return FrozenTime::createFromTimestamp((int)(((float)$ticks - self::OFFSET) / self::MULTIPLIER));
    }

    public static function toTicks(DateTimeInterface $datetime): string
    {
        return (string)((int)(($datetime->getTimestamp() * self::MULTIPLIER) + self::OFFSET));
    }
}
