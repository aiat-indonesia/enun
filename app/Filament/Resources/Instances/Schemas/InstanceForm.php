<?php

namespace App\Filament\Resources\Instances\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InstanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('work_id')
                    ->relationship('work', 'title')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                Select::make('publisher_id')
                    ->relationship('publisher', 'name'),
                Select::make('publication_place_id')
                    ->relationship('publicationPlace', 'name'),
                TextInput::make('publication_year'),
                TextInput::make('format'),
                TextInput::make('identifiers'),
                TextInput::make('metadata'),
            ]);
    }
}
