<?php

namespace App\Filament\Resources\Works\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class WorkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Work Details')
                    ->tabs([
                        Tabs\Tab::make('Basic Information')
                            ->schema([
                                Section::make('Title & Description')
                                    ->schema([
                                        TextInput::make('title')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                                        TextInput::make('slug')
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                        Textarea::make('summary')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Textarea::make('description')
                                            ->rows(6)
                                            ->columnSpanFull(),
                                        TagsInput::make('languages')
                                            ->suggestions(['Arabic', 'English', 'Persian', 'Turkish', 'Urdu', 'Malay'])
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),

                                Section::make('Classification')
                                    ->schema([
                                        Select::make('type')
                                            ->required()
                                            ->options([
                                                'manuscript' => 'Manuscript',
                                                'book' => 'Book',
                                                'article' => 'Article',
                                                'compilation' => 'Compilation',
                                                'translation' => 'Translation',
                                                'commentary' => 'Commentary',
                                                'tafsir' => 'Tafsir',
                                                'other' => 'Other',
                                            ]),
                                        Select::make('status')
                                            ->required()
                                            ->default('draft')
                                            ->options([
                                                'draft' => 'Draft',
                                                'in_review' => 'In Review',
                                                'published' => 'Published',
                                                'archived' => 'Archived',
                                            ]),
                                        Select::make('primary_place_id')
                                            ->relationship('primaryPlace', 'name')
                                            ->searchable()
                                            ->preload(),
                                        TagsInput::make('subjects')
                                            ->suggestions(['Quranic Studies', 'Hadith', 'Fiqh', 'Theology', 'History', 'Literature'])
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3),
                            ]),

                        Tabs\Tab::make('Additional Data')
                            ->schema([
                                Section::make('Alternative Information')
                                    ->schema([
                                        Repeater::make('alternative_titles')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->required(),
                                                TextInput::make('language'),
                                                Select::make('type')
                                                    ->options([
                                                        'transliteration' => 'Transliteration',
                                                        'translation' => 'Translation',
                                                        'variant' => 'Variant',
                                                        'subtitle' => 'Subtitle',
                                                    ]),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(0)
                                            ->columnSpanFull(),

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
                                                        'internal' => 'Internal ID',
                                                        'other' => 'Other',
                                                    ]),
                                                TextInput::make('value')
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0),

                                        Repeater::make('external_links')
                                            ->schema([
                                                Select::make('type')
                                                    ->required()
                                                    ->options([
                                                        'website' => 'Website',
                                                        'digital_library' => 'Digital Library',
                                                        'catalog' => 'Catalog',
                                                        'archive' => 'Archive',
                                                        'bibliography' => 'Bibliography',
                                                        'other' => 'Other',
                                                    ]),
                                                TextInput::make('url')
                                                    ->required()
                                                    ->url()
                                                    ->placeholder('https://...'),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0),
                                    ]),

                                Section::make('Metadata')
                                    ->schema([
                                        KeyValue::make('metadata')
                                            ->keyLabel('Field')
                                            ->valueLabel('Value')
                                            ->helperText('Additional metadata fields'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Media & Assets')
                            ->schema([
                                Section::make('Images')
                                    ->schema([
                                        FileUpload::make('images')
                                            ->image()
                                            ->multiple()
                                            ->imageEditor()
                                            ->maxFiles(10)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->directory('work-images')
                                            ->disk('public')
                                            ->description('Upload cover images, thumbnails, or related photographs')
                                            ->downloadable(),
                                    ]),

                                Section::make('Manuscripts')
                                    ->schema([
                                        FileUpload::make('manuscripts')
                                            ->multiple()
                                            ->maxFiles(50)
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/tiff'])
                                            ->directory('work-manuscripts')
                                            ->disk('public')
                                            ->description('Upload manuscript pages, scans, or PDF documents')
                                            ->downloadable(),
                                    ]),

                                Section::make('Documents')
                                    ->schema([
                                        FileUpload::make('documents')
                                            ->multiple()
                                            ->maxFiles(20)
                                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'])
                                            ->directory('work-documents')
                                            ->disk('public')
                                            ->description('Upload related documents, transcriptions, or research notes')
                                            ->downloadable(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
