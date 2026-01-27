<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserApiController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = User::query();

        // Filter by unit_id
        if ($request->has('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        // Filter by instansi_id
        if ($request->has('instansi_id')) {
            $query->where('instansi_id', $request->instansi_id);
        }

        // Filter by position_id
        if ($request->has('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        // Filter by rank_id
        if ($request->has('rank_id')) {
            $query->where('rank_id', $request->rank_id);
        }

        // Filter by travel_grade_id
        if ($request->has('travel_grade_id')) {
            $query->where('travel_grade_id', $request->travel_grade_id);
        }

        // Filter by employee_type
        if ($request->has('employee_type')) {
            $query->where('employee_type', $request->employee_type);
        }

        // Search by name, nip, or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Eager load relationships
        $with = ['unit', 'instansi', 'position.echelon', 'rank', 'travelGrade'];
        if ($request->has('with')) {
            $with = array_merge($with, explode(',', $request->with));
        }
        $query->with($with);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100); // Max 100 per page

        $users = $query->orderBy('name')->paginate($perPage);

        return UserResource::collection($users);
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, User $user): UserResource
    {
        // Eager load relationships
        $with = ['unit', 'instansi', 'position.echelon', 'rank', 'travelGrade'];
        if ($request->has('with')) {
            $with = array_merge($with, explode(',', $request->with));
        }
        $user->load($with);

        return new UserResource($user);
    }
}
