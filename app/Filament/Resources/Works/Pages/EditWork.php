<?php

namespace App\Filament\Resources\Works\Pages;

use App\Filament\Resources\Works\WorkResource;
use App\Models\Asset;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class EditWork extends EditRecord
{
    protected static string $resource = WorkResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load existing agents with pivot data for the repeater
        $record = $this->getRecord();
        $agents = $record->agents()->withPivot('role')->get();

        $data['agents'] = $agents->map(function ($agent) {
            return [
                'agent_id' => $agent->id,
                'role' => $agent->pivot->role,
            ];
        })->toArray();

        // Load existing media files for display (using Spatie MediaLibrary)
        $media = $record->getMedia() ?? collect();
        $data['assets_quick'] = $media->map(function ($mediaItem) {
            return [
                'file' => $mediaItem->getPath(),
                'label' => $mediaItem->name,
                'metadata_json' => json_encode($mediaItem->custom_properties),
            ];
        })->toArray();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Extract repeater data before updating the record
        $agentsData = $data['agents'] ?? [];
        $assetsQuickData = $data['assets_quick'] ?? [];

        // Remove repeater data from main data to prevent validation issues
        unset($data['agents'], $data['assets_quick']);

        // Update the Work record
        $record->update($data);

        // Handle agents pivot data
        $this->handleAgentsPivot($record, $agentsData);

        // Handle assets creation (only create new ones)
        $this->handleAssetsCreation($record, $assetsQuickData);

        return $record;
    }

    protected function handleAgentsPivot($record, array $agentsData): void
    {
        if (empty($agentsData)) {
            // If no agents data, detach all
            $record->agents()->detach();

            return;
        }

        $pivotData = [];
        foreach ($agentsData as $agentItem) {
            if (isset($agentItem['agent_id']) && isset($agentItem['role'])) {
                $pivotData[$agentItem['agent_id']] = ['role' => $agentItem['role']];
            }
        }

        $record->agents()->sync($pivotData);
    }

    protected function handleAssetsCreation($record, array $assetsQuickData): void
    {
        foreach ($assetsQuickData as $assetItem) {
            if (isset($assetItem['file']) && $assetItem['file']) {
                $filePath = $assetItem['file'];
                $disk = 'public';

                // Check if this asset already exists to avoid duplicates
                $existingAsset = Asset::where('assetable_type', get_class($record))
                    ->where('assetable_id', $record->id)
                    ->where('path', $filePath)
                    ->first();

                if (! $existingAsset) {
                    // Get file info from storage
                    $fullPath = storage_path("app/{$disk}/{$filePath}");
                    if (file_exists($fullPath)) {
                        $filename = basename($filePath);
                        $mimeType = mime_content_type($fullPath);
                        $size = filesize($fullPath);

                        Asset::create([
                            'assetable_type' => get_class($record),
                            'assetable_id' => $record->id,
                            'disk' => $disk,
                            'path' => $filePath,
                            'filename' => $filename,
                            'mime_type' => $mimeType,
                            'size' => $size,
                            'metadata' => ! empty($assetItem['metadata_json']) ?
                                json_decode($assetItem['metadata_json'], true) : null,
                        ]);
                    }
                }
            }
        }
    }

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
