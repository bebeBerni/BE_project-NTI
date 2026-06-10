<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\TeamMessageController;
use App\Http\Middleware\CommissionMemberOnly;
use App\Models\Mentor;
use App\Models\Team;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\DecisionController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\ProjectApplicationController;
use App\Http\Controllers\ProjectAssignmentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectHistoryController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\CompanyController;
use App\Models\Project;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/student', [AuthController::class, 'registerStudent']);
Route::post('/register/mentor', [AuthController::class, 'registerMentor']);
Route::post('/register/company', [AuthController::class, 'registerCompany']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/mentors', [MentorController::class, 'index']);


Route::get('/teams', [TeamController::class, 'index']);
Route::get('/teams/{team}', [TeamController::class, 'show']);

Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{project}', [ProjectController::class, 'show']);






Route::get('/ping-db', function () {
    return response()->json([
        'status' => 'ok',
    ]);
});


Route::post('/debug', function (Request $request) {
    return response()->json([
        'all' => $request->all(),
        'json' => $request->json()->all(),
        'headers' => $request->headers->all(),
    ]);
});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    */

    Route::get('/me', function (Request $request) {
        return response()->json([
            'user' => $request->user()?->load('roles','commissionMembers'),
        ]);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::apiResource('project-histories', ProjectHistoryController::class);

    Route::get('/teams/{team}/messages', [TeamMessageController::class, 'index']);

    Route::post('/teams/{team}/messages', [TeamMessageController::class, 'store']);


    /*
    |--------------------------------------------------------------------------
    | DEBUG / TEST
    |--------------------------------------------------------------------------
    */

    Route::get('/test-role', function (Request $request) {
        return response()->json([
            'email' => $request->user()->email,
            'roles' => $request->user()->roles->pluck('name'),
        ]);
    });

    Route::get('/auth-debug', function (Request $request) {
        return response()->json([
            'bearer_token' => $request->bearerToken(),
            'user' => $request->user(),
            'auth_check' => auth()->check(),
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);

        Route::get('/admin/users', [AdminController::class, 'users']);
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser']);
        Route::delete('/teams/{team}/mentors/{mentor}', [TeamController::class, 'removeMentor']);
        Route::post('/teams/{team}/assign-mentor', [TeamController::class, 'assignMentor']);


        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser']);
        Route::get('/admin/teams', [AdminController::class, 'teams']);
        Route::get('/admin/projects', [AdminController::class, 'projects']);
        Route::get('/admin/project-assignments', [AdminController::class, 'assignments']);

        Route::apiResource('project-assignments', ProjectAssignmentController::class);
        Route::apiResource('students', StudentController::class);
        Route::put('/projects/{project}', [ProjectController::class, 'update']);


    });


    /*
    |--------------------------------------------------------------------------
    | STUDENTS
    |--------------------------------------------------------------------------
    */

    Route::middleware('student')->group(function () {
        Route::get('/student/dashboard', [StudentController::class, 'dashboard']);
        Route::get('/student/available-teams', [StudentController::class, 'teams']);
        Route::get('/student/available-projects', [StudentController::class, 'projects']);
        Route::post('/student/projects', [StudentController::class, 'addProject']);
        Route::post('/student/projects/{project}/join', [StudentController::class, 'joinProject']);
        Route::post('/student/teams/{team}/join', [StudentController::class, 'joinTeam']);
        Route::post('/student/teams', [StudentController::class, 'createTeam']);
        Route::post('/student/teams/{team}/leave', [StudentController::class, 'leaveTeam']);
    });

    Route::middleware(['auth:sanctum'])
        ->prefix('leader')
        ->group(function () {
            Route::get('/team-requests', [LeaderController::class, 'teamJoinRequests']);

            Route::post('/team-requests/{requestId}/approve', [LeaderController::class, 'approveTeamRequest']);

            Route::post('/team-requests/{requestId}/reject', [LeaderController::class, 'rejectTeamRequest']);
        });

    Route::middleware(['auth:sanctum', CommissionMemberOnly::class])->group(function () {

        Route::get('/commission/applications', [CommissionController::class, 'applications']);
        Route::post('/commission/applications/{id}/approve', [CommissionController::class, 'approveApplication']);
        Route::post('/commission/applications/{id}/reject', [CommissionController::class, 'rejectApplication']);

    });



    /*
    |--------------------------------------------------------------------------
    | MENTORS
    |--------------------------------------------------------------------------
    */

    Route::middleware('mentor')->group(function () {
        Route::get('/mentor/dashboard', [MentorController::class, 'dashboard']);
        Route::get('/mentor/teams', [MentorController::class, 'managedTeams']);
        Route::post('/mentor/teams/{team}/students/{student}/add', [MentorController::class, 'addStudentToTeam']);
        Route::delete('/mentor/teams/{team}/students/{student}/remove', [MentorController::class, 'removeStudentFromTeam']);
        Route::post('/mentor/teams/{team}/assign', [MentorController::class, 'assignToTeam']);
    });


    /*
    |--------------------------------------------------------------------------
    | TEAMS
    |--------------------------------------------------------------------------
    */


    Route::post('/teams/{team}/activate', [TeamController::class, 'activate']);
    Route::post('/teams/{team}/deactivate', [TeamController::class, 'deactivate']);
    Route::delete('/leader/teams/{team}/members/{student}', [TeamController::class, 'removeMember'])->middleware('auth:sanctum');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTS
    |--------------------------------------------------------------------------
    */

    Route::post('/documents', [DocumentController::class, 'store']);


    /*
    |--------------------------------------------------------------------------
    | PROJECT APPLICATIONS
    |--------------------------------------------------------------------------
    */

    Route::apiResource('project-applications', ProjectApplicationController::class);


    /*
    |--------------------------------------------------------------------------
    | PROJECT ASSIGNMENTS
    |--------------------------------------------------------------------------
    */

    Route::get('/project-assignments', [ProjectAssignmentController::class, 'index']);
    Route::get('/project-assignments/{id}', [ProjectAssignmentController::class, 'show']);
    Route::put('/project-assignments/{id}', [ProjectAssignmentController::class, 'update']);
    Route::delete('/project-assignments/{id}', [ProjectAssignmentController::class, 'destroy']);


    /*
    |--------------------------------------------------------------------------
    | COMMISSIONS
    |--------------------------------------------------------------------------
    */

    Route::apiResource('commissions', CommissionController::class);

    Route::get('/commissions/{commission}/members', [CommissionController::class, 'members']);
    Route::post('/commissions/{commission}/members', [CommissionController::class, 'addMember']);
    Route::delete('/commissions/{commission}/members/{user}', [CommissionController::class, 'removeMember']);


    /*
    |--------------------------------------------------------------------------
    | DECISIONS
    |--------------------------------------------------------------------------
    */

    Route::apiResource('decisions', DecisionController::class);


    /*
    |--------------------------------------------------------------------------
    | CATEGORIES
    |--------------------------------------------------------------------------
    */

    Route::get('/categories', [CategoryController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | COMPANY
    |--------------------------------------------------------------------------
    */
    Route::middleware('company')->group(function () {

        Route::get('/company/me', [CompanyController::class, 'myCompany']);
        Route::put('/company/me', [CompanyController::class, 'updateMyCompany']);

        Route::get('/company/projects', [CompanyController::class, 'myProjects']);

        Route::post('/company/projects', [ProjectController::class, 'store']);
    });



});

Route::post('/email/verification-notification',
    function (Request $request) {

        $request->user()
            ->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification link sent.'
        ]);
    }
)->middleware('auth:sanctum','throttle:3,1');

Route::post('/forgot-password', [
    PasswordResetController::class,
    'forgotPassword'
]);

Route::post('/reset-password', [
    PasswordResetController::class,
    'resetPassword'
]);

Route::get('/reset-password/{token}', function ($token, Request $request) {
    return response()->json([
        'route_working' => true,
        'token' => $token,
        'email' => $request->query('email'),
        'full_url' => $request->fullUrl(),
    ]);
})->name('password.reset');

