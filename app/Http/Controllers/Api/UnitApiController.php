<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UnitApiController extends Controller
{
    /**
     * Display a listing of the units.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Unit::query();

        // Filter by parent_id
        if ($request->has('parent_id')) {
            if ($request->parent_id === 'null' || $request->parent_id === null) {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        // Search by code or name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        // Eager load relationships
        $with = ['parent', 'children'];
        if ($request->has('with')) {
            $with = array_merge($with, explode(',', $request->with));
        }
        $query->with($with);
        $query->withCount(['users', 'children']);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $units = $query->orderBy('code')->paginate($perPage);

        return UnitResource::collection($units);
    }

    /**
     * Display the specified unit.
     */
    public function show(Request $request, Unit $unit): UnitResource
    {
        // Eager load relationships
        $with = ['parent', 'children'];
        if ($request->has('with')) {
            $with = array_merge($with, explode(',', $request->with));
        }
        $unit->load($with);
        $unit->loadCount(['users', 'children']);

        return new UnitResource($unit);
    }
}
