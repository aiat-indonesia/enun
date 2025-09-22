<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeoController extends Controller
{
    /**
     * Get places as GeoJSON points for mapping.
     */
    public function points(Request $request): JsonResponse
    {
        $query = Place::whereNotNull('lat')
            ->whereNotNull('lng')
            ->withCount('works');

        // Apply filters
        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        // Bounding box filter
        if ($request->has(['north', 'south', 'east', 'west'])) {
            $query->whereBetween('lat', [$request->get('south'), $request->get('north')])
                ->whereBetween('lng', [$request->get('west'), $request->get('east')]);
        }

        // Limit for performance
        $limit = min($request->get('limit', 1000), 5000);
        $places = $query->limit($limit)->get();

        $features = $places->map(function ($place) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [
                        (float) $place->lng,
                        (float) $place->lat,
                    ],
                ],
                'properties' => [
                    'id' => $place->id,
                    'name' => $place->name,
                    'type' => $place->type,
                    'works_count' => $place->works_count,
                    'description' => $place->description ?? null,
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
            'meta' => [
                'total_features' => $features->count(),
                'bounds' => $this->calculateBounds($places),
            ],
        ]);
    }

    /**
     * Calculate bounding box for the places.
     */
    private function calculateBounds($places): ?array
    {
        if ($places->isEmpty()) {
            return null;
        }

        $latitudes = $places->pluck('lat')->filter();
        $longitudes = $places->pluck('lng')->filter();

        if ($latitudes->isEmpty() || $longitudes->isEmpty()) {
            return null;
        }

        return [
            'north' => $latitudes->max(),
            'south' => $latitudes->min(),
            'east' => $longitudes->max(),
            'west' => $longitudes->min(),
        ];
    }
}
