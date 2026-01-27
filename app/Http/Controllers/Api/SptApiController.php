<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SptResource;
use App\Models\Spt;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SptApiController extends Controller
{
    /**
     * Display a listing of the SPTs.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Spt::query();

        // Filter by signed_by_user_id
        if ($request->has('signed_by_user_id')) {
            $query->where('signed_by_user_id', $request->signed_by_user_id);
        }

        // Filter by nota_dinas_id
        if ($request->has('nota_dinas_id')) {
            $query->where('nota_dinas_id', $request->nota_dinas_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by doc_no or assignment_title
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('doc_no', 'like', '%' . $search . '%')
                  ->orWhere('assignment_title', 'like', '%' . $search . '%');
            });
        }

        // Eager load relationships
        $with = ['signedByUser', 'members.user'];
        if ($request->has('with')) {
            $with = array_merge($with, explode(',', $request->with));
        }
        $query->with($with);
        $query->withCount('members');

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $spts = $query->orderBy('spt_date', 'desc')->paginate($perPage);

        return SptResource::collection($spts);
    }

    /**
     * Display the specified SPT.
     */
    public function show(Request $request, Spt $spt): SptResource
    {
        // Eager load relationships
        $with = ['signedByUser', 'members.user'];
        if ($request->has('with')) {
            $with = array_merge($with, explode(',', $request->with));
        }
        $spt->load($with);
        $spt->loadCount('members');

        return new SptResource($spt);
    }
}
