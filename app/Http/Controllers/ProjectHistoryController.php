<?php

namespace App\Http\Controllers;

use App\Models\ProjectHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectHistoryController extends Controller
{
    /**
     * Display a listing of project histories
     */
    public function index()
    {
        $projectHistories = ProjectHistory::with(['project', 'team'])->get();

        return response()->json([
            'project_histories' => $projectHistories
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created project history
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'team_id' => 'required|exists:teams,id',
            'result' => 'nullable|string',
            'final_note' => 'nullable|string',
            'finished_at' => 'nullable|date',
        ]);

        $projectHistory = ProjectHistory::create($validated);

        return response()->json([
            'message' => 'Project history created successfully',
            'project_history' => $projectHistory
        ], Response::HTTP_CREATED);
    }

    /**
     * Display a specific project history
     */
    public function show($id)
    {
        $projectHistory = ProjectHistory::with(['project', 'team'])->find($id);

        if (!$projectHistory) {
            return response()->json([
                'message' => 'Project history not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'project_history' => $projectHistory
        ], Response::HTTP_OK);
    }

    /**
     * Update project history
     */
    public function update(Request $request, $id)
    {
        $projectHistory = ProjectHistory::find($id);

        if (!$projectHistory) {
            return response()->json([
                'message' => 'Project history not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'project_id' => 'sometimes|exists:projects,id',
            'team_id' => 'sometimes|exists:teams,id',
            'result' => 'nullable|string',
            'final_note' => 'nullable|string',
            'finished_at' => 'nullable|date',
        ]);

        $projectHistory->update($validated);

        return response()->json([
            'message' => 'Project history updated successfully',
            'project_history' => $projectHistory
        ], Response::HTTP_OK);
    }

    /**
     * Delete project history
     */
    public function destroy($id)
    {
        $projectHistory = ProjectHistory::find($id);

        if (!$projectHistory) {
            return response()->json([
                'message' => 'Project history not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $projectHistory->delete();

        return response()->json([
            'message' => 'Project history deleted successfully'
        ], Response::HTTP_OK);
    }
}
