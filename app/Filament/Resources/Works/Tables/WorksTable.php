<?php

namespace App\Filament\Resources\Works\Tables;

use App\Enums\WorkStatus;
use App\Enums\WorkType;
use App\Models\Work;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class WorksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchUsing(function (Builder $query, string $search): Builder {
                // Use Laravel Scout for search if available
                if (method_exists(Work::class, 'search') && ! empty($search)) {
                    $searchResults = Work::search($search)->get();
                    $searchIds = $searchResults->pluck('id')->toArray();

                    if (! empty($searchIds)) {
                        return $query->whereIn('id', $searchIds);
                    }
                }

                // Fallback to database search
                return $query->where(function (Builder $query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('subtitle', 'like', "%{$search}%")
                        ->orWhere('summary', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('languages', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('place', function (Builder $query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    }),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TagsColumn::make('languages')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->colors([
                        'primary' => 'manuscript',
                        'success' => 'book',
                        'warning' => 'journal',
                        'danger' => 'article',
                        'gray' => 'tafsir',
                    ])
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'in_review',
                        'success' => 'published',
                        'danger' => 'archived',
                    ])
                    ->searchable(),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('place.name')
                    ->label('Place')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('visibility')
                    ->badge()
                    ->colors([
                        'success' => 'public',
                        'warning' => 'restricted',
                        'gray' => 'private',
                    ])
                    ->toggleable(),
                TextColumn::make('instances_count')
                    ->label('Instances')
                    ->counts('instances')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),

                // Advanced QueryBuilder filters
                QueryBuilder::make()
                    ->constraints([
                        TextConstraint::make('title')
                            ->label('Title'),
                        TextConstraint::make('summary')
                            ->label('Summary'),
                        TextConstraint::make('slug')
                            ->label('Slug'),
                        SelectConstraint::make('type')
                            ->label('Type')
                            ->options(WorkType::options()),
                        SelectConstraint::make('status')
                            ->label('Status')
                            ->options(WorkStatus::options()),
                        SelectConstraint::make('visibility')
                            ->label('Visibility')
                            ->options([
                                'private' => 'Private',
                                'public' => 'Public',
                                'restricted' => 'Restricted',
                            ]),
                        RelationshipConstraint::make('author')
                            ->label('Author')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                            ),
                        RelationshipConstraint::make('place')
                            ->label('Place')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                            ),
                        DateConstraint::make('created_at')
                            ->label('Created Date'),
                        DateConstraint::make('updated_at')
                            ->label('Updated Date'),
                    ]),

                // Legacy simple filters for backward compatibility
                SelectFilter::make('type')
                    ->options(WorkType::options()),
                SelectFilter::make('status')
                    ->options(WorkStatus::options()),
                SelectFilter::make('primary_place_id')
                    ->relationship('place', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Workflow Actions
                    BulkAction::make('submit_for_review')
                        ->label('Submit for Review')
                        ->icon(Heroicon::OutlinedEye)
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Submit Selected Works for Review')
                        ->modalDescription('Are you sure you want to submit the selected works for review?')
                        ->action(function (Collection $records) {
                            $records->where('status', 'draft')->each(fn ($record) => $record->update(['status' => 'in_review']));

                            Notification::make()
                                ->title('Works submitted for review')
                                ->success()
                                ->send();
                        })
                        ->visible(fn () => Auth::check()),

                    BulkAction::make('approve')
                        ->label('Approve & Publish')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve & Publish Selected Works')
                        ->modalDescription('Are you sure you want to approve and publish the selected works?')
                        ->action(function (Collection $records) {
                            $records->where('status', 'in_review')->each(fn ($record) => $record->update(['status' => 'published']));

                            Notification::make()
                                ->title('Works approved and published')
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('reject')
                        ->label('Reject to Draft')
                        ->icon(Heroicon::OutlinedXCircle)
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Reject Selected Works')
                        ->modalDescription('Are you sure you want to reject the selected works and return them to draft?')
                        ->action(function (Collection $records) {
                            $records->where('status', 'in_review')->each(fn ($record) => $record->update(['status' => 'draft']));

                            Notification::make()
                                ->title('Works rejected and returned to draft')
                                ->warning()
                                ->send();
                        }),

                    BulkAction::make('archive')
                        ->label('Archive')
                        ->icon(Heroicon::OutlinedArchiveBox)
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Selected Works')
                        ->modalDescription('Are you sure you want to archive the selected works?')
                        ->action(function (Collection $records) {
                            $records->where('status', 'published')->each(fn ($record) => $record->update(['status' => 'archived']));

                            Notification::make()
                                ->title('Works archived')
                                ->warning()
                                ->send();
                        }),

                    // Standard Actions
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
