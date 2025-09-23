<?php

namespace App\Filament\Resources\Places\Pages;

use App\Filament\Resources\Places\PlaceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class CreatePlace extends CreateRecord
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        Log::info('CreatePlace mutateFormDataBeforeCreate called', ['data' => $data]);

        // Extract lat/lng from coordinates field
        if (isset($data['coordinates']) && is_array($data['coordinates'])) {
            if (isset($data['coordinates']['lat'], $data['coordinates']['lng'])) {
                $data['lat'] = $data['coordinates']['lat'];
                $data['lng'] = $data['coordinates']['lng'];
                Log::info('Coordinates extracted', [
                    'lat' => $data['lat'],
                    'lng' => $data['lng'],
                ]);
            }
            unset($data['coordinates']); // Remove coordinates field
        }

        Log::info('Final data for creation', ['data' => $data]);

        return $data;
    }
}
