<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'student']);
    }

    /**
     * Show logged-in student
     */
    public function index()
    {
        $student = auth()->user()->student;

        return response()->json([
            'student' => $student->load('user')
        ], Response::HTTP_OK);
    }

    /**
     * Store student
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'faculty' => 'required|string|max:50',
            'department' => 'required|string|max:50',
            'study_program' => 'required|string|max:100',
            'year_of_study' => 'required|integer|min:1|max:5',
            'is_ukf_verified' => 'required|boolean',
        ]);

        $student = Student::create($validated);

        return response()->json([
            'message' => 'Student created successfully',
            'student' => $student
        ], Response::HTTP_CREATED);
    }

    /**
     * Show own student
     */
    public function show($id)
    {
        $student = auth()->user()->student;

        if (!$student || $student->id != $id) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'student' => $student->load('user')
        ], Response::HTTP_OK);
    }

    /**
     * Update student
     */
    public function update(Request $request, $id)
    {
        $student = auth()->user()->student;

        if (!$student || $student->id != $id) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'faculty' => 'sometimes|string|max:50',
            'department' => 'sometimes|string|max:50',
            'study_program' => 'sometimes|string|max:100',
            'year_of_study' => 'sometimes|integer|min:1|max:5',
            'is_ukf_verified' => 'sometimes|boolean',
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully',
            'student' => $student
        ], Response::HTTP_OK);
    }

    /**
     * Delete student
     */
    public function destroy($id)
    {
        $student = auth()->user()->student;

        if (!$student || $student->id != $id) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Student dashboard (team + project + mentor)
     */
    public function dashboard()
    {
        $student = auth()->user()->student;

        $team = $student->teamMember()
            ->with([
                'team.teamMembers.student.user',
                'team.projectAssignments.project',
                'team.teamMentors.mentor.user'
            ])
            ->first();

        return response()->json([
            'student' => $student,
            'team' => $team?->team
        ], Response::HTTP_OK);
    }
}
