<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Item Details')
                    ->tabs([
                        Tabs\Tab::make('Basic Information')
                            ->schema([
                                Section::make('Instance & Identification')
                                    ->schema([
                                        Select::make('instance_id')
                                            ->relationship('instance', 'label')
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                        TextInput::make('item_identifier')
                                            ->required()
                                            ->helperText('Unique identifier for this specific item'),
                                        TextInput::make('call_number')
                                            ->helperText('Library or archive call number'),
                                        Select::make('availability')
                                            ->required()
                                            ->default('available')
                                            ->options([
                                                'available' => 'Available',
                                                'checked_out' => 'Checked Out',
                                                'restricted' => 'Restricted Access',
                                                'conservation' => 'In Conservation',
                                                'missing' => 'Missing',
                                                'damaged' => 'Damaged',
                                            ]),
                                    ])
                                    ->columns(2),

                                Section::make('Location & Condition')
                                    ->schema([
                                        TextInput::make('location')
                                            ->helperText('Physical location (building, room, shelf)'),
                                        Textarea::make('metadata')
                                            ->rows(4)
                                            ->helperText('Additional metadata, condition notes, or provenance information'),
                                    ])
                                    ->columns(1),
                            ]),

                        Tabs\Tab::make('Media & Documentation')
                            ->schema([
                                Section::make('Item Photos')
                                    ->schema([
                                        FileUpload::make('photos')
                                            ->image()
                                            ->multiple()
                                            ->imageEditor()
                                            ->maxFiles(10)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->directory('item-photos')
                                            ->disk('public')
                                            ->description('Upload photos of the physical item')
                                            ->downloadable(),
                                    ]),

                                Section::make('Digital Scans')
                                    ->schema([
                                        FileUpload::make('scans')
                                            ->multiple()
                                            ->maxFiles(100)
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/tiff'])
                                            ->directory('item-scans')
                                            ->disk('public')
                                            ->description('Upload high-resolution scans of pages or content')
                                            ->downloadable(),
                                    ]),

                                Section::make('Condition Reports')
                                    ->schema([
                                        FileUpload::make('condition_reports')
                                            ->multiple()
                                            ->maxFiles(10)
                                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'])
                                            ->directory('item-reports')
                                            ->disk('public')
                                            ->description('Upload condition reports, conservation notes, or appraisal documents')
                                            ->downloadable(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
