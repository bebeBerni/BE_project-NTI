<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\TeamJoinRequest;
use App\Services\EmailService;

class LeaderController extends Controller
{
    public function __construct(
        private EmailService $emailService
    ) {}
    public function teamJoinRequests(Request $request)
    {
        $user = $request->user();

        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student profile not found.'
            ], 404);
        }

        $team = $student->teams()
            ->wherePivot('member_role', 'leader')
            ->first();

        if (!$team) {
            return response()->json([
                'message' => 'You are not a team leader.'
            ], 403);
        }

        $requests = TeamJoinRequest::with([
            'student.user'
        ])
            ->where('team_id', $team->id)
            ->where('status', 'pending')
            ->get();

        return response()->json([
            'team' => $team,
            'requests' => $requests
        ]);
    }
    public function approveTeamRequest(
        Request $request,
                $requestId,
        EmailService $emailService
    )
    {
        $user = $request->user();

        $leader = Student::where('user_id', $user->id)->first();

        if (!$leader) {
            return response()->json([
                'message' => 'Student profile not found.'
            ], 404);
        }

        $leaderTeam = $leader->teams()
            ->wherePivot('member_role', 'leader')
            ->first();

        if (!$leaderTeam) {
            return response()->json([
                'message' => 'You are not a team leader.'
            ], 403);
        }

        $joinRequest = TeamJoinRequest::findOrFail($requestId);

        if ($joinRequest->team_id !== $leaderTeam->id) {
            return response()->json([
                'message' => 'You cannot approve requests for another team.'
            ], 403);
        }

        if ($joinRequest->status !== 'pending') {
            return response()->json([
                'message' => 'Request has already been processed.'
            ], 409);
        }

        $student = $joinRequest->student;

        $leaderTeam->students()->attach(
            $student->id,
            [
                'member_role' => 'member',
                'joined_at' => now(),
            ]
        );

        $joinRequest->update([
            'status' => 'approved'
        ]);
        $this->emailService->sendTeamJoinedEmail(
            $student->user,
            $leaderTeam
        );

        return response()->json([
            'message' => 'Student approved successfully.'
        ]);
    }
    public function rejectTeamRequest(
        Request $request,
                $requestId
    )
    {
        $user = $request->user();

        $leader = Student::where(
            'user_id',
            $user->id
        )->first();

        if (!$leader) {
            return response()->json([
                'message' => 'Student profile not found.'
            ], 404);
        }

        $leaderTeam = $leader->teams()
            ->wherePivot('member_role', 'leader')
            ->first();

        if (!$leaderTeam) {
            return response()->json([
                'message' => 'You are not a team leader.'
            ], 403);
        }

        $joinRequest = TeamJoinRequest::findOrFail($requestId);

        if ($joinRequest->team_id !== $leaderTeam->id) {
            return response()->json([
                'message' => 'You cannot reject requests for another team.'
            ], 403);
        }

        $joinRequest->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'message' => 'Request rejected successfully.'
        ]);
    }
}
