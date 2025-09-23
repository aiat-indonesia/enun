<?php

namespace App\Filament\Resources\Places\Schemas;

use App\Filament\Components\MapPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PlaceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Select::make('type')
                    ->required()
                    ->options([
                        'province' => 'Province',
                        'regency' => 'Regency',
                        'district' => 'District',
                        'village' => 'Village',
                        'city' => 'City',
                        'other' => 'Other',
                    ])
                    ->default('city'),

                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),

                // MapPicker::make('coordinates')
                //     ->label('Location')
                //     ->defaultLocation([-6.2088, 106.8456]) // Jakarta coordinates
                //     ->defaultZoom(5)
                //     ->height('400px')
                //     ->tileProvider('openstreetmap')
                //     ->dehydrated(false), // Don't save coordinates field directly

                // Hidden fields for actual storage
                TextInput::make('lat')
                    ->label('Latitude')
                    ->numeric()
                    ->step(0.00000001)
                    ->default(null),

                TextInput::make('lng')
                    ->label('Longitude')
                    ->numeric()
                    ->step(0.00000001)
                    ->default(null),

                Textarea::make('metadata')
                    ->label('Additional Metadata')
                    ->helperText('Optional: Additional information as JSON')
                    ->rows(3),
            ]);
    }
}
