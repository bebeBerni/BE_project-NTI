<?php

namespace App\Http\Controllers;

use App\Models\ProjectAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectAssignmentController extends Controller
{
    public function index()
    {
        $assignments = ProjectAssignment::with(['project', 'team'])->get();

        return response()->json([
            'assignments' => $assignments
        ]);
    }

    public function show($id)
    {
        $assignment = ProjectAssignment::with(['project', 'team'])->findOrFail($id);

        return response()->json([
            'assignment' => $assignment
        ]);
    }

    public function update(Request $request, $id)
    {
        $assignment = ProjectAssignment::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'string', 'max:45'],
        ]);

        $assignment->update($validated);

        return response()->json([
            'message' => 'Project assignment updated successfully.',
            'assignment' => $assignment
        ]);
    }

    public function destroy($id)
    {
        $assignment = ProjectAssignment::findOrFail($id);

        $assignment->delete();

        return response()->json([
            'message' => 'Project assignment removed successfully.'
        ]);
    }
}
