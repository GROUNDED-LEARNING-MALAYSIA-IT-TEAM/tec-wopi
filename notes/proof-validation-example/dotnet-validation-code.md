```
<?php

namespace Nagi\LaravelWopi\Support;

use DateTimeImmutable;
use DateTimeInterface;

class DotNetTimeConverter
{
    private const MULTIPLIER = 1e7;

    private const OFFSET = 621355968e9;

    public static function toDatetime(string $ticks): DateTimeInterface
    {
        return DateTimeImmutable::createFromFormat(
            'U',
            (string) ((int) (((float) $ticks - self::OFFSET) / self::MULTIPLIER))
        );
    }

    public static function toTicks(DateTimeInterface $datetime): string
    {
        return (string) (int) (($datetime->getTimestamp() * self::MULTIPLIER) + self::OFFSET);
    }
}
```

The provided PHP class `DotNetTimeConverter` is a utility that converts between .NET's "ticks" (which represent the number of 100-nanosecond intervals that have elapsed since 12:00:00 midnight, January 1, 0001) and PHP's `DateTimeInterface` objects.

The class contains two static methods:

- `toDatetime(string $ticks): DateTimeInterface` which converts a .NET tick value to a `DateTimeInterface` object in PHP.
- `toTicks(DateTimeInterface $datetime): string` which converts a `DateTimeInterface` object to a .NET tick value.

Here's a breakdown of the class methods:

1. `toDatetime` takes a string representing .NET ticks, subtracts the .NET epoch offset (the number of ticks from the .NET epoch to the Unix epoch), divides by the multiplier to convert ticks to seconds, and creates a `DateTimeImmutable` object from the Unix timestamp.

2. `toTicks` takes a `DateTimeInterface` object, gets its Unix timestamp, multiplies by the multiplier to convert seconds to ticks, and then adds the .NET epoch offset to get the .NET ticks.

The `MULTIPLIER` constant is used to convert between ticks and seconds, and the `OFFSET` constant is the difference in ticks between the .NET epoch (January 1, 0001) and the Unix epoch (January 1, 1970).

Here is an example of how you would use this class:

```php
// Convert .NET ticks to DateTimeInterface
$dotNetTicks = '637675904000000000'; // Example .NET ticks value
$dateTime = DotNetTimeConverter::toDatetime($dotNetTicks);
echo $dateTime->format('Y-m-d H:i:s'); // Outputs the equivalent date and time

// Convert DateTimeInterface to .NET ticks
$now = new DateTimeImmutable(); // Current time as DateTimeImmutable object
$ticks = DotNetTimeConverter::toTicks($now);
echo $ticks; // Outputs the equivalent .NET ticks value
```

This utility class is particularly useful when you need to interoperate between PHP and .NET systems that represent date and time values differently.
