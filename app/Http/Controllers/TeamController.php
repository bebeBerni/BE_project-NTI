<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'student']);
    }

    /**
     * Create team
     */
    public function store(Request $request)
    {
        $this->authorize('create', Team::class);

        $student = auth()->user()->student;

        // Student already in team?
        if ($student->teamMember) {
            return response()->json([
                'message' => 'You are already in a team'
            ], 400);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'leader_user_id' => $student->user_id,
            'status' => 'draft',
        ]);

        TeamMember::create([
            'student_id' => $student->id,
            'team_id' => $team->id,
            'member_role' => 'leader',
        ]);

        return response()->json([
            'message' => 'Team created successfully',
            'team' => $team
        ], Response::HTTP_CREATED);
    }

    /**
     * Add student to team
     */
    public function addMember(Request $request, $teamId)
    {
        $team = Team::findOrFail($teamId);

        $this->authorize('addMember', $team);

        // max 5 members
        if ($team->teamMembers()->count() >= 5) {
            return response()->json([
                'message' => 'Team is full (max 5 members)'
            ], 400);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        $newStudent = Student::findOrFail($validated['student_id']);

        if ($newStudent->teamMember) {
            return response()->json([
                'message' => 'Student already in a team'
            ], 400);
        }

        TeamMember::create([
            'student_id' => $newStudent->id,
            'team_id' => $team->id,
            'member_role' => 'developer',
        ]);

        return response()->json([
            'message' => 'Student added to team'
        ]);
    }

    /**
     * Remove member
     */
    public function removeMember($teamId, $studentId)
    {
        $team = Team::findOrFail($teamId);

        $this->authorize('removeMember', [$team, $studentId]);

        $student = auth()->user()->student;

        // Prevent leader removing himself
        if ($student->id == $studentId && $team->leader_user_id === auth()->id()) {
            return response()->json([
                'message' => 'Leader cannot remove themselves'
            ], 400);
        }

        $member = TeamMember::where([
            'team_id' => $teamId,
            'student_id' => $studentId
        ])->first();

        if (!$member) {
            return response()->json([
                'message' => 'Member not found'
            ], 404);
        }

        $member->delete();

        return response()->json([
            'message' => 'Member removed'
        ]);
    }

    /**
     * Update team name
     */
    public function update(Request $request, $teamId)
    {
        $team = Team::findOrFail($teamId);

        $this->authorize('update', $team);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        $team->update($validated);

        return response()->json([
            'message' => 'Team updated',
            'team' => $team
        ]);
    }

    /**
     * Activate team
     */
    public function activate($teamId)
    {
        $team = Team::findOrFail($teamId);

        $this->authorize('activate', $team);

        if ($team->teamMembers()->count() < 3) {
            return response()->json([
                'message' => 'Minimum 3 members required'
            ], 400);
        }

        $team->update(['status' => 'active']);

        return response()->json([
            'message' => 'Team activated'
        ]);
    }
}
