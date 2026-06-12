<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TasksController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/organizations', [OrganizationController::class, 'store']);
    Route::get('/organizations', [OrganizationController::class, 'index']);
    Route::put('/organizations/{organization}', [OrganizationController::class, 'update']);
    Route::get('/organizations/{organization}', [OrganizationController::class, 'show']);
    Route::get('/organizations/{organization}/users', [OrganizationController::class, 'users']);

    Route::post('/organizations/{organization}/projects', [ProjectController::class, 'store']);
    Route::get('/organizations/{organization}/projects', [ProjectController::class, 'index']);
    Route::put('/organizations/{organization}/projects/{project}', [ProjectController::class, 'update']);
    Route::get('/organizations/{organization}/projects/{project}', [ProjectController::class, 'show']);

    Route::post('/organizations/{organization}/projects/{project}/tasks', [TasksController::class, 'store']);
    Route::get('/organizations/{organization}/projects/{project}/tasks', [TasksController::class, 'index']);
    Route::put('/organizations/{organization}/projects/{project}/tasks/{task}', [TasksController::class, 'update']);
    Route::get('/organizations/{organization}/projects/{project}/tasks/{task}', [TasksController::class, 'show']);
});

