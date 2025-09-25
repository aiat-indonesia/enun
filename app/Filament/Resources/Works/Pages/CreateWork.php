<?php

namespace App\Filament\Resources\Works\Pages;

use App\Filament\Resources\Works\WorkResource;
use App\Models\Asset;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateWork extends CreateRecord
{
    protected static string $resource = WorkResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Extract repeater data before creating the record
        $agentsData = $data['agents'] ?? [];
        $assetsQuickData = $data['assets_quick'] ?? [];

        // Remove repeater data from main data to prevent validation issues
        unset($data['agents'], $data['assets_quick']);

        // Create the Work record
        $record = static::getModel()::create($data);

        // Handle agents pivot data
        $this->handleAgentsPivot($record, $agentsData);

        // Handle assets creation
        $this->handleAssetsCreation($record, $assetsQuickData);

        return $record;
    }

    protected function handleAgentsPivot($record, array $agentsData): void
    {
        if (empty($agentsData)) {
            return;
        }

        $pivotData = [];
        foreach ($agentsData as $agentItem) {
            if (isset($agentItem['agent_id']) && isset($agentItem['role'])) {
                $pivotData[$agentItem['agent_id']] = ['role' => $agentItem['role']];
            }
        }

        if (! empty($pivotData)) {
            $record->agents()->sync($pivotData);
        }
    }

    protected function handleAssetsCreation($record, array $assetsQuickData): void
    {
        foreach ($assetsQuickData as $assetItem) {
            if (isset($assetItem['file']) && $assetItem['file']) {
                $filePath = $assetItem['file'];
                $disk = 'public';

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
