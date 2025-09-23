<?php

namespace App\Filament\Components;

use Closure;
use Filament\Forms\Components\Field;
use JsonException;

class MapPicker extends Field
{
    protected string $view = 'filament.components.map-picker';

    protected string|Closure $height = '400px';

    protected array|Closure|null $defaultLocation = [-6.2088, 106.8456]; // Jakarta coordinates

    protected int|Closure $defaultZoom = 13;

    protected bool|Closure $draggable = true;

    protected bool|Closure $clickable = true;

    protected string|Closure|null $myLocationButtonLabel = 'My Location';

    protected string|Closure $tileProvider = 'openstreetmap';

    protected array|Closure $customTiles = [];

    protected string|Closure $markerIconPath = '';

    protected string|Closure $markerShadowPath = '';

    protected bool $showTileControl = true;

    private int $precision = 8;

    private array $mapConfig = [
        'draggable' => true,
        'clickable' => true,
        'defaultLocation' => [
            'lat' => -6.2088,
            'lng' => 106.8456,
        ],
        'statePath' => '',
        'defaultZoom' => 13,
        'myLocationButtonLabel' => '',
        'tileProvider' => 'openstreetmap',
        'customTiles' => [],
        'customMarker' => null,
        'markerIconPath' => '',
        'markerShadowPath' => '',
        'showTileControl' => false,
    ];

    public function height(string|Closure $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getHeight(): string
    {
        return $this->evaluate($this->height);
    }

    public function defaultLocation(array|Closure|null $location): static
    {
        $this->defaultLocation = $location;

        return $this;
    }

    public function getDefaultLocation(): array
    {
        return $this->evaluate($this->defaultLocation) ?? [-6.2088, 106.8456];
    }

    public function defaultZoom(int|Closure $zoom): static
    {
        $this->defaultZoom = $zoom;

        return $this;
    }

    public function getDefaultZoom(): int
    {
        return $this->evaluate($this->defaultZoom);
    }

    public function draggable(bool|Closure $draggable = true): static
    {
        $this->draggable = $draggable;

        return $this;
    }

    public function getDraggable(): bool
    {
        return $this->evaluate($this->draggable);
    }

    public function clickable(bool|Closure $clickable = true): static
    {
        $this->clickable = $clickable;

        return $this;
    }

    public function getClickable(): bool
    {
        return $this->evaluate($this->clickable);
    }

    public function myLocationButtonLabel(string|Closure|null $label): static
    {
        $this->myLocationButtonLabel = $label;

        return $this;
    }

    public function getMyLocationButtonLabel(): ?string
    {
        return $this->evaluate($this->myLocationButtonLabel);
    }

    public function tileProvider(string|Closure $provider): static
    {
        $this->tileProvider = $provider;

        return $this;
    }

    public function getTileProvider(): string
    {
        return $this->evaluate($this->tileProvider);
    }

    public function customTiles(array|Closure $tiles): static
    {
        $this->customTiles = $tiles;

        return $this;
    }

    public function getCustomTiles(): array
    {
        return $this->evaluate($this->customTiles);
    }

    public function hideTileControl(): static
    {
        $this->showTileControl = false;

        return $this;
    }

    public function getShowTileControl(): bool
    {
        return $this->showTileControl;
    }

    public function markerIconPath(string|Closure $path): static
    {
        $this->markerIconPath = $path;

        return $this;
    }

    public function getMarkerIconPath(): string
    {
        return $this->evaluate($this->markerIconPath) ?: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png';
    }

    public function markerShadowPath(string|Closure $path): static
    {
        $this->markerShadowPath = $path;

        return $this;
    }

    public function getMarkerShadowPath(): string
    {
        return $this->evaluate($this->markerShadowPath) ?: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png';
    }

    /**
     * Get the map configuration as JSON string
     */
    public function getMapConfig(): string
    {
        $config = [
            'draggable' => $this->getDraggable(),
            'clickable' => $this->getClickable(),
            'defaultLocation' => [
                'lat' => $this->getDefaultLocation()[0],
                'lng' => $this->getDefaultLocation()[1],
            ],
            'statePath' => $this->getStatePath(),
            'defaultZoom' => $this->getDefaultZoom(),
            'myLocationButtonLabel' => $this->getMyLocationButtonLabel(),
            'tileProvider' => $this->getTileProvider(),
            'customTiles' => $this->getCustomTiles(),
            'markerIconPath' => $this->getMarkerIconPath(),
            'markerShadowPath' => $this->getMarkerShadowPath(),
            'showTileControl' => $this->getShowTileControl(),
        ];

        try {
            return json_encode($config, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new \RuntimeException('Failed to encode map configuration to JSON: '.$e->getMessage());
        }
    }

    /**
     * Get the current state properly formatted
     */
    public function getState(): mixed
    {
        $state = parent::getState();

        // If state is already an array with lat/lng, return it
        if (is_array($state) && isset($state['lat'], $state['lng'])) {
            return $state;
        }

        // If state is a string, try to decode it
        if (is_string($state)) {
            try {
                $decoded = json_decode($state, true, flags: JSON_THROW_ON_ERROR);
                if (is_array($decoded) && isset($decoded['lat'], $decoded['lng'])) {
                    return $decoded;
                }
            } catch (JsonException) {
                // Fall through to default
            }
        }

        // Return default location if no valid state
        return [
            'lat' => $this->getDefaultLocation()[0],
            'lng' => $this->getDefaultLocation()[1],
        ];
    }

    /**
     * Mutate the data before saving
     */
    public function mutateDehydratedState(mixed $state): mixed
    {
        // Ensure state is properly formatted for storage
        if (is_array($state) && isset($state['lat'], $state['lng'])) {
            return [
                'lat' => (float) $state['lat'],
                'lng' => (float) $state['lng'],
            ];
        }

        return $state;
    }
}
