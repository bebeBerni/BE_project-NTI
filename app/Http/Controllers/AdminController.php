<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Mentor;
use App\Models\Team;
use App\Models\Project;
use App\Models\ProjectAssignment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'message' => 'Welcome to the admin dashboard.',
            'statistics' => [
                'users' => User::count(),
                'students' => Student::count(),
                'mentors' => Mentor::count(),
                'teams' => Team::count(),
                'projects' => Project::count(),
                'project_assignments' => ProjectAssignment::count(),
            ]
        ]);
    }

 public function users(Request $request)
{
    return $this->getUsers($request);
}

private function getUsers(Request $request)
{
    $query = User::with('roles');

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    return response()->json([
        'users' => $query->get()
    ]);
}

    public function teams()
    {
        return response()->json([
            'teams' => Team::with(['leader', 'students', 'mentors', 'projects'])->get()
        ]);
    }

    public function projects()
    {
        return response()->json([
            'projects' => Project::with(['team'])->get()
        ]);
    }

    public function assignments()
    {
        return response()->json([
            'assignments' => ProjectAssignment::with(['project', 'team'])->get()
        ]);
    }





}
