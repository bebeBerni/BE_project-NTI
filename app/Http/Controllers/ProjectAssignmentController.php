<?php

namespace App\Http\Controllers;

use App\Models\ProjectAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function store(Request $request)
    {
        $this->authorize('create', ProjectAssignment::class);

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'team_id' => 'required|exists:teams,id',
        ]);

        $validated['status'] = 'active';
        $validated['assigned_at'] = now();

        $assignment = ProjectAssignment::create($validated);

        return response()->json([
            'message' => 'Team assigned to project',
            'assignment' => $assignment
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $assignment = ProjectAssignment::with(['project', 'team'])->find($id);

        if (!$assignment) {
            return response()->json([
                'message' => 'Assignment not found'
            ], 404);
        }

        $this->authorize('view', $assignment);

        return response()->json([
            'assignment' => $assignment
        ]);
    }

    public function update(Request $request, $id)
    {
        $assignment = ProjectAssignment::find($id);

        if (!$assignment) {
            return response()->json([
                'message' => 'Assignment not found'
            ], 404);
        }

        $this->authorize('update', $assignment);

        $validated = $request->validate([
            'status' => 'required|string|max:45'
        ]);

        $assignment->update($validated);

        return response()->json([
            'message' => 'Assignment updated',
            'assignment' => $assignment
        ]);
    }

    public function destroy($id)
    {
        $assignment = ProjectAssignment::find($id);

        if (!$assignment) {
            return response()->json([
                'message' => 'Assignment not found'
            ], 404);
        }

        $this->authorize('delete', $assignment);

        $assignment->delete();

        return response()->json([
            'message' => 'Assignment removed'
        ]);
    }
}
