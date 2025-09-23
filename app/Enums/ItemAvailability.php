<?php

namespace App\Enums;

enum ItemAvailability: string
{
    case Available = 'available';
    case CheckedOut = 'checked_out';
    case Restricted = 'restricted';
    case Conservation = 'conservation';
    case Missing = 'missing';
    case Damaged = 'damaged';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::CheckedOut => 'Checked Out',
            self::Restricted => 'Restricted Access',
            self::Conservation => 'In Conservation',
            self::Missing => 'Missing',
            self::Damaged => 'Damaged',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }
}
