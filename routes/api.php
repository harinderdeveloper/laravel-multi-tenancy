<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\BranchController;

Route::apiResource('{branch_id}/books', BookController::class)
    ->parameters(['books' => 'book'])
    ->middleware('set.tenant.connection');

Route::apiResource('schools', SchoolController::class)
    ->middleware('set.tenant.connection');

Route::apiResource('branches', BranchController::class)
    ->middleware('set.tenant.connection');
