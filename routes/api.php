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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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

Route::post('teams/{id}/students', [TeamController::class, 'addStudent']);
Route::delete('teams/{id}/students/{studentId}', [TeamController::class, 'removeStudent']);

Route::post('teams/{id}/mentors', [TeamController::class, 'addMentor']);
Route::delete('teams/{id}/mentors/{mentorId}', [TeamController::class, 'removeMentor']);

Route::apiResource('project-assignments', ProjectAssignmentController::class);
