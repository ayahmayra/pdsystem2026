<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EchelonResource;
use App\Models\Echelon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EchelonApiController extends Controller
{
    /**
     * Display a listing of the echelons.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Echelon::query();

        // Search by code or name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $echelons = $query->orderBy('code')->paginate($perPage);

        return EchelonResource::collection($echelons);
    }

    /**
     * Display the specified echelon.
     */
    public function show(Echelon $echelon): EchelonResource
    {
        return new EchelonResource($echelon);
    }
}
