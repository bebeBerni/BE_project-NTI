<?php

use App\Http\Controllers\MentorController;
use App\Http\Controllers\ProjectAssignmentController;
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

Route::post('teams/{id}/students', [TeamController::class, 'addStudent']);
Route::delete('teams/{id}/students/{studentId}', [TeamController::class, 'removeStudent']);

Route::post('teams/{id}/mentors', [TeamController::class, 'addMentor']);
Route::delete('teams/{id}/mentors/{mentorId}', [TeamController::class, 'removeMentor']);

Route::apiResource('project-assignments', ProjectAssignmentController::class);
