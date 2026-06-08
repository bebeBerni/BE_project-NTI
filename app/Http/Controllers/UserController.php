<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return response()->json(
            User::with(['companies'])->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:25',
            'last_name'  => 'required|string|max:25',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6',
            'phone'      => 'nullable|string|max:15',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        return response()->json(
            User::with(['companies'])->findOrFail($id)
        );
    }

public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $validated = $request->validate([
        'first_name' => 'sometimes|string|max:25',
        'last_name'  => 'sometimes|string|max:25',
        'email'      => 'sometimes|email|unique:users,email,' . $id,
        'password'   => 'nullable|min:6',
        'phone'      => 'nullable|string|max:15',
    ]);

    if (!empty($validated['password'])) {
        $validated['password'] = bcrypt($validated['password']);
    } else {
        unset($validated['password']);
    }

    $user->update($validated);

    return response()->json([
        'message' => 'User updated successfully',
        'user' => $user
    ]);
}

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // authorization safe check
        if (method_exists($this, 'authorize')) {
            $this->authorize('delete', $user);
        }

        // cleanup pivot
        $user->roles()->detach();

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
