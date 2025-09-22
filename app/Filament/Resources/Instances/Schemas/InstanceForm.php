<?php

namespace App\Filament\Resources\Instances\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
                                            ->preload(),
                                        TextInput::make('label')
                                            ->required()
                                            ->helperText('Specific label for this instance (e.g., "First Edition", "Manuscript A")'),
                                        Select::make('publisher_id')
                                            ->relationship('publisher', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Select::make('publication_place_id')
                                            ->relationship('publicationPlace', 'name')
                                            ->searchable()
                                            ->preload(),
                                        TextInput::make('publication_year')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(date('Y')),
                                        TextInput::make('format')
                                            ->placeholder('e.g., Hardcover, Paperback, Manuscript'),
                                    ])
                                    ->columns(2),

                                Section::make('Additional Data')
                                    ->schema([
                                        Textarea::make('identifiers')
                                            ->rows(3)
                                            ->helperText('JSON or structured identifiers for this instance'),
                                        Textarea::make('metadata')
                                            ->rows(4)
                                            ->helperText('Additional metadata specific to this instance'),
                                    ])
                                    ->columns(1),
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
                                            ->description('Upload cover images or front matter')
                                            ->downloadable(),
                                    ]),

                                Section::make('Preview Pages')
                                    ->schema([
                                        FileUpload::make('preview_pages')
                                            ->multiple()
                                            ->maxFiles(20)
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/tiff'])
                                            ->directory('instance-previews')
                                            ->disk('public')
                                            ->description('Upload sample pages or preview content')
                                            ->downloadable(),
                                    ]),

                                Section::make('Documents')
                                    ->schema([
                                        FileUpload::make('documents')
                                            ->multiple()
                                            ->maxFiles(15)
                                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'])
                                            ->directory('instance-documents')
                                            ->disk('public')
                                            ->description('Upload related documents, catalogs, or publication info')
                                            ->downloadable(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
