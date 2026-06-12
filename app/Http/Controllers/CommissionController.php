<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\CommissionMember;
use App\Models\ProjectApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Models\Decision;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    /**
     * GET /commissions
     */
    public function index()
    {
        $commissions = Commission::with('decisions')->get();

        return response()->json([
            'commissions' => $commissions
        ], Response::HTTP_OK);
    }

    /**
     * POST /commissions
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:45',
            'description' => 'nullable|string|max:255',
            'status' => ['required', Rule::in([
                'active',
                'inactive',
                'closed',
            ])],
        ]);

        $commission = Commission::create($validated);

        return response()->json([
            'message' => 'Commission created successfully',
            'commission' => $commission
        ], Response::HTTP_CREATED);
    }

    /**
     * GET /commissions/{id}
     */
    public function show($id)
    {
        $commission = Commission::with('decisions')->find($id);

        if (!$commission) {
            return response()->json([
                'message' => 'Commission not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'commission' => $commission
        ]);
    }

    /**
     * PUT /commissions/{id}
     */
    public function update(Request $request, $id)
    {
        $commission = Commission::find($id);

        if (!$commission) {
            return response()->json([
                'message' => 'Commission not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $commission);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:45',
            'description' => 'nullable|string|max:255',
            'status' => ['sometimes', Rule::in([
                'active',
                'inactive',
                'closed',
            ])],
        ]);

        $commission->update($validated);

        return response()->json([
            'message' => 'Commission updated successfully',
            'commission' => $commission
        ]);
    }

    /**
     * DELETE /commissions/{id}
     */
    public function destroy($id)
    {
        $commission = Commission::find($id);

        if (!$commission) {
            return response()->json([
                'message' => 'Commission not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('delete', $commission);

        $commission->delete();

        return response()->json([
            'message' => 'Commission deleted successfully'
        ]);
    }

    /**
     * GET /commissions/{id}/members
     */
    public function members($id)
    {
        $commission = Commission::with('members.user')->find($id);

        if (!$commission) {
            return response()->json([
                'message' => 'Commission not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'members' => $commission->members
        ]);
    }

    /**
     * POST /commissions/{id}/members
     */
    public function addMember(Request $request, $id)
    {
        $commission = Commission::find($id);

        if (!$commission) {
            return response()->json([
                'message' => 'Commission not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $commission);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $exists = CommissionMember::where('commission_id', $id)
            ->where('user_id', $validated['user_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'User is already a member of this commission'
            ], Response::HTTP_CONFLICT);
        }

        $member = CommissionMember::create([
            'commission_id' => $id,
            'user_id' => $validated['user_id'],
        ]);

        return response()->json([
            'message' => 'Member added successfully',
            'member' => $member
        ], Response::HTTP_CREATED);
    }

    /**
     * DELETE /commissions/{id}/members/{userId}
     */
    public function removeMember($commissionId, $userId)
    {
        $commission = Commission::find($commissionId);

        if (!$commission) {
            return response()->json([
                'message' => 'Commission not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $commission);

        $member = CommissionMember::where('commission_id', $commissionId)
            ->where('user_id', $userId)
            ->first();

        if (!$member) {
            return response()->json([
                'message' => 'Member not found in this commission'
            ], Response::HTTP_NOT_FOUND);
        }

        $member->delete();

        return response()->json([
            'message' => 'Member removed successfully'
        ]);
    }

    public function applications(Request $request)
    {
        $commissionIds = CommissionMember::where(
            'user_id',
            $request->user()->id
        )->pluck('commission_id');

        $applications = ProjectApplication::with([
            'project',
            'team',
            'category'
        ])
            ->whereHas('project.decisions', function ($query) use ($commissionIds) {
                $query->whereIn(
                    'commission_id',
                    $commissionIds
                );
            })
            ->where('status', 'pending')
            ->get();

        return response()->json([
            'applications' => $applications
        ]);
    }
    public function approveApplication(Request $request, $id)
    {
        $application = ProjectApplication::with('project')
            ->findOrFail($id);

        $commissionIds = CommissionMember::where(
            'user_id',
            $request->user()->id
        )->pluck('commission_id');

        $canDecide = Decision::where(
            'project_id',
            $application->project_id
        )
            ->whereIn(
                'commission_id',
                $commissionIds
            )
            ->exists();

        if (!$canDecide) {
            return response()->json([
                'message' => 'You are not allowed to approve this application.'
            ], 403);
        }

        DB::transaction(function () use ($application) {

            $application->update([
                'status' => 'approved'
            ]);

            $application->project->update([
                'team_id' => $application->team_id
            ]);

            ProjectApplication::where(
                'project_id',
                $application->project_id
            )
                ->where('id', '!=', $application->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected'
                ]);
        });

        return response()->json([
            'message' => 'Application approved successfully.',
            'application' => $application
        ]);
    }
    public function rejectApplication(Request $request, $id)
    {
        $application = ProjectApplication::with('project')
            ->findOrFail($id);

        $commissionIds = CommissionMember::where(
            'user_id',
            $request->user()->id
        )->pluck('commission_id');

        $canDecide = Decision::where(
            'project_id',
            $application->project_id
        )
            ->whereIn(
                'commission_id',
                $commissionIds
            )
            ->exists();

        if (!$canDecide) {
            return response()->json([
                'message' => 'You are not allowed to reject this application.'
            ], 403);
        }

        $application->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'message' => 'Application rejected successfully.',
            'application' => $application
        ]);
    }
}
