<?php

namespace App\Filament\Resources\Places\Pages;

use App\Filament\Resources\Places\PlaceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class EditPlace extends EditRecord
{
    protected static string $resource = PlaceResource::class;

    #[On('coordinates-updated')]
    public function updateCoordinates($lat, $lng)
    {
        Log::info('Coordinates updated via attribute event', ['lat' => $lat, 'lng' => $lng]);

        // Update form data directly
        $this->data['lat'] = (string) $lat;
        $this->data['lng'] = (string) $lng;

        Log::info('Form data updated', ['lat' => $this->data['lat'], 'lng' => $this->data['lng']]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        Log::info('EditPlace mutateFormDataBeforeFill called', ['data' => $data]);

        // Set coordinates field from lat/lng for the map picker
        if (isset($data['lat'], $data['lng'])) {
            $data['coordinates'] = [
                'lat' => (float) $data['lat'],
                'lng' => (float) $data['lng'],
            ];
            Log::info('Coordinates set for MapPicker', ['coordinates' => $data['coordinates']]);
        }

        Log::info('Final data for form fill', ['data' => $data]);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        Log::info('EditPlace mutateFormDataBeforeSave called', ['data' => $data]);

        // Extract lat/lng from coordinates field
        if (isset($data['coordinates']) && is_array($data['coordinates'])) {
            if (isset($data['coordinates']['lat'], $data['coordinates']['lng'])) {
                $data['lat'] = $data['coordinates']['lat'];
                $data['lng'] = $data['coordinates']['lng'];
                Log::info('Coordinates extracted for save', [
                    'lat' => $data['lat'],
                    'lng' => $data['lng'],
                ]);
            }
            unset($data['coordinates']); // Remove coordinates field
        }

        Log::info('Final data for save', ['data' => $data]);

        return $data;
    }
}
