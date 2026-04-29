<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
            'phone'      => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'phone'      => $validated['phone'] ?? null,
        ]);

        // ✅ DEFAULT ROLE
        $role = Role::where('name', 'student')->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrácia úspešná.',
            'user' => $user->load('roles'),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Nesprávny email alebo heslo.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Prihlásenie úspešné.',
            'user' => $user->load('roles'),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('roles'),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Odhlásenie úspešné.',
        ]);
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Odhlásenie zo všetkých zariadení úspešné.',
        ]);
    }
    public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required', 'string'],
        'new_password' => ['required', 'string', 'min:6', 'confirmed'],
    ]);

    $user = $request->user();

    // 1. ellenőrizzük a jelenlegi jelszót
    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json([
            'message' => 'A jelenlegi jelszó helytelen.'
        ], 422);
    }

    // 2. új jelszó mentése
    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json([
        'message' => 'Jelszó sikeresen megváltoztatva.'
    ]);
}
}
