<?php

namespace App\Http\Controllers;

use App\Models\Team;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with([
            'leader',
            'students',
            'mentors',
            'projects'
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
}
