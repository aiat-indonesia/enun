<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlaceController extends Controller
{
    /**
     * Display a listing of places.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Place::withCount('works');

        // Apply filters
        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Bounding box filter for map
        if ($request->has(['north', 'south', 'east', 'west'])) {
            $query->whereBetween('lat', [$request->get('south'), $request->get('north')])
                ->whereBetween('lng', [$request->get('west'), $request->get('east')]);
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $places = $query->paginate($perPage);

        return PlaceResource::collection($places);
    }

    /**
     * Display the specified place.
     */
    public function show(Place $place): PlaceResource
    {
        $place->loadCount('works');

        return new PlaceResource($place);
    }
}
