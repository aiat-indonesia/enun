<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\WorkResource;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WorkController extends Controller
{
    /**
     * Display a listing of works.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Regular query builder for requests
        $query = Work::with(['author', 'place'])
            ->withCount('instances');

        $this->applyFilters($query, $request);

        $perPage = min($request->get('per_page', 15), 100);
        $works = $query->paginate($perPage);

        return WorkResource::collection($works);
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters($query, Request $request): void
    {
        // Apply filters
        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('place')) {
            $place = $request->get('place');
            if (is_numeric($place)) {
                $query->where('place_id', $place);
            } else {
                $query->whereHas('place', function ($q) use ($place) {
                    $q->where('name', 'like', '%' . $place . '%');
                });
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['title', 'created_at', 'updated_at', 'type'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Legacy search support (kept for backward compatibility)
        if ($request->has('search') && ! $request->has('q')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            });
        }
    }

    /**
     * Display the specified work.
     */
    public function show(Work $work): WorkResource
    {
        $work->load([
            'author',
            'place',
            'instances' => function ($query) {
                $query->with(['items', 'publisher']);
            },
        ]);

        return new WorkResource($work);
    }
}
