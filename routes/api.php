<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;

Route::apiResource('{branch_id}/books', BookController::class)
    ->parameters(['books' => 'book'])
    ->middleware('set.tenant.connection');
