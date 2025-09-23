<?php

namespace App\Enums;

enum InstanceFormat: string
{
    case Hardcover = 'hardcover';
    case Paperback = 'paperback';
    case Manuscript = 'manuscript';
    case Digital = 'digital';
    case Microfilm = 'microfilm';
    case Facsimile = 'facsimile';
    case FirstEdition = 'first_edition';
    case RevisedEdition = 'revised_edition';
    case Ebook = 'ebook';
    case Print = 'print';
    case Pdf = 'pdf';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Hardcover => 'Hardcover',
            self::Paperback => 'Paperback',
            self::Manuscript => 'Manuscript',
            self::Digital => 'Digital',
            self::Microfilm => 'Microfilm',
            self::Facsimile => 'Facsimile',
            self::FirstEdition => 'First Edition',
            self::RevisedEdition => 'Revised Edition',
            self::Ebook => 'E-book',
            self::Print => 'Print',
            self::Pdf => 'PDF',
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
