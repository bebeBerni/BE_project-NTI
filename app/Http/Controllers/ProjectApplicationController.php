<?php

namespace App\Http\Controllers;

use App\Models\ProjectApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectApplicationController extends Controller
{
    /**
     * Display a listing of project applications
     */


public function __construct()
{
    $this->middleware('auth:sanctum');
}

    public function index()
    {
        $projectApplications = ProjectApplication::with(['project', 'team', 'category'])->get();

        return response()->json([
            'project_applications' => $projectApplications
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created project application
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
        'team_id' => 'required|exists:teams,id',
        'category_id' => 'required|exists:categories,id',
        'status' => 'required|string|max:45',
        'motivation' => 'nullable|string',
        'note' => 'nullable|string',
        'applied_at' => 'nullable|date',
    ]);

    $validated['submitted_by_user_id'] = auth()->id();
    $validated['status'] = 'pending';

    $projectApplication = ProjectApplication::create($validated);

    return response()->json([
        'message' => 'Project application created successfully',
        'project_application' => $projectApplication
    ], Response::HTTP_CREATED);
}

    /**
     * Display a specific project application
     */
    public function show($id)
    {
        $projectApplication = ProjectApplication::with(['project', 'team', 'category','submittedBy'])->find($id);

        if (!$projectApplication) {
            return response()->json([
                'message' => 'Project application not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'project_application' => $projectApplication
        ], Response::HTTP_OK);
    }

    /**
     * Update project application
     */
    public function update(Request $request, $id)
    {
        $projectApplication = ProjectApplication::find($id);

        if (!$projectApplication) {
            return response()->json([
                'message' => 'Project application not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'project_id' => 'sometimes|exists:projects,id',
            'team_id' => 'sometimes|exists:teams,id',
            'category_id' => 'sometimes|exists:categories,id',
            'status' => 'sometimes|string|max:45',
            'motivation' => 'nullable|string',
            'note' => 'nullable|string',
            'applied_at' => 'nullable|date',
        ]);

        $projectApplication->update($validated);

        return response()->json([
            'message' => 'Project application updated successfully',
            'project_application' => $projectApplication
        ], Response::HTTP_OK);
    }

    /**
     * Delete project application
     */
    public function destroy($id)
    {
        $projectApplication = ProjectApplication::find($id);

        if (!$projectApplication) {
            return response()->json([
                'message' => 'Project application not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $projectApplication->delete();

        return response()->json([
            'message' => 'Project application deleted successfully'
        ], Response::HTTP_OK);
    }
}
