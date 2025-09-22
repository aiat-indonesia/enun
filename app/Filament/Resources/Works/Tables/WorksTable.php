<?php

namespace App\Filament\Resources\Works\Tables;

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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class WorksTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                TextColumn::make('primaryPlace.name')
                    ->label('Primary Place')
                    ->searchable()
                    ->sortable(),
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
                SelectFilter::make('type')
                    ->options([
                        'manuscript' => 'Manuscript',
                        'tafsir' => 'Tafsir',
                        'book' => 'Book',
                        'journal' => 'Journal',
                        'article' => 'Article',
                        'thesis' => 'Thesis',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'in_review' => 'In Review',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),
                SelectFilter::make('primary_place_id')
                    ->relationship('primaryPlace', 'name')
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
