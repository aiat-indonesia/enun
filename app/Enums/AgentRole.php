<?php

namespace App\Enums;

enum AgentRole: string
{
    case Author = 'author';
    case Editor = 'editor';
    case Translator = 'translator';
    case Compiler = 'compiler';
    case Commentator = 'commentator';
    case Publisher = 'publisher';
    case Illustrator = 'illustrator';
    case Contributor = 'contributor';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Author => 'Author',
            self::Editor => 'Editor',
            self::Translator => 'Translator',
            self::Compiler => 'Compiler',
            self::Commentator => 'Commentator',
            self::Publisher => 'Publisher',
            self::Illustrator => 'Illustrator',
            self::Contributor => 'Contributor',
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
