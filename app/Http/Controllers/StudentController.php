<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectApplication;
use App\Models\ProjectAssignment;
use App\Models\Student;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $student = Student::with([
            'user',
            'teams.students.user',
        ])->where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student profile was not found.'
            ], 404);
        }

        $team = $student->teams->first();

        $project = null;

        if ($team) {
            $assignment = ProjectAssignment::with([
                'project.company',
            ])
                ->where('team_id', $team->id)
                ->where('status', 'assigned')
                ->latest()
                ->first();

            $project = $assignment ? $assignment->project : null;
        }

        return response()->json([
            'message' => 'Welcome to the student dashboard.',
            'student' => $student,
            'team' => $team,
            'team_members' => $team ? $team->students : [],
            'project' => $project,
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
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);

        $user = $request->user();

        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student profile was not found for this user.'
            ], 404);
        }

        $team = $student->teams()->first();

        if (!$team) {
            return response()->json([
                'message' => 'Student is not a member of any team.'
            ], 400);
        }

        $categoryId = $validated['category_id'] ?? 1;

        unset($validated['category_id']);

        $result = DB::transaction(function () use ($validated, $user, $team, $categoryId) {
            $validated['created_by_user_id'] = $user->id;
            $validated['team_id'] = $validated['team_id'] ?? $team->id;
            $validated['status'] = $validated['status'] ?? 'pending';

            $project = Project::create($validated);

            $assignment = ProjectAssignment::firstOrCreate(
                [
                    'project_id' => $project->id,
                    'team_id' => $team->id,
                ],
                [
                    'status' => 'assigned',
                    'assigned_at' => now(),
                ]
            );

            $application = ProjectApplication::firstOrCreate(
                [
                    'project_id' => $project->id,
                    'team_id' => $team->id,
                ],
                [
                    'category_id' => $categoryId,
                    'status' => 'pending',
                    'motivation' => null,
                    'note' => null,
                    'applied_at' => now(),
                ]
            );

            return [
                'project' => $project,
                'assignment' => $assignment,
                'application' => $application,
            ];
        });

        return response()->json([
            'message' => 'Project was created successfully and team was applied.',
            'project' => $result['project'],
            'assignment' => $result['assignment'],
            'project_application' => $result['application'],
            'project_application_id' => $result['application']->id,
        ], 201);
    }

    public function joinProject($projectId, Request $request)
    {
        $user = $request->user();

        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student profile was not found for this user.'
            ], 404);
        }

        $team = $student->teams()->first();

        if (!$team) {
            return response()->json([
                'message' => 'Student is not a member of any team.'
            ], 400);
        }

        $project = Project::findOrFail($projectId);

        $categoryId = $request->input('category_id', 1);

        $result = DB::transaction(function () use ($project, $team, $categoryId) {
            $assignment = ProjectAssignment::firstOrCreate(
                [
                    'project_id' => $project->id,
                    'team_id' => $team->id,
                ],
                [
                    'status' => 'assigned',
                    'assigned_at' => now(),
                ]
            );

            $application = ProjectApplication::firstOrCreate(
                [
                    'project_id' => $project->id,
                    'team_id' => $team->id,
                ],
                [
                    'category_id' => $categoryId,
                    'status' => 'pending',
                    'motivation' => null,
                    'note' => null,
                    'applied_at' => now(),
                ]
            );

            return [
                'assignment' => $assignment,
                'application' => $application,
            ];
        });

        return response()->json([
            'message' => 'Team successfully joined the project.',
            'project' => $project,
            'team' => $team,
            'assignment' => $result['assignment'],
            'project_application' => $result['application'],
            'project_application_id' => $result['application']->id,
        ], 201);
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
