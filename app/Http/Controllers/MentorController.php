<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MentorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'mentor']);
    }

    /**
     * Show logged-in mentor only
     */
    public function index()
    {
        $mentor = auth()->user()->mentor;

        return response()->json([
            'mentor' => $mentor->load(['user', 'teams'])
        ], Response::HTTP_OK);
    }

    /**
     * Store mentor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialization' => 'required|string|max:45',
            'bio' => 'required|string|max:255',
        ]);

        $mentor = Mentor::create($validated);

        return response()->json([
            'message' => 'Mentor created successfully',
            'mentor' => $mentor
        ], Response::HTTP_CREATED);
    }

    /**
     * Show only own mentor profile
     */
    public function show($id)
    {
        $mentor = auth()->user()->mentor;

        if (!$mentor || $mentor->id != $id) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'mentor' => $mentor->load(['user', 'teams'])
        ], Response::HTTP_OK);
    }

    /**
     * Update own mentor
     */
    public function update(Request $request, $id)
    {
        $mentor = auth()->user()->mentor;

        if (!$mentor || $mentor->id != $id) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'specialization' => 'sometimes|string|max:45',
            'bio' => 'sometimes|string|max:255',
        ]);

        $mentor->update($validated);

        return response()->json([
            'message' => 'Mentor updated successfully',
            'mentor' => $mentor
        ], Response::HTTP_OK);
    }

    /**
     * Delete own mentor
     */
    public function destroy($id)
    {
        $mentor = auth()->user()->mentor;

        if (!$mentor || $mentor->id != $id) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        $mentor->delete();

        return response()->json([
            'message' => 'Mentor deleted successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Mentor dashboard (teams + students + projects)
     */
    public function dashboard()
    {
        $mentor = auth()->user()->mentor;

        $teams = $mentor->teams()
            ->with([
                'teamMembers.student.user',
                'projectAssignments.project'
            ])
            ->get();

        return response()->json([
            'mentor' => $mentor,
            'teams' => $teams
        ], Response::HTTP_OK);
    }
}
