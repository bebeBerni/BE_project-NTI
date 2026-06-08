<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Mentor;
use App\Models\Team;
use App\Models\Project;
use App\Models\ProjectAssignment;
use App\Models\Role;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function getHighestRole($roles)
    {
        if (!$roles || $roles->isEmpty()) {
            return null;
        }

        $priority = [
            'admin' => 3,
            'mentor' => 2,
            'student' => 1,
        ];

        return $roles->sortByDesc(function ($role) use ($priority) {
            return $priority[strtolower($role->name)] ?? 0;
        })->first();
    }

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

        $users = $query->get()->map(function ($user) {

            $highestRole = $this->getHighestRole($user->roles);

            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'role' => $highestRole?->name ?? 'No Role',
            ];
        });

        return response()->json([
            'users' => $users,
            'total' => $users->count(),
        ]);
    }

public function updateUser(Request $request, User $user)
{
    $validated = $request->validate([
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'email' => 'required|email',
        'phone' => 'nullable|string',
    ]);

    $user->update($validated);

    return response()->json([
        'message' => 'User updated successfully',
        'user' => $user
    ]);
}

public function deleteUser(User $user)
{
    Student::where('user_id', $user->id)->delete();
    Mentor::where('user_id', $user->id)->delete();

    $user->delete();

    return response()->json([
        'message' => 'User deleted successfully'
    ]);
}

    public function teams()
    {
        return response()->json([
            'teams' => Team::with([
                'leader',
                'students',
                'mentors',
                'projects'
            ])->get()
        ]);
    }

    public function projects()
    {
        return response()->json([
            'projects' => Project::with('team')->get()
        ]);
    }

    public function assignments()
    {
        return response()->json([
            'assignments' => ProjectAssignment::with([
                'project',
                'team'
            ])->get()
        ]);
    }
}
