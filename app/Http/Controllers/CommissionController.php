<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\CommissionMember;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class CommissionController extends Controller
{
    /**
     * Display a listing of commissions
     */
    public function index()
    {
        $commissions = Commission::with('decisions')->get();

        return response()->json([
            'commissions' => $commissions
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created commission
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
     * Display a specific commission
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
        ], Response::HTTP_OK);
    }

    /**
     * Update commission
     */
    public function update(Request $request, $id)
    {
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
            'commission' => $commission
        ], Response::HTTP_OK);
    }

    /**
     * Delete commission
     */
    public function destroy($id)
    {
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
     * Get all members of a commission
     */
    public function getMembers($id)
    {
        $commission = Commission::find($id);

        if (!$commission) {
            return response()->json([
                'message' => 'Commission not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $members = CommissionMember::with('user')
            ->where('commission_id', $id)
            ->get();

        return response()->json([
            'commission' => $commission,
            'members' => $members
        ], Response::HTTP_OK);
    }

    /**
     * Add member to commission
     */
    public function addMember(Request $request, $id)
    {
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
            'member' => $member
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove member from commission
     */
    public function removeMember($commissionId, $userId)
    {
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
}
