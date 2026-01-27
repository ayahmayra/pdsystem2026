<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RankResource;
use App\Models\Rank;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RankApiController extends Controller
{
    /**
     * Display a listing of the ranks.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Rank::query();

        // Search by code or name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        // Eager load relationships
        $with = [];
        if ($request->has('with')) {
            $with = explode(',', $request->with);
        }
        $query->with($with);
        $query->withCount('users');

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $ranks = $query->orderBy('code')->paginate($perPage);

        return RankResource::collection($ranks);
    }

    /**
     * Display the specified rank.
     */
    public function show(Request $request, Rank $rank): RankResource
    {
        // Eager load relationships
        $with = [];
        if ($request->has('with')) {
            $with = explode(',', $request->with);
        }
        $rank->load($with);
        $rank->loadCount('users');

        return new RankResource($rank);
    }
}
