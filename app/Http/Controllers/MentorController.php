<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MentorController extends Controller
{
    /**
     * Display a listing of mentors
     */
    public function index()
    {
        $mentors = Mentor::with(['user', 'teams'])->get();

        return response()->json([
            'mentors' => $mentors
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created mentor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'users_id' => 'required|exists:users,id',
            'specialization' => 'required|string|max:45',
            'bio' => 'required|string|max:45',
        ]);

        $mentor = Mentor::create($validated);

        return response()->json([
            'message' => 'Mentor created successfully',
            'mentor' => $mentor
        ], Response::HTTP_CREATED);
    }

    /**
     * Display a specific mentor
     */
    public function show($id)
    {
        $mentor = Mentor::with(['user', 'teams'])->find($id);

        if (!$mentor) {
            return response()->json([
                'message' => 'Mentor not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'mentor' => $mentor
        ], Response::HTTP_OK);
    }

    /**
     * Update mentor
     */
    public function update(Request $request, $id)
    {
        $mentor = Mentor::find($id);

        if (!$mentor) {
            return response()->json([
                'message' => 'Mentor not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'specialization' => 'sometimes|string|max:45',
            'bio' => 'sometimes|string|max:45',
        ]);

        $mentor->update($validated);

        return response()->json([
            'message' => 'Mentor updated successfully',
            'mentor' => $mentor
        ], Response::HTTP_OK);
    }

    /**
     * Delete mentor
     */
    public function destroy($id)
    {
        $mentor = Mentor::find($id);

        if (!$mentor) {
            return response()->json([
                'message' => 'Mentor not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $mentor->delete();

        return response()->json([
            'message' => 'Mentor deleted successfully'
        ], Response::HTTP_OK);
    }
}
