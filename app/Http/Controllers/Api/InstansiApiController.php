<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InstansiResource;
use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InstansiApiController extends Controller
{
    /**
     * Display a listing of the instansis.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Instansi::query();

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

        $instansis = $query->orderBy('name')->paginate($perPage);

        return InstansiResource::collection($instansis);
    }

    /**
     * Display the specified instansi.
     */
    public function show(Request $request, Instansi $instansi): InstansiResource
    {
        // Eager load relationships
        $with = [];
        if ($request->has('with')) {
            $with = explode(',', $request->with);
        }
        $instansi->load($with);
        $instansi->loadCount('users');

        return new InstansiResource($instansi);
    }
}
