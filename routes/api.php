<?php

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
});

Route::apiResource('students', StudentController::class);

Route::apiResource('mentors', MentorController::class);

Route::apiResource('teams', TeamController::class);

Route::apiResource('decisions', DecisionController::class);

Route::apiResource('projects', ProjectController::class);

Route::apiResource('commissions', CommissionController::class);

Route::apiResource('project-histories', ProjectHistoryController::class);

Route::apiResource('project-applications', ProjectApplicationController::class);

Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/commissions/{id}/members', [CommissionController::class, 'getMembers']);
Route::post('/commissions/{id}/members', [CommissionController::class, 'addMember']);
Route::delete('/commissions/{commissionId}/members/{userId}', [CommissionController::class, 'removeMember']);

Route::apiResource('project-assignments', ProjectAssignmentController::class);

// --------------------
// MIDDLEWARE
// --------------------

Route::middleware(['auth:sanctum', 'mentor'])->group(function () {
    Route::get('/mentor', [MentorController::class, 'index']);
    Route::get('/mentor/{id}', [MentorController::class, 'show']);
    Route::put('/mentor/{id}', [MentorController::class, 'update']);
    Route::delete('/mentor/{id}', [MentorController::class, 'destroy']);

    Route::get('/mentor/dashboard', [MentorController::class, 'dashboard']);
});

Route::middleware(['auth:sanctum', 'student'])->group(function () {
    Route::get('/student', [StudentController::class, 'index']);
    Route::get('/student/{id}', [StudentController::class, 'show']);
    Route::put('/student/{id}', [StudentController::class, 'update']);
    Route::delete('/student/{id}', [StudentController::class, 'destroy']);

    Route::get('/student/dashboard', [StudentController::class, 'dashboard']);
});

Route::middleware(['auth:sanctum', 'student'])->group(function () {
    Route::post('/teams', [TeamController::class, 'store']);
    Route::post('/teams/{teamId}/add-member', [TeamController::class, 'addMember']);
    Route::delete('/teams/{teamId}/remove-member/{studentId}', [TeamController::class, 'removeMember']);
    Route::put('/teams/{teamId}', [TeamController::class, 'update']);
    Route::post('/teams/{teamId}/activate', [TeamController::class, 'activate']);
});
