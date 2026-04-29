<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    /**
     *
     */

public function __construct()
{
    $this->middleware('auth:sanctum');
}



    public function index()
    {
        return User::with(['roles', 'companies'])->get();
    }

    /**
     *
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:25',
            'last_name'  => 'required|max:25',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6',
            'phone'      => 'nullable|max:15',
        ]);

        //  hashing
        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        // 🔹 role-ok (pivot: user_roles)
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return response()->json($user, 201);
    }

    /**
     * Show user by ID + kapcsolatok
     */
    public function show($id)
    {
        return User::with(['roles', 'companies'])->findOrFail($id);
    }

    /**
     *  User upd
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|max:25',
            'last_name'  => 'sometimes|max:25',
            'email'      => 'sometimes|email|unique:users,email,' . $id,
            'password'   => 'nullable|min:6',
            'phone'      => 'nullable|max:15',
        ]);

        // if password is being updated, hash it
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        //  role update
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return response()->json($user);
    }

    /**
     *  User del
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
  $this->authorize('delete', $user);
        // pivot cleanup
        $user->roles()->detach();

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
