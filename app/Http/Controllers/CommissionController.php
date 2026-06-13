<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\CommissionMember;
use App\Models\ProjectApplication;
use App\Models\Decision;
use App\Models\ProjectAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    private function isAdmin(Request $request): bool
    {
        return $request->user() && $request->user()->role === 'admin';
    }

    /**
     * GET /commissions
     */
    public function index(Request $request)
    {
        $query = Commission::with(['decisions', 'members.user']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $commissions = $query->get();

        return response()->json([
            'commissions' => $commissions
        ], Response::HTTP_OK);
    }

    /**
     * POST /commissions
     */
    public function store(Request $request)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

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
        $commission = Commission::with(['decisions', 'members.user'])->find($id);

        if (!$commission) {
            return response()->json([
                'message' => 'Commission not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'commission' => $commission
        ], Response::HTTP_OK);
    }

    /**
     * PUT /commissions/{id}
     */
    public function update(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        $commission = Commission::find($id);

        if (!$commission) {
            return response()->json([
                'message' => 'Commission not found'
            ], Response::HTTP_NOT_FOUND);
        }

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
            'commission' => $commission->load(['decisions', 'members.user'])
        ], Response::HTTP_OK);
    }

    /**
     * DELETE /commissions/{id}
     */
    public function destroy(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        $commission = Commission::find($id);

        if (!$commission) {
            return response()->json([
                'message' => 'Commission not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $commission->delete();

        return response()->json([
            'message' => 'Commission deleted successfully'
        ], Response::HTTP_OK);
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
        ], Response::HTTP_OK);
    }

    /**
     * POST /commissions/{id}/members
     */
    public function addMember(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        $commission = Commission::find($id);

        if (!$commission) {
            return response()->json([
                'message' => 'Commission not found'
            ], Response::HTTP_NOT_FOUND);
        }

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
            'member' => $member->load('user')
        ], Response::HTTP_CREATED);
    }

    /**
     * DELETE /commissions/{id}/members/{userId}
     */
    public function removeMember(Request $request, $commissionId, $userId)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        $commission = Commission::find($commissionId);

        if (!$commission) {
            return response()->json([
                'message' => 'Commission not found'
            ], Response::HTTP_NOT_FOUND);
        }

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
        ], Response::HTTP_OK);
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
        ], Response::HTTP_OK);
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
            ], Response::HTTP_FORBIDDEN);
        }

        DB::transaction(function () use ($application) {
            $application->update([
                'status' => 'approved'
            ]);

            $application->project->update([
                'team_id' => $application->team_id
            ]);

            ProjectAssignment::create([
                'project_id' => $application->project_id,
                'team_id' => $application->team_id,
                'status' => 'assigned',
                'assigned_at' => now(),
            ]);

            ProjectApplication::where(
                'team_id',
                $application->team_id
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
        ], Response::HTTP_OK);
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
            ], Response::HTTP_FORBIDDEN);
        }

        $application->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'message' => 'Application rejected successfully.',
            'application' => $application
        ], Response::HTTP_OK);
    }
}
