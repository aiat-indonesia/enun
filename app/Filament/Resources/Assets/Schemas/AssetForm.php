<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('assetable_type')
                    ->required(),
                TextInput::make('assetable_id')
                    ->required()
                    ->numeric(),
                TextInput::make('disk')
                    ->required()
                    ->default('local'),
                TextInput::make('path')
                    ->required(),
                TextInput::make('filename')
                    ->required(),
                TextInput::make('mime_type'),
                TextInput::make('size')
                    ->numeric(),
                Textarea::make('extracted_text')
                    ->columnSpanFull(),
                TextInput::make('metadata'),
            ]);
    }
}
