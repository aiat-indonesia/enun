<?php

namespace App\Enums;

enum WorkStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case InReview = 'in_review';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Review => 'Review',
            self::InReview => 'In Review',
            self::Published => 'Published',
            self::Archived => 'Archived',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }
}
