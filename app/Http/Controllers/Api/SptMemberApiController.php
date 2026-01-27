<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SptMemberResource;
use App\Models\SptMember;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SptMemberApiController extends Controller
{
    /**
     * Display a listing of the SPT members.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = SptMember::query();

        // Filter by spt_id
        if ($request->has('spt_id')) {
            $query->where('spt_id', $request->spt_id);
        }

        // Filter by user_id
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Eager load relationships
        $with = ['user', 'spt'];
        if ($request->has('with')) {
            $with = array_merge($with, explode(',', $request->with));
        }
        $query->with($with);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $members = $query->orderBy('id')->paginate($perPage);

        return SptMemberResource::collection($members);
    }

    /**
     * Display the specified SPT member.
     */
    public function show(Request $request, SptMember $sptMember): SptMemberResource
    {
        // Eager load relationships
        $with = ['user', 'spt'];
        if ($request->has('with')) {
            $with = array_merge($with, explode(',', $request->with));
        }
        $sptMember->load($with);

        return new SptMemberResource($sptMember);
    }
}
