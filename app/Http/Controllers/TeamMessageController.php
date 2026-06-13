<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\TeamMessage;

class TeamMessageController extends Controller
{

    public function index(Request $request, $teamId)
    {
        if (
            !$this->canAccessTeam(
                $request->user(),
                $teamId
            )
        ) {
            abort(403);
        }
        $messages = TeamMessage::with('user')
            ->where('team_id', $teamId)
            ->oldest()
            ->get();

        return response()->json([
            'messages' => $messages
        ]);
    }
    public function store(Request $request, $teamId)
    {
        if (
            !$this->canAccessTeam(
                $request->user(),
                $teamId
            )
        ) {
            abort(403);
        }
        $request->validate([
            'message' => ['required', 'string']
        ]);

        $message = TeamMessage::create([
            'team_id' => $teamId,
            'user_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => $message->load('user')
        ]);
    }
    private function canAccessTeam($user, $teamId)
    {
        $student = Student::where(
            'user_id',
            $user->id
        )->first();

        if ($student) {
            $isMember = $student->teams()
                ->where('teams.id', $teamId)
                ->exists();

            if ($isMember) {
                return true;
            }
        }

        $mentor = Mentor::where(
            'user_id',
            $user->id
        )->first();

        if ($mentor) {
            $isAssigned = $mentor->teams()
                ->where('teams.id', $teamId)
                ->exists();

            if ($isAssigned) {
                return true;
            }
        }

        return false;
    }
}
