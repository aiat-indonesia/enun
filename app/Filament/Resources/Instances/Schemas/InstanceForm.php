<?php

namespace App\Filament\Resources\Instances\Schemas;

use App\Enums\InstanceFormat;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class InstanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Instance Details')
                    ->tabs([
                        Tabs\Tab::make('Basic Information')
                            ->schema([
                                Section::make('Work & Publication')
                                    ->schema([
                                        Select::make('work_id')
                                            ->relationship('work', 'title')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Select the work this instance belongs to')
                                            ->placeholder('Choose a work...'),
                                        TextInput::make('label')
                                            ->required()
                                            ->helperText('Specific label for this instance (e.g., "First Edition", "Manuscript A")'),
                                        Select::make('publisher_id')
                                            ->relationship('publisher', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Select the publisher or publishing organization')
                                            ->placeholder('Choose a publisher...'),
                                        Select::make('publication_place_id')
                                            ->relationship('publicationPlace', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Select the place where this instance was published')
                                            ->placeholder('Choose a place...'),
                                        TextInput::make('publication_year')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(date('Y')),
                                        Select::make('format')
                                            ->options(InstanceFormat::options())
                                            ->placeholder('Select format'),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Additional Data')
                            ->schema([
                                Section::make('Identifiers')
                                    ->schema([
                                        Repeater::make('identifiers')
                                            ->schema([
                                                Select::make('type')
                                                    ->required()
                                                    ->options([
                                                        'isbn' => 'ISBN',
                                                        'issn' => 'ISSN',
                                                        'doi' => 'DOI',
                                                        'oclc' => 'OCLC',
                                                        'lccn' => 'LCCN',
                                                        'barcode' => 'Barcode',
                                                        'internal' => 'Internal ID',
                                                        'other' => 'Other',
                                                    ])
                                                    ->placeholder('Select identifier type'),
                                                TextInput::make('value')
                                                    ->required()
                                                    ->placeholder('Enter identifier value'),
                                                TextInput::make('note')
                                                    ->placeholder('Optional note about this identifier'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(0)
                                            ->columnSpanFull()
                                            ->helperText('Add unique identifiers for this specific instance'),
                                    ]),

                                Section::make('Metadata')
                                    ->schema([
                                        KeyValue::make('metadata')
                                            ->keyLabel('Field')
                                            ->valueLabel('Value')
                                            ->helperText('Additional metadata specific to this instance (pages, dimensions, etc.)')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Media & Files')
                            ->schema([
                                Section::make('Cover Images')
                                    ->schema([
                                        FileUpload::make('cover_images')
                                            ->image()
                                            ->multiple()
                                            ->imageEditor()
                                            ->maxFiles(5)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->directory('instance-covers')
                                            ->disk('public')
                                            ->downloadable()
                                            ->helperText('Upload cover images for this instance'),
                                    ]),

                                Section::make('Preview Pages')
                                    ->schema([
                                        FileUpload::make('preview_pages')
                                            ->multiple()
                                            ->maxFiles(20)
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/tiff'])
                                            ->directory('instance-previews')
                                            ->disk('public')
                                            ->downloadable()
                                            ->helperText('Upload preview pages or sample content'),
                                    ]),

                                Section::make('Documents')
                                    ->schema([
                                        FileUpload::make('documents')
                                            ->multiple()
                                            ->maxFiles(15)
                                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'])
                                            ->directory('instance-documents')
                                            ->disk('public')
                                            ->downloadable()
                                            ->helperText('Upload related documents or transcriptions'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
