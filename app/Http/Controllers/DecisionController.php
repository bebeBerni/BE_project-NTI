<?php

namespace App\Http\Controllers;

use App\Models\Decision;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class DecisionController extends Controller
{
    /**
     * Display a listing of decisions
     */
    public function index()
    {
        $decisions = Decision::with(['project', 'commission'])->get();

        return response()->json([
            'decisions' => $decisions
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created decision
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'commission_id' => 'required|exists:commissions,id',
            'status' => ['required', Rule::in([
                'pending',
                'approved',
                'rejected',
                'revision',
            ])],
            'comment' => 'nullable|string',
            'decided_at' => 'nullable|date',
        ]);

        $decision = Decision::create($validated);

        return response()->json([
            'message' => 'Decision created successfully',
            'decision' => $decision
        ], Response::HTTP_CREATED);
    }

    /**
     * Display a specific decision
     */
    public function show($id)
    {
        $decision = Decision::with(['project', 'commission'])->find($id);

        if (!$decision) {
            return response()->json([
                'message' => 'Decision not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'decision' => $decision
        ], Response::HTTP_OK);
    }

    /**
     * Update decision
     */
    public function update(Request $request, $id)
    {
        $decision = Decision::find($id);

        if (!$decision) {
            return response()->json([
                'message' => 'Decision not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'project_id' => 'sometimes|exists:projects,id',
            'commission_id' => 'sometimes|exists:commissions,id',
            'status' => ['sometimes', Rule::in([
                'pending',
                'approved',
                'rejected',
                'revision',
            ])],
            'comment' => 'nullable|string',
            'decided_at' => 'nullable|date',
        ]);

        $decision->update($validated);

        return response()->json([
            'message' => 'Decision updated successfully',
            'decision' => $decision
        ], Response::HTTP_OK);
    }

    /**
     * Delete decision
     */
    public function destroy($id)
    {
        $decision = Decision::find($id);

        if (!$decision) {
            return response()->json([
                'message' => 'Decision not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $decision->delete();

        return response()->json([
            'message' => 'Decision deleted successfully'
        ], Response::HTTP_OK);
    }
}
