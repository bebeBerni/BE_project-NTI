<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Team;
use App\Models\Mentor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with([
            'leader',
            'students',
            'projects',
            'mentors.user'
        ])->get();

        return response()->json([
            'teams' => $teams
        ]);
    }

    public function show($id)
    {
        $team = Team::with([
            'leader',
            'students',
            'mentors',
            'projects'
        ])->findOrFail($id);

        return response()->json([
            'team' => $team
        ]);
    }

    public function activate($id)
    {
        $team = Team::findOrFail($id);

        $team->status = 'active';
        $team->save();

        return response()->json([
            'message' => 'Team activated successfully.',
            'team' => $team
        ]);
    }

    public function deactivate($id)
    {
        $team = Team::findOrFail($id);

        $team->status = 'inactive';
        $team->save();

        return response()->json([
            'message' => 'Team deactivated successfully.',
            'team' => $team
        ]);
    }
    public function removeMember(
        Team $team,
        Student $student
    ) {
        $leader = auth()->user()->student;

        $isLeader = $team->students()
            ->where('students.id', $leader->id)
            ->wherePivot('member_role', 'leader')
            ->exists();

        if (!$isLeader) {
            return response()->json([
                'message' => 'Only team leaders can remove members.'
            ], 403);
        }

        $isTargetLeader = $team->students()
            ->where('students.id', $student->id)
            ->wherePivot('member_role', 'leader')
            ->exists();

        if ($isTargetLeader) {
            return response()->json([
                'message' => 'Leader cannot be removed.'
            ], 403);
        }

        $team->students()->detach($student->id);

        return response()->json([
            'message' => 'Member removed successfully.'
        ]);
    }
    public function assignMentor(Request $request, Team $team)
    {
        $request->validate([
            'mentor_id' => 'required|exists:mentors,id'
        ]);

        $team->mentors()->syncWithoutDetaching([
            $request->mentor_id => [
                'assigned_at' => Carbon::now(),
                'active' => true
            ]
        ]);

        return response()->json([
            'message' => 'Mentor assigned successfully'
        ]);
    }
    public function removeMentor(Team $team, Mentor $mentor)
    {
        $team->mentors()->detach($mentor->id);

        return response()->json([
            'message' => 'Mentor removed'
        ]);
    }
}
