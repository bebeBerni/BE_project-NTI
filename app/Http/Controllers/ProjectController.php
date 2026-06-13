<?php

namespace App\Http\Controllers;

use App\Models\Decision;
use App\Models\Project;
use App\Models\ProjectApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Services\EmailService;



class ProjectController extends Controller
{
    public function __construct(
        EmailService $emailService
    ) {
        $this->emailService = $emailService;
    }
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
            'team_id' => 'nullable|exists:teams,id',
            'budget' => 'required|numeric|min:0',
            'status' => ['required', Rule::in([
                'pending','active','paused','finished','archived',
            ])],
            'deadline' => 'nullable|date',
        ]);

        $company = $request->user()
            ->companies()
            ->first();

        $validated['company_id'] = $company->id;
        $validated['created_by_user_id'] = $request->user()->id;

        $project = Project::create($validated);

        return response()->json([
            'message' => 'Project created successfully',
            'project' => $project
        ], 201);
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
        $oldStatus = $project->status;

        $validated = $request->validate([
            'title' => 'sometimes|string|max:45',
            'description' => 'sometimes|string',
            'type' => 'sometimes|string|max:45',
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

        $newStatus = $project->status;

        if (
            $oldStatus !== $newStatus &&
            in_array($newStatus, ['finished', 'archived'])
        ) {

            $applications = ProjectApplication::where(
                'project_id',
                $project->id
            )->get();

            foreach ($applications as $application) {

                $user = User::find(
                    $application->submitted_by_user_id
                );

                if ($user) {

                    $this->emailService->sendProjectClosedEmail(
                        $user,
                        $project
                    );
                }
            }
        }

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

    public function assignCommission(Request $request, Project $project)
    {
        $request->validate([
            'commission_id' => 'required|exists:commissions,id',
        ]);

        Decision::updateOrCreate(
            [
                'project_id' => $project->id,
            ],
            [
                'commission_id' => $request->commission_id,
                'status' => 'pending',
            ]
        );

        return response()->json([
            'message' => 'Commission assigned successfully',
            'project' => $project->load('decisions')
        ]);
    }

    public function assignedCommission(Project $project)
    {
        $decision = Decision::with('commission')
            ->where('project_id', $project->id)
            ->first();

        return response()->json([
            'commission' => $decision?->commission
        ]);
    }



}
