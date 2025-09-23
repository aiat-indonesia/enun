<?php

namespace App\Enums;

enum WorkType: string
{
    case Manuscript = 'manuscript';
    case Book = 'book';
    case Article = 'article';
    case Journal = 'journal';
    case Compilation = 'compilation';
    case Translation = 'translation';
    case Commentary = 'commentary';
    case Tafsir = 'tafsir';
    case Thesis = 'thesis';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Manuscript => 'Manuscript',
            self::Book => 'Book',
            self::Article => 'Article',
            self::Journal => 'Journal',
            self::Compilation => 'Compilation',
            self::Translation => 'Translation',
            self::Commentary => 'Commentary',
            self::Tafsir => 'Tafsir',
            self::Thesis => 'Thesis',
            self::Other => 'Other',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }
}
