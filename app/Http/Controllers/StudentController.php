<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StudentController extends Controller
{
    /**
     * Display a listing of students
     */
    public function index()
    {
        $students = Student::with('user')->get();

        return response()->json([
            'students' => $students
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'users_id' => 'required|exists:users,id',
            'faculty' => 'required|string|max:45',
            'department' => 'required|string|max:45',
            'study_program' => 'required|string|max:45',
            'year_of_study' => 'required|string|max:45',
            'is_ukf_verified' => 'boolean',
        ]);

        $student = Student::create($validated);

        return response()->json([
            'message' => 'Student created successfully',
            'student' => $student
        ], Response::HTTP_CREATED);
    }

    /**
     * Display a specific student
     */
    public function show($id)
    {
        $student = Student::with(['user', 'teams'])->find($id);

        if (!$student) {
            return response()->json([
                'message' => 'Student not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'student' => $student
        ], Response::HTTP_OK);
    }

    /**
     * Update student
     */
    public function update(Request $request, $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'message' => 'Student not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'faculty' => 'sometimes|string|max:45',
            'department' => 'sometimes|string|max:45',
            'study_program' => 'sometimes|string|max:45',
            'year_of_study' => 'sometimes|string|max:45',
            'is_ukf_verified' => 'boolean',
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
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'message' => 'Student not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully'
        ], Response::HTTP_OK);
    }
}
