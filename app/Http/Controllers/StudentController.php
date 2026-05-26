<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Project;
use App\Models\ProjectApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StudentController extends Controller
{

    /**
     * Show logged-in student
     */
    public function index()
    {
        $student = auth()->user()->student;

        return response()->json([
            'student' => $student->load('user')
        ], Response::HTTP_OK);
    }

    /**
     * Show own student profile
     */
    public function show($id)
    {
        $student = auth()->user()->student;

        if (!$student || $student->id != $id) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'student' => $student->load('user', 'teamMembers.team')
        ], Response::HTTP_OK);
    }

    /**
     * Update student profile
     */
    public function update(Request $request, $id)
    {
        $student = auth()->user()->student;

        if (!$student || $student->id != $id) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'faculty' => 'sometimes|string|max:50',
            'department' => 'sometimes|string|max:50',
            'study_program' => 'sometimes|string|max:100',
            'year_of_study' => 'sometimes|integer|min:1|max:5',
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'student' => $student
        ], Response::HTTP_OK);
    }

    /**
     * Student Dashboard - Profile + Team + Projects
     */
    public function dashboard()
    {
        $student = auth()->user()->student;
        $user = auth()->user();

        // Get team if student is in one
        $teamMember = $student->teamMembers()
            ->with([
                'team.teamMembers.student.user',
                'team.projectAssignments.project',
                'team.teamMentors.mentor.user'
            ])
            ->first();

        // Get projects created by student
        $createdProjects = Project::where('created_by_user_id', $user->id)
            ->with('company', 'team')
            ->get();

        // Get projects student applied to
        $appliedProjects = ProjectApplication::where('user_id', $user->id)
            ->with('project')
            ->get();

        // Get projects assigned to student's team
        $assignedProjects = $teamMember
            ? $teamMember->team->projectAssignments()
                ->with('project')
                ->get()
            : [];

        return response()->json([
            'student' => $student->load('user'),
            'team' => $teamMember?->team,
            'created_projects' => $createdProjects,
            'applied_projects' => $appliedProjects,
            'assigned_projects' => $assignedProjects,
        ], Response::HTTP_OK);
    }

    /**
     * Get all available teams (for joining)
     */
    public function availableTeams()
    {
        $student = auth()->user()->student;

        // Check if student already in a team
        if ($student->teamMembers()->exists()) {
            return response()->json([
                'message' => 'You are already in a team'
            ], 400);
        }

        $teams = \App\Models\Team::where('status', 'draft')
            ->withCount('teamMembers')
            ->with('teamMembers.student.user')
            ->having('team_members_count', '<', 5)
            ->get();

        return response()->json([
            'teams' => $teams
        ], Response::HTTP_OK);
    }

    /**
     * Get available projects (for joining)
     */
    public function availableProjects()
    {
        $projects = Project::where('status', '!=', 'archived')
            ->with('creator', 'company', 'team')
            ->get();

        return response()->json([
            'projects' => $projects
        ], Response::HTTP_OK);
    }
}
