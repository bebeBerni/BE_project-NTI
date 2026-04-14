<?php

namespace App\Http\Controllers;

use App\Models\ProjectAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectAssignmentController extends Controller
{
    /**
     * Display a listing of project assignments
     */
    public function index()
    {
        $projectAssignments = ProjectAssignment::with(['project', 'team'])->get();

        return response()->json([
            'project_assignments' => $projectAssignments
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created project assignment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'team_id' => 'required|exists:teams,id',
            'assigned_at' => 'nullable|date',
            'status' => 'required|in:assigned,in_progress,completed',
        ]);

        $projectAssignment = ProjectAssignment::create([
            'project_id' => $validated['project_id'],
            'team_id' => $validated['team_id'],
            'assigned_at' => $validated['assigned_at'] ?? now(),
            'status' => $validated['status'],
        ]);

        return response()->json([
            'message' => 'Project assignment created successfully',
            'project_assignment' => $projectAssignment
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified project assignment
     */
    public function show($id)
    {
        $projectAssignment = ProjectAssignment::with(['project', 'team'])->find($id);

        if (!$projectAssignment) {
            return response()->json([
                'message' => 'Project assignment not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'project_assignment' => $projectAssignment
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified project assignment
     */
    public function update(Request $request, $id)
    {
        $projectAssignment = ProjectAssignment::find($id);

        if (!$projectAssignment) {
            return response()->json([
                'message' => 'Project assignment not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'project_id' => 'sometimes|exists:projects,id',
            'team_id' => 'sometimes|exists:teams,id',
            'assigned_at' => 'sometimes|date',
            'status' => 'sometimes|in:assigned,in_progress,completed',
        ]);

        $projectAssignment->update($validated);

        return response()->json([
            'message' => 'Project assignment updated successfully',
            'project_assignment' => $projectAssignment
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified project assignment
     */
    public function destroy($id)
    {
        $projectAssignment = ProjectAssignment::find($id);

        if (!$projectAssignment) {
            return response()->json([
                'message' => 'Project assignment not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $projectAssignment->delete();

        return response()->json([
            'message' => 'Project assignment deleted successfully'
        ], Response::HTTP_OK);
    }
}
