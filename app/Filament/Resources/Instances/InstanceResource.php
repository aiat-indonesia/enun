<?php

namespace App\Filament\Resources\Instances;

use App\Filament\Resources\Instances\Pages\CreateInstance;
use App\Filament\Resources\Instances\Pages\EditInstance;
use App\Filament\Resources\Instances\Pages\ListInstances;
use App\Filament\Resources\Instances\RelationManagers;
use App\Filament\Resources\Instances\Schemas\InstanceForm;
use App\Filament\Resources\Instances\Tables\InstancesTable;
use App\Models\Instance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InstanceResource extends Resource
{
    protected static ?string $model = Instance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return InstanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InstancesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInstances::route('/'),
            'create' => CreateInstance::route('/create'),
            'edit' => EditInstance::route('/{record}/edit'),
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
