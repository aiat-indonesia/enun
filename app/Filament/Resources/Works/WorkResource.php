<?php

namespace App\Filament\Resources\Works;

use App\Filament\Resources\Works\Pages\CreateWork;
use App\Filament\Resources\Works\Pages\EditWork;
use App\Filament\Resources\Works\Pages\ListWorks;
use App\Filament\Resources\Works\Schemas\WorkForm;
use App\Filament\Resources\Works\Tables\WorksTable;
use App\Models\Work;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkResource extends Resource
{
    protected static ?string $model = Work::class;

    protected static ?string $modelPolicy = \App\Policies\WorkPolicy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    protected static int $globalSearchResultsLimit = 20;

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'subtitle', 'summary', 'slug', 'languages', 'primaryPlace.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Work $record */
        return [
            'Type' => $record->type,
            'Status' => $record->status,
            'Primary Place' => $record->primaryPlace?->name,
            'Languages' => implode(', ', $record->languages ?? []),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['primaryPlace']);
    }

    public static function form(Schema $schema): Schema
    {
        return WorkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InstancesRelationManager::class,
            RelationManagers\AgentsRelationManager::class,
            RelationManagers\AssetsRelationManager::class,
            RelationManagers\SubjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorks::route('/'),
            'create' => CreateWork::route('/create'),
            'edit' => EditWork::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
