<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Mentor;
use App\Models\Student;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Services\EmailService;
use Psy\Util\Str;


class AuthController extends Controller
{
    public function __construct(
        private EmailService $emailService
    ) {}




    // --------------------
    // GENERIC REGISTER
    // --------------------
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'min:6', 'confirmed'],
            'phone'      => ['nullable', 'string'],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'phone'      => $validated['phone'] ?? null,
            'email_verified_at' => null,
        ]);

        $role = Role::where('name', 'student')->first();

        if ($role) {
            $user->roles()->attach($role->id);
        }

        $this->emailService->sendWelcomeEmail($user);

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Registration successful. Please verify your email before login.',
            'user' => $user->load('roles'),
        ], 201);
    }

    // --------------------
    // STUDENT REGISTRATION
    // --------------------
    public function registerStudent(Request $request, EmailService $emailService)
    {
        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'password'      => ['required', 'min:6', 'confirmed'],
            'phone'         => ['nullable', 'string'],
            'faculty'       => ['nullable', 'string'],
            'department'    => ['nullable', 'string'],
            'study_program' => ['nullable', 'string'],
            'year_of_study' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'phone'      => $validated['phone'] ?? null,
        ]);

        // Attach student role
        $studentRole = Role::where('name', 'student')->first();
        if ($studentRole) {
            $user->roles()->attach($studentRole->id);
        }

        // Create Student profile
        Student::create([
            'user_id'        => $user->id,
            'faculty'        => $validated['faculty'] ?? null,
            'department'     => $validated['department'] ?? null,
            'study_program'  => $validated['study_program'] ?? null,
            'year_of_study'  => $validated['year_of_study'] ?? null,
            'is_ukf_verified' => false,
        ]);
        $emailService->sendWelcomeEmail(
            $user,
            'student'
        );
        $user->sendEmailVerificationNotification();

        return response()->json([
            'user' => $user->load('roles', 'student'),
        ], 201);
    }

    // --------------------
    // MENTOR REGISTRATION
    // --------------------
    public function registerMentor(Request $request,EmailService $emailService)
    {
        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'password'      => ['required', 'min:6', 'confirmed'],
            'phone'         => ['nullable', 'string'],
            'specialization' => ['required', 'string', 'max:255'],
            'bio'           => ['nullable', 'string', 'max:1000'],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'phone'      => $validated['phone'] ?? null,
        ]);

        // Attach mentor role
        $mentorRole = Role::where('name', 'mentor')->first();
        if ($mentorRole) {
            $user->roles()->attach($mentorRole->id);
        }

        // Create Mentor profile
        Mentor::create([
            'user_id'        => $user->id,
            'specialization' => $validated['specialization'],
            'bio'            => $validated['bio'] ?? null,
        ]);

        $emailService->sendWelcomeEmail(
            $user,
            'mentor'
        );
        $user->sendEmailVerificationNotification();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user->load('roles', 'mentor'),
            'token' => $token,
        ], 201);
    }

    // --------------------
    // COMPANY REGISTRATION
    // --------------------
    public function registerCompany(Request $request,EmailService $emailService)
    {
        $validated = $request->validate([
            'first_name'   => ['required', 'string', 'max:255'],
            'last_name'    => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'password'     => ['required', 'min:6', 'confirmed'],
            'phone'        => ['nullable', 'string'],
            'company_name' => ['required', 'string', 'max:255'],
            'ico'          => ['required', 'string', 'unique:companies,ico'],
            'description'  => ['nullable', 'string'],
            'website'      => ['nullable', 'url'],
            'address'      => ['nullable', 'string'],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'phone'      => $validated['phone'] ?? null,
        ]);

        // Attach company role (you may want to add this role)
        $companyRole = Role::where('name', 'company')->first();
        if ($companyRole) {
            $user->roles()->attach($companyRole->id);
        }

        // Create Company
        $company = Company::create([
            'company_name' => $validated['company_name'],
            'ico'          => $validated['ico'],
            'description'  => $validated['description'] ?? null,
            'website'      => $validated['website'] ?? null,
            'address'      => $validated['address'] ?? null,
        ]);

        // Add user to company as admin
        $user->companies()->attach($company->id, ['role_in_company' => 'admin']);
        $emailService->sendWelcomeEmail(
            $user,
            'company'
        );
        $user->sendEmailVerificationNotification();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user->load('roles', 'companies'),
            'token' => $token,
        ], 201);
    }

    // --------------------
    // LOGIN
    // --------------------


    public function login(
        Request $request,
        EmailService $emailService
    )
    {
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Incorrect email or password.'],
            ]);
        }
// check email verification
        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Please verify your email first.'
            ], 403);
        }

        $emailService->sendLoginNotification($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user->load('roles'),
            'token' => $token,
        ]);
    }


/*
public function login(Request $request)
{
    return response()->json([
        'content_type' => $request->header('Content-Type'),
        'all' => $request->all(),
        'json' => $request->json()->all(),
        'raw' => $request->getContent()
    ]);
}
*/




    // --------------------
    // ME
    // --------------------
    public function me(Request $request)
    {
        return response()->json($request->user()->load('roles'));
    }

    // --------------------
    // LOGOUT
    // --------------------
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    // --------------------
    // LOGOUT ALL
    // --------------------
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out all devices']);
    }

    // --------------------
    // CHANGE PASSWORD (FIXED)
    // --------------------
    public function changePassword(Request $request,EmailService $emailService)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:6', 'confirmed'],
        ]);

        $user = $request->user();

        // check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is wrong'
            ], 422);
        }

        // update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);
        $emailService->sendPasswordChangedEmail($user);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password was changed successfully.'
            ]);
        }

        return response()->json([
            'message' => 'Invalid token or email.'
        ], 400);
    }
}
