<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('instance_id')
                    ->relationship('instance', 'id')
                    ->required(),
                TextInput::make('item_identifier'),
                TextInput::make('location'),
                TextInput::make('call_number'),
                TextInput::make('availability')
                    ->required()
                    ->default('available'),
                TextInput::make('metadata'),
            ]);
    }
}
