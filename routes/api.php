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

    Route::get('/me', function (Request $request) {
        return response()->json([
            'user' => $request->user()?->load('roles')
        ]);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    Route::post('/change-password', function (Request $request) {
        return app(AuthController::class)->changePassword($request);
    });

    Route::get('/test-role', function (Request $request) {
        return [
            'email' => $request->user()->email,
            'roles' => $request->user()->roles->pluck('name'),
        ];
    });
Route::get('/auth-debug', function (Request $request) {
    return [
        'bearer_token' => $request->bearerToken(),
        'user' => $request->user(),
        'auth_check' => auth()->check(),
    ];
})->middleware('auth:sanctum');
    Route::apiResource('students', StudentController::class);
    Route::apiResource('mentors', MentorController::class);
    Route::apiResource('teams', TeamController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('project-applications', ProjectApplicationController::class);
    Route::apiResource('project-assignments', ProjectAssignmentController::class);
    Route::apiResource('project-histories', ProjectHistoryController::class);
    Route::apiResource('commissions', CommissionController::class);
    Route::apiResource('decisions', DecisionController::class);

    Route::get('/categories', [CategoryController::class, 'index']);

});
