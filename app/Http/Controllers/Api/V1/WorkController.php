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
        $query = Work::with(['subjects', 'agents', 'primaryPlace'])
            ->withCount('instances');

        // Apply filters
        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->has('language')) {
            $query->whereJsonContains('languages', $request->get('language'));
        }

        if ($request->has('subject')) {
            $query->whereHas('subjects', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->get('subject').'%');
            });
        }

        if ($request->has('place')) {
            $query->whereHas('primaryPlace', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->get('place').'%');
            });
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 100); // Max 100 items per page
        $works = $query->paginate($perPage);

        return WorkResource::collection($works);
    }

    /**
     * Display the specified work.
     */
    public function show(Work $work): WorkResource
    {
        $work->load([
            'subjects',
            'agents.pivot',
            'primaryPlace',
            'instances' => function ($query) {
                $query->with(['items', 'publisher']);
            },
        ]);

        return new WorkResource($work);
    }
}
