<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PositionResource;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PositionApiController extends Controller
{
    /**
     * Display a listing of the positions.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Position::query();

        // Filter by echelon_id
        if ($request->has('echelon_id')) {
            $query->where('echelon_id', $request->echelon_id);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Search by name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Eager load relationships
        $with = ['echelon'];
        if ($request->has('with')) {
            $with = array_merge($with, explode(',', $request->with));
        }
        $query->with($with);
        $query->withCount('users');

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $positions = $query->orderBy('name')->paginate($perPage);

        return PositionResource::collection($positions);
    }

    /**
     * Display the specified position.
     */
    public function show(Request $request, Position $position): PositionResource
    {
        // Eager load relationships
        $with = ['echelon'];
        if ($request->has('with')) {
            $with = array_merge($with, explode(',', $request->with));
        }
        $position->load($with);
        $position->loadCount('users');

        return new PositionResource($position);
    }
}
