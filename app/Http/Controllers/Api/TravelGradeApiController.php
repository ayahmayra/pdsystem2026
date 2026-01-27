<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TravelGradeResource;
use App\Models\TravelGrade;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TravelGradeApiController extends Controller
{
    /**
     * Display a listing of the travel grades.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = TravelGrade::query();

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

        $travelGrades = $query->orderBy('code')->paginate($perPage);

        return TravelGradeResource::collection($travelGrades);
    }

    /**
     * Display the specified travel grade.
     */
    public function show(Request $request, TravelGrade $travelGrade): TravelGradeResource
    {
        // Eager load relationships
        $with = [];
        if ($request->has('with')) {
            $with = explode(',', $request->with);
        }
        $travelGrade->load($with);
        $travelGrade->loadCount('users');

        return new TravelGradeResource($travelGrade);
    }
}
