<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectApplication;
use App\Models\ProjectAssignment;
use App\Models\Student;
use App\Models\Team;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TeamJoinRequest;
use App\Models\Document;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $student = Student::with([
            'user',
            'teams.students.user',
        ])->where('user_id', $user->id)->first();

        $hasCv = Document::where('user_id', auth()->id())
            ->where('type', 'cv')
            ->exists();

        if (!$student) {
            return response()->json([
                'message' => 'Student profile was not found.'
            ], 404);
        }

        $team = $student->teams->first();

        $project = null;
        $projectApplications = [];

        if ($team) {
            $projectApplications = ProjectApplication::with('project')
                ->where('team_id', $team->id)
                ->latest()
                ->get();
        }

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
        $pendingRequests = collect();

        if ($team) {

            $isLeader = $team->students
                ->contains(function ($member) use ($student) {
                    return $member->id === $student->id
                        && $member->pivot->member_role === 'leader';
                });

            if ($isLeader) {
                $pendingRequests = TeamJoinRequest::with([
                    'student.user'
                ])
                    ->where('team_id', $team->id)
                    ->where('status', 'pending')
                    ->get();
            }
        }
        $projectApplication = null;

        if ($project) {
            $projectApplication = ProjectApplication::where(
                'team_id',
                $team->id
            )
                ->where(
                    'project_id',
                    $project->id
                )
                ->latest()
                ->first();
        }

        return response()->json([
            'message' => 'Welcome to the student dashboard.',
            'student' => $student,
            'team' => $team,
            'team_members' => $team ? $team->students : [],
            'project' => $project,
            'project_applications' => $projectApplications,
            'has_cv' => $hasCv,
            'pending_team_requests' => $pendingRequests,
            'project_application_status' => $projectApplication?->status,
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

    public function projects(Request $request)
    {
        $student = $request->user()->student;
        $team = $student->teams()->first();

        $appliedProjectIds = [];

        if ($team) {
            $appliedProjectIds = ProjectApplication::where(
                'team_id',
                $team->id
            )->pluck('project_id');
        }

        $projects = Project::where('status', 'pending')
            ->whereNotIn('id', $appliedProjectIds)
            ->get();

        return response()->json([
            'projects' => $projects
        ]);
    }

    public function addProject(Request $request)
    {
        $student = auth()->user()->student;

        $team = $student->teams()->first();

        if (!$team) {
            return response()->json([
                'message' => 'You must be in a team.'
            ], 403);
        }

        $isLeader = $team->students()
            ->where('students.id', $student->id)
            ->wherePivot('member_role', 'leader')
            ->exists();

        if (!$isLeader) {
            return response()->json([
                'message' => 'Only team leaders can apply for projects.'
            ], 403);
        }
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

    public function joinProject($projectId, Request $request,EmailService $emailService)
    {
        $student = auth()->user()->student;

        $team = $student->teams()->first();

        if (!$team) {
            return response()->json([
                'message' => 'You must be in a team.'
            ], 403);
        }

        $isLeader = $team->students()
            ->where('students.id', $student->id)
            ->wherePivot('member_role', 'leader')
            ->exists();

        if (!$isLeader) {
            return response()->json([
                'message' => 'Only team leaders can apply for projects.'
            ], 403);
        }
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
        $hasApprovedApplication = ProjectApplication::where(
            'team_id',
            $team->id
        )
            ->where('status', 'approved')
            ->exists();

        if ($hasApprovedApplication) {
            return response()->json([
                'message' => 'Your team already has an approved project.'
            ], 422);
        }


        $project = Project::findOrFail($projectId);

        $existingApplication = ProjectApplication::where(
            'team_id',
            $team->id
        )
            ->where('project_id', $project->id)
            ->exists();

        if ($existingApplication) {
            return response()->json([
                'message' => 'Your team has already applied for this project.'
            ], 422);
        }

        $categoryId = $request->input('category_id', 1);

        $result = DB::transaction(function () use ($project, $team, $categoryId,$user) {
            $application = ProjectApplication::create([
                'project_id' => $project->id,
                'team_id' => $team->id,
                'category_id' => $categoryId,
                'submitted_by_user_id' => $user->id,
                'status' => 'pending',
                'motivation' => null,
                'note' => null,
                'applied_at' => now(),
            ]);

            return [
                'application' => $application,
            ];
        });
        $emailService->sendApplicationSubmittedEmail(
            $user,
            $result['application']
        );
        return response()->json([
            'message' => 'Team successfully joined the project.',
            'project' => $project,
            'team' => $team,
            'project_application' => $result['application'],
            'project_application_id' => $result['application']->id,
        ], 201);
    }

    public function joinTeam($teamId, Request $request,EmailService $emailService)
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

        $existingRequest = TeamJoinRequest::where('team_id', $team->id)
            ->where('student_id', $student->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            return response()->json([
                'message' => 'You already have a pending request for this team.'
            ], 409);
        }

        $joinRequest = TeamJoinRequest::create([
            'team_id' => $team->id,
            'student_id' => $student->id,
            'status' => 'pending',
        ]);

        $emailService->sendTeamJoinedEmail(
            $user,
            $team
        );

        return response()->json([
            'message' => 'Join request sent successfully.',
            'request' => $joinRequest
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
    public function leaveTeam(Request $request, $teamId)
    {
        $user = $request->user();

        $student = Student::where('user_id', $user->id)->firstOrFail();

        $team = Team::findOrFail($teamId);

        $membership = $student->teams()
            ->where('teams.id', $team->id)
            ->first();

        if (!$membership) {
            return response()->json([
                'message' => 'You are not a member of this team.'
            ], 400);
        }

        if ($membership->pivot->member_role === 'leader') {
            return response()->json([
                'message' => 'Team leaders cannot leave their team.'
            ], 403);
        }

        $student->teams()->detach($team->id);

        return response()->json([
            'message' => 'Successfully left the team.'
        ]);
    }
}
