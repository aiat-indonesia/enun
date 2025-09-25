<?php

namespace App\Filament\Resources\Works\Schemas;

use App\Enums\InstanceFormat;
use App\Enums\WorkStatus;
use App\Enums\WorkType;
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
                                    ])
                                    ->columns(2),

                                Section::make('Classification')
                                    ->schema([
                                        Select::make('type')
                                            ->required()
                                            ->options(WorkType::options()),
                                        Select::make('status')
                                            ->required()
                                            ->default('draft')
                                            ->options(WorkStatus::options()),
                                        Select::make('visibility')
                                            ->required()
                                            ->default('private')
                                            ->options([
                                                'private' => 'Private',
                                                'public' => 'Public',
                                                'restricted' => 'Restricted',
                                            ]),
                                        Select::make('author_id')
                                            ->relationship('author', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Choose an author...')
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->label('Author name'),
                                                TextInput::make('metadata')
                                                    ->label('Metadata (JSON)')
                                                    ->placeholder('{"birth_year":"..."}'),
                                            ]),
                                        Select::make('place_id')
                                            ->relationship('place', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Select the primary geographic location associated with this work')
                                            ->placeholder('Choose a place...')
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->label('Place name'),
                                                TextInput::make('metadata')
                                                    ->label('Metadata (JSON)')
                                                    ->placeholder('{"region":"..."}'),
                                            ]),
                                        TagsInput::make('subjects')
                                            ->suggestions(['Quranic Studies', 'Hadith', 'Fiqh', 'Theology', 'History', 'Literature'])
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Additional Data')
                            ->schema([
                                Section::make('Contributors & Timeline')
                                    ->schema([
                                        Repeater::make('contributors')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->label('Contributor Name'),
                                                Select::make('role')
                                                    ->options([
                                                        'translator' => 'Translator',
                                                        'editor' => 'Editor',
                                                        'commentator' => 'Commentator',
                                                        'compiler' => 'Compiler',
                                                        'illustrator' => 'Illustrator',
                                                        'other' => 'Other',
                                                    ])
                                                    ->required(),
                                                TextInput::make('notes')
                                                    ->label('Additional Notes'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(0)
                                            ->columnSpanFull(),

                                        Repeater::make('creation_year')
                                            ->schema([
                                                TextInput::make('year')
                                                    ->numeric()
                                                    ->label('Year')
                                                    ->placeholder('e.g., 1450'),
                                                Select::make('type')
                                                    ->options([
                                                        'exact' => 'Exact Year',
                                                        'approximate' => 'Approximate',
                                                        'range_start' => 'Range Start',
                                                        'range_end' => 'Range End',
                                                    ])
                                                    ->default('exact'),
                                                TextInput::make('notes')
                                                    ->label('Notes')
                                                    ->placeholder('e.g., based on colophon'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(0)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),

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
                                            ->helperText('Upload cover images, thumbnails, or related photographs')
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
                                            ->helperText('Upload manuscript pages, scans, or PDF documents')
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
                                            ->helperText('Upload related documents, transcriptions, or research notes')
                                            ->downloadable(),
                                    ]),
                            ]),
                        Tabs\Tab::make('Relations')
                            ->schema([
                                Section::make('Instances')
                                    ->schema([
                                        Repeater::make('instances')
                                            ->relationship('instances')
                                            ->schema([
                                                TextInput::make('label')
                                                    ->required()
                                                    ->placeholder('e.g. 1st edition / Manuscript A'),
                                                Select::make('publisher_id')
                                                    ->relationship('publisher', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->placeholder('Select publisher')
                                                    ->createOptionForm([
                                                        TextInput::make('name')
                                                            ->required()
                                                            ->label('Publisher name'),
                                                        TextInput::make('metadata')
                                                            ->label('Metadata (JSON)')
                                                            ->placeholder('{"type":"institution"}'),
                                                    ]),
                                                Select::make('publication_place_id')
                                                    ->relationship('publicationPlace', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->placeholder('Select publication place')
                                                    ->createOptionForm([
                                                        TextInput::make('name')
                                                            ->required()
                                                            ->label('Place name'),
                                                    ]),
                                                TextInput::make('publication_year')
                                                    ->placeholder('e.g. 1892'),
                                                Select::make('format')
                                                    ->options(InstanceFormat::options())
                                                    ->placeholder('Select format'),
                                                KeyValue::make('identifiers')
                                                    ->keyLabel('Type')
                                                    ->valueLabel('Value')
                                                    ->helperText('ISBN, DOI, shelfmark, etc.'),
                                            ])
                                            ->createItemButtonLabel('Add instance')
                                            ->defaultItems(0)
                                            ->columns(3)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Agents')
                                    ->schema([
                                        Repeater::make('agents')
                                            ->schema([
                                                Select::make('agent_id')
                                                    ->label('Agent')
                                                    ->options(fn () => \App\Models\Agent::pluck('name', 'id'))
                                                    ->searchable()
                                                    ->preload()
                                                    ->createOptionForm([
                                                        TextInput::make('name')
                                                            ->required()
                                                            ->label('Agent name'),
                                                        TextInput::make('type')
                                                            ->label('Type')
                                                            ->placeholder('Person / Organization'),
                                                    ]),
                                                Select::make('role')
                                                    ->label('Role')
                                                    ->required()
                                                    ->options([
                                                        'author' => 'Author',
                                                        'editor' => 'Editor',
                                                        'translator' => 'Translator',
                                                        'publisher' => 'Publisher',
                                                        'contributor' => 'Contributor',
                                                    ])
                                                    ->placeholder('Role for this agent'),
                                            ])
                                            ->createItemButtonLabel('Add agent')
                                            ->defaultItems(0)
                                            ->columns(2)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Assets (Quick)')
                                    ->schema([
                                        Repeater::make('assets_quick')
                                            ->schema([
                                                FileUpload::make('file')
                                                    ->directory('work-assets')
                                                    ->disk('public')
                                                    ->preserveFilenames()
                                                    ->helperText('Upload images, PDFs, or documents and they will be attached as assets'),
                                                TextInput::make('label')
                                                    ->placeholder('Optional label for this asset'),
                                                TextInput::make('metadata_json')
                                                    ->label('Metadata (JSON)')
                                                    ->placeholder('{"source":"archive"}'),
                                            ])
                                            ->createItemButtonLabel('Add asset')
                                            ->defaultItems(0)
                                            ->columns(1)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Subjects / Tags')
                                    ->schema([
                                        TagsInput::make('subjects')
                                            ->suggestions(['Quranic Studies', 'Hadith', 'Fiqh', 'Theology', 'History', 'Literature'])
                                            ->helperText('Add or select subjects (tags) related to this work')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
