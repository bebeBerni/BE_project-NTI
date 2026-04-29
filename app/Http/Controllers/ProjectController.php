<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;



class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index()
    {
        $projects = Project::with(['creator', 'company', 'team'])->get();

        return response()->json([
            'projects' => $projects
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:45',
            'description' => 'required|string',
            'type' => 'required|string|max:45',
             // 'created_by_user_id' => 'required|exists:users,id',
           'created_by_user_id' => auth()->id(),

            'company_id' => 'nullable|exists:companies,id',
            'team_id' => 'nullable|exists:teams,id',
            'budget' => 'required|numeric|min:0',
            'status' => ['required', Rule::in([
                'pending',
                'active',
                'paused',
                'finished',
                'archived',
            ])],
            'deadline' => 'nullable|date',
        ]);

        $project = Project::create($validated);

        return response()->json([
            'message' => 'Project created successfully',
            'project' => $project
        ], Response::HTTP_CREATED);
    }

    /**
     * Display a specific project
     */


    /* CHLOE kommentalta ki
    public function show($id)
    {
        $project = Project::with([
            'creator',
            'company',
            'team',
            'assignments',
            'applications',
            'decisions',
            'history'
        ])->find($id);

        if (!$project) {
            return response()->json([
                'message' => 'Project not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'project' => $project
        ], Response::HTTP_OK);
    }

    */
// UJ VALTOZAT CHLOE SZERINT
public function show(Project $project)
{
    return response()->json([
        'project' => $project->load([
            'creator',
            'company',
            'team',
            'assignments',
            'applications',
            'decisions',
            'history'
        ])
    ]);
}




    /**
     * Update project
     */




    /* CHLOE kommentalta ki
    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'message' => 'Project not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:45',
            'description' => 'sometimes|string',
            'type' => 'sometimes|string|max:45',
            'created_by_user_id' => 'sometimes|exists:users,id',
            'company_id' => 'nullable|exists:companies,id',
            'team_id' => 'nullable|exists:teams,id',
            'budget' => 'sometimes|numeric|min:0',
            'status' => ['sometimes', Rule::in([
                'pending',
                'active',
                'paused',
                'finished',
                'archived',
            ])],
            'deadline' => 'nullable|date',
        ]);

        $project->update($validated);

        return response()->json([
            'message' => 'Project updated successfully',
            'project' => $project
        ], Response::HTTP_OK);
    }
*/
public function update(Request $request, Project $project)
{
    $validated = $request->validate([
        'title' => 'sometimes|string|max:45',
        'description' => 'sometimes|string',
        'type' => 'sometimes|string|max:45',
        'company_id' => 'nullable|exists:companies,id',
        'team_id' => 'nullable|exists:teams,id',
        'budget' => 'sometimes|numeric|min:0',
        'status' => ['sometimes', Rule::in([
            'pending','active','paused','finished','archived',
        ])],
        'deadline' => 'nullable|date',
    ]);

    $project->update($validated);

    return response()->json([
        'message' => 'Project updated successfully',
        'project' => $project
    ]);
}
    /**
     * Delete project
     */


        /* CHLOE kommentalta ki
   /* public function destroy($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'message' => 'Project not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully'
        ], Response::HTTP_OK);
    }
    */
public function destroy(Project $project)
{
    $this->authorize('delete', $project);

    $project->delete();

    return response()->json([
        'message' => 'Project deleted successfully'
    ]);
}



}
