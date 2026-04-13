<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TeamController extends Controller
{
    /**
     * Display a listing of teams
     */
    public function index()
    {
        $teams = Team::with(['leader', 'students', 'mentors', 'projects'])->get();

        return response()->json([
            'teams' => $teams
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created team
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:45',
            'leader_user_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive,archived',
        ]);

        $team = Team::create($validated);

        return response()->json([
            'message' => 'Team created successfully',
            'team' => $team
        ], Response::HTTP_CREATED);
    }

    /**
     * Display a specific team
     */
    public function show($id)
    {
        $team = Team::with(['leader', 'students', 'mentors', 'projects'])->find($id);

        if (!$team) {
            return response()->json([
                'message' => 'Team not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'team' => $team
        ], Response::HTTP_OK);
    }

    /**
     * Update team
     */
    public function update(Request $request, $id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'message' => 'Team not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:45',
            'leader_user_id' => 'sometimes|exists:users,id',
            'status' => 'sometimes|in:active,inactive,archived',
        ]);

        $team->update($validated);

        return response()->json([
            'message' => 'Team updated successfully',
            'team' => $team
        ], Response::HTTP_OK);
    }

    /**
     * Delete team
     */
    public function destroy($id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'message' => 'Team not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $team->delete();

        return response()->json([
            'message' => 'Team deleted successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Add student to team
     */
    public function addStudent(Request $request, $id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'message' => 'Team not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'member_role' => 'required|string|max:45',
            'joined_at' => 'nullable|date',
        ]);

        if ($team->students()->where('students.id', $validated['student_id'])->exists()) {
            return response()->json([
                'message' => 'Student is already in the team'
            ], Response::HTTP_CONFLICT);
        }

        $team->students()->attach($validated['student_id'], [
            'member_role' => $validated['member_role'],
            'joined_at' => $validated['joined_at'] ?? now(),
        ]);

        return response()->json([
            'message' => 'Student added to team successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Remove student from team
     */
    public function removeStudent($id, $studentId)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'message' => 'Team not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $team->students()->detach($studentId);

        return response()->json([
            'message' => 'Student removed from team successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Assign mentor to team
     */
    public function addMentor(Request $request, $id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'message' => 'Team not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'mentor_id' => 'required|exists:mentors,id',
            'assigned_at' => 'nullable|date',
            'active' => 'nullable|boolean',
        ]);

        if ($team->mentors()->where('mentors.id', $validated['mentor_id'])->exists()) {
            return response()->json([
                'message' => 'Mentor is already assigned to the team'
            ], Response::HTTP_CONFLICT);
        }

        $team->mentors()->attach($validated['mentor_id'], [
            'assigned_at' => $validated['assigned_at'] ?? now(),
            'active' => $validated['active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Mentor assigned to team successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Remove mentor from team
     */
    public function removeMentor($id, $mentorId)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'message' => 'Team not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $team->mentors()->detach($mentorId);

        return response()->json([
            'message' => 'Mentor removed from team successfully'
        ], Response::HTTP_OK);
    }
}
