<?php

namespace App\Filament\Resources\Works\Pages;

use App\Filament\Resources\Works\WorkResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditWork extends EditRecord
{
    protected static string $resource = WorkResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];

        // Add workflow actions based on current status
        $workflowActions = $this->getWorkflowActions();

        return array_merge($workflowActions, $actions);
    }

    protected function getWorkflowActions(): array
    {
        $record = $this->getRecord();
        $actions = [];

        switch ($record->status) {
            case \App\Enums\WorkStatus::Draft:
                $actions[] = Action::make('submit_for_review')
                    ->label('Submit for Review')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Submit for Review')
                    ->modalDescription('Are you sure you want to submit this work for review?')
                    ->action(function () use ($record) {
                        $record->update(['status' => \App\Enums\WorkStatus::InReview]);

                        Notification::make()
                            ->title('Work submitted for review')
                            ->success()
                            ->send();
                    });
                break;

            case \App\Enums\WorkStatus::InReview:
                $actions[] = Action::make('approve')
                    ->label('Approve & Publish')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve and Publish')
                    ->modalDescription('Are you sure you want to approve and publish this work?')
                    ->action(function () use ($record) {
                        $record->update(['status' => \App\Enums\WorkStatus::Published]);

                        Notification::make()
                            ->title('Work approved and published')
                            ->success()
                            ->send();
                    });

                $actions[] = Action::make('reject')
                    ->label('Reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Work')
                    ->modalDescription('Are you sure you want to reject this work? It will be returned to draft status.')
                    ->action(function () use ($record) {
                        $record->update(['status' => \App\Enums\WorkStatus::Draft]);

                        Notification::make()
                            ->title('Work rejected and returned to draft')
                            ->warning()
                            ->send();
                    });
                break;

            case \App\Enums\WorkStatus::Published:
                $actions[] = Action::make('archive')
                    ->label('Archive')
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Archive Work')
                    ->modalDescription('Are you sure you want to archive this work?')
                    ->action(function () use ($record) {
                        $record->update(['status' => \App\Enums\WorkStatus::Archived]);

                        Notification::make()
                            ->title('Work archived')
                            ->warning()
                            ->send();
                    });
                break;

            case \App\Enums\WorkStatus::Archived:
                $actions[] = Action::make('restore_to_published')
                    ->label('Restore to Published')
                    ->icon(Heroicon::OutlinedArrowPathRoundedSquare)
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Restore Work')
                    ->modalDescription('Are you sure you want to restore this work to published status?')
                    ->action(function () use ($record) {
                        $record->update(['status' => \App\Enums\WorkStatus::Published]);

                        Notification::make()
                            ->title('Work restored to published')
                            ->success()
                            ->send();
                    });
                break;
        }

        return $actions;
    }
}
