<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/jobs', [JobController::class, 'store']);            // Buat job
    Route::patch('/jobs/{id}/publish', [JobController::class, 'publish']); // Publish job
    Route::get('/jobs/my', [JobController::class, 'myJobs']);         // Job milik sendiri
});

Route::get('/jobs', [JobController::class, 'index']); // Dilihat semua (tanpa login)

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/jobs/{id}/apply', [ApplicationController::class, 'apply']); // Freelancer apply
    Route::get('/jobs/{id}/applications', [ApplicationController::class, 'viewApplicants']); // Employer lihat pelamar
});
