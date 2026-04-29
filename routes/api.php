<?php

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

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/ping-db', function () {
    return response()->json([
        'status' => 'ok'
    ]);
});

// ✅ IDE TEDD (AUTHON KÍVÜL)
Route::post('/debug', function (Illuminate\Http\Request $request) {
    return response()->json([
        'all' => $request->all(),
        'json' => $request->json()->all(),
        'headers' => $request->headers->all()
    ]);
});
/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return response()->json([
        'user' => $request->user()?->load('roles')
    ]);
});
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::post('/change-password', function (Request $request) {
    if (!$request->user()) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    return app(AuthController::class)->changePassword($request);
});

    // Test
    Route::get('/test-role', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'email' => $user->email,
            'roles' => $user->roles->pluck('name'),
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | STUDENTS (auth required)
    |--------------------------------------------------------------------------
    */
    Route::apiResource('students', StudentController::class);

    /*
    |--------------------------------------------------------------------------
    | MENTORS
    |--------------------------------------------------------------------------
    */
    Route::apiResource('mentors', MentorController::class);

    /*
    |--------------------------------------------------------------------------
    | TEAMS
    |--------------------------------------------------------------------------
    */
    Route::apiResource('teams', TeamController::class);

    Route::post('/teams/{team}/add-member', [TeamController::class, 'addMember']);
    Route::delete('/teams/{team}/remove-member/{student}', [TeamController::class, 'removeMember']);
    Route::post('/teams/{team}/activate', [TeamController::class, 'activate']);

    /*
    |--------------------------------------------------------------------------
    | PROJECTS
    |--------------------------------------------------------------------------
    */
    Route::apiResource('projects', ProjectController::class);

    /*
    |--------------------------------------------------------------------------
    | APPLICATIONS
    |--------------------------------------------------------------------------
    */
    Route::apiResource('project-applications', ProjectApplicationController::class);

    /*
    |--------------------------------------------------------------------------
    | ASSIGNMENTS
    |--------------------------------------------------------------------------
    */
    Route::apiResource('project-assignments', ProjectAssignmentController::class);

    /*
    |--------------------------------------------------------------------------
    | HISTORY
    |--------------------------------------------------------------------------
    */
    Route::apiResource('project-histories', ProjectHistoryController::class);

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
//

});
