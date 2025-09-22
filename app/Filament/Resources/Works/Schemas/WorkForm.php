<?php

namespace App\Filament\Resources\Works\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;

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
                                            ->afterStateUpdated(fn(string $context, $state, callable $set) =>
                                            $context === 'create' ? $set('slug', str($state)->slug()) : null),
                                        TextInput::make('slug')
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                        TextInput::make('subtitle'),
                                        Textarea::make('summary')
                                            ->rows(4),
                                    ])->columns(2),

                                Section::make('Classification')
                                    ->schema([
                                        TagsInput::make('languages')
                                            ->suggestions(['id', 'ar', 'ms', 'jv', 'su', 'mad'])
                                            ->helperText('Language codes: id (Indonesian), ar (Arabic), ms (Malay), jv (Javanese), etc.'),
                                        Select::make('type')
                                            ->options([
                                                'manuscript' => 'Manuscript',
                                                'tafsir' => 'Tafsir',
                                                'book' => 'Book',
                                                'journal' => 'Journal',
                                                'article' => 'Article',
                                                'thesis' => 'Thesis',
                                            ]),
                                        Select::make('status')
                                            ->required()
                                            ->default('draft')
                                            ->options([
                                                'draft' => 'Draft',
                                                'review' => 'Under Review',
                                                'published' => 'Published',
                                            ]),
                                        Select::make('primary_place_id')
                                            ->relationship('primaryPlace', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Additional Data')
                            ->schema([
                                Section::make('Alternative Information')
                                    ->schema([
                                        TagsInput::make('alternative_titles')
                                            ->helperText('Alternative titles or transliterations'),
                                        Repeater::make('external_identifiers')
                                            ->schema([
                                                Select::make('type')
                                                    ->options([
                                                        'doi' => 'DOI',
                                                        'isbn' => 'ISBN',
                                                        'issn' => 'ISSN',
                                                        'urn' => 'URN',
                                                        'handle' => 'Handle',
                                                    ])
                                                    ->required(),
                                                TextInput::make('value')
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0),
                                        Repeater::make('seller_links')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->placeholder('e.g., Gramedia, Tokopedia'),
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
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
