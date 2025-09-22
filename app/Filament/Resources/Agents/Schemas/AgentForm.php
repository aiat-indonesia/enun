<?php

namespace App\Filament\Resources\Agents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AgentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('type')
                    ->required()
                    ->default('person'),
                DatePicker::make('birth_date'),
                DatePicker::make('death_date'),
                Textarea::make('biography')
                    ->columnSpanFull(),
                TextInput::make('metadata'),
            ]);
    }
}
