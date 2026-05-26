<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Project;
use App\Models\Student;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        return response()->json([
            'message' => 'Welcome to the student dashboard.',
            'student' => $request->user()
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'student' => $request->user()
        ]);
    }

    public function teams()
    {
        return response()->json([
            'teams' => Team::all()
        ]);
    }

    public function projects()
    {
        return response()->json([
            'projects' => Project::all()
        ]);
    }

    public function addProject(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:45'],
            'description' => ['required', 'string'],
            'type' => ['required', 'string', 'max:45'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'budget' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:pending,active,paused,finished,archived'],
            'deadline' => ['nullable', 'date'],
        ]);

        $validated['created_by_user_id'] = $request->user()->id;

        $project = Project::create($validated);

        return response()->json([
            'message' => 'Project was created successfully.',
            'project' => $project
        ], 201);
    }

    public function joinProject($projectId, Request $request)
    {
        $project = Project::findOrFail($projectId);

        $user = $request->user();

        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student profile was not found for this user.'
            ], 404);
        }

        if (!$project->team_id) {
            return response()->json([
                'message' => 'This project has no assigned team.'
            ], 400);
        }

        $team = Team::findOrFail($project->team_id);

        if ($team->students()->where('students.id', $student->id)->exists()) {
            return response()->json([
                'message' => 'Student is already a member of this project team.'
            ], 409);
        }

        $team->students()->attach($student->id, [
            'member_role' => 'member',
            'joined_at' => now(),
        ]);

        return response()->json([
            'message' => 'Successfully joined the project team.',
            'project' => $project,
            'team' => $team,
            'student' => $student
        ]);
    }

    public function joinTeam($teamId, Request $request)
    {
        $user = $request->user();

        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student profile was not found for this user.'
            ], 404);
        }

        $team = Team::findOrFail($teamId);

        if ($team->students()->where('students.id', $student->id)->exists()) {
            return response()->json([
                'message' => 'Student is already a member of this team.'
            ], 409);
        }

        $team->students()->attach($student->id, [
            'member_role' => 'member',
            'joined_at' => now(),
        ]);

        return response()->json([
            'message' => 'Successfully joined the team.',
            'team' => $team,
            'student' => $student
        ], 201);
    }

    public function createTeam(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student profile was not found.'
            ], 404);
        }

        $team = Team::create([
            'name' => $validated['name'],
            'leader_user_id' => $user->id,
            'status' => $validated['status'] ?? 'active',
        ]);

        $team->students()->attach($student->id, [
            'member_role' => 'leader',
            'joined_at' => now(),
        ]);

        return response()->json([
            'message' => 'Team created successfully.',
            'team' => $team
        ], 201);
    }
}
