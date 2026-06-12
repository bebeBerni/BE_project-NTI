<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Team;
use App\Models\Student;

class MentorController extends Controller
{
public function index(Request $request)
{
    $query = Mentor::query()->with('user');

if ($request->filled('search')) {
    $search = $request->search;

    $query->where(function ($q) use ($search) {
        $q->where('specialization', 'like', "%{$search}%")
          ->orWhereHas('user', function ($u) use ($search) {
              $u->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
          });
    });
}

    $mentors = $query->get()->map(function ($mentor) {
        return [
            'user_id' => $mentor->user_id,
            'first_name' => $mentor->user->first_name ?? null,
            'last_name' => $mentor->user->last_name ?? null,
            'email' => $mentor->user->email ?? null,
            'specialization' => $mentor->specialization,
            'phone' => $mentor->user->phone ?? null,
             'bio' => $mentor->bio,

        ];
    });

    return response()->json([
        'mentors' => $mentors
    ]);
}





    public function dashboard(Request $request)
    {
        $user = $request->user();

        $mentor = Mentor::where('user_id', $user->id)->first();

        if (!$mentor) {
            return response()->json([
                'message' => 'Mentor profile was not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Welcome to the mentor dashboard.',
            'user' => $user,
            'mentor' => $mentor
        ]);
    }

    //MANAGE TEAMS
    public function managedTeams(Request $request)
    {
        $user = $request->user();

        $mentor = Mentor::where('user_id', $user->id)->first();

        if (!$mentor) {
            return response()->json([
                'message' => 'Mentor profile was not found.'
            ], 404);
        }

        $teams = $mentor->teams()->get();

        return response()->json([
            'mentor' => $mentor,
            'teams' => $teams
        ]);
    }




    //ASSIGN MENTOR TO TEAM
    public function assignToTeam($teamId, Request $request,EmailService $emailService)
    {
        $user = $request->user();

        $mentor = Mentor::where('user_id', $user->id)->first();

        if (!$mentor) {
            return response()->json([
                'message' => 'Mentor profile was not found.'
            ], 404);
        }

        $team = Team::findOrFail($teamId);

        if ($mentor->teams()->where('teams.id', $team->id)->exists()) {
            return response()->json([
                'message' => 'Mentor is already assigned to this team.'
            ], 409);
        }

        $mentor->teams()->attach($team->id, [
            'assigned_at' => now(),
            'active' => true,
        ]);

        $team->load('teamMembers.student.user');
        $mentor->load('user');

        foreach ($team->teamMembers as $member) {

            $user = $member->student?->user;

            if ($user) {

                $emailService->sendMentorAssignedEmail(
                    $user,
                    $team,
                    $mentor
                );
            }
        }

        return response()->json([
            'message' => 'Mentor was assigned to the team successfully.',
            'mentor' => $mentor,
            'team' => $team
        ], 201);
    }


    //ADD STUDENT TO TEAM
    public function addStudentToTeam($teamId, $studentId, Request $request)
    {
        $user = $request->user();

        $mentor = Mentor::where('user_id', $user->id)->first();

        if (!$mentor) {
            return response()->json([
                'message' => 'Mentor profile was not found.'
            ], 404);
        }

        $team = Team::findOrFail($teamId);
        $student = Student::findOrFail($studentId);

        if (!$mentor->teams()->where('teams.id', $team->id)->exists()) {
            return response()->json([
                'message' => 'You are not allowed to manage this team.'
            ], 403);
        }

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
            'message' => 'Student was added to the team successfully.',
            'team' => $team,
            'student' => $student
        ], 201);
    }


    //REMOVE STUDENT FROM TEAM
    public function removeStudentFromTeam($teamId, $studentId, Request $request)
    {
        $user = $request->user();

        $mentor = Mentor::where('user_id', $user->id)->first();

        if (!$mentor) {
            return response()->json([
                'message' => 'Mentor profile was not found.'
            ], 404);
        }

        $team = Team::findOrFail($teamId);
        $student = Student::findOrFail($studentId);

        if (!$mentor->teams()->where('teams.id', $team->id)->exists()) {
            return response()->json([
                'message' => 'You are not allowed to manage this team.'
            ], 403);
        }

        if (!$team->students()->where('students.id', $student->id)->exists()) {
            return response()->json([
                'message' => 'Student is not a member of this team.'
            ], 404);
        }

        $team->students()->detach($student->id);

        return response()->json([
            'message' => 'Student was removed from the team successfully.'
        ]);
    }
}
