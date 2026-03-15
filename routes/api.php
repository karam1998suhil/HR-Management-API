<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\PositionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // ── static routes first (no wildcards) ──────────────────────
        Route::get('employees/search',                          [EmployeeController::class, 'search']);
        Route::get('employees/export/csv',                      [EmployeeController::class, 'exportCsv']);
        Route::post('employees/import/csv',                     [EmployeeController::class, 'importCsv']);
        Route::get('employees/no-salary-change/{months}',       [EmployeeController::class, 'noSalaryChange']);

        // ── routes with {employee} wildcard ──────────────────────────
        Route::get('employees/{employee}/hierarchy',             [EmployeeController::class, 'hierarchy']);
        Route::get('employees/{employee}/hierarchy-with-salary', [EmployeeController::class, 'hierarchyWithSalary']);
        Route::get('employees/{employee}/logs',                  [EmployeeController::class, 'logs']);

        // ── apiResource always last ───────────────────────────────────
        Route::apiResource('employees', EmployeeController::class);
        Route::apiResource('positions', PositionController::class);
    });
});