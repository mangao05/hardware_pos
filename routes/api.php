<?php

use App\Http\Controllers\GetRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']); // List all users
    Route::post('/', [UserController::class, 'store']); // Create a new user
    Route::put('/{id}', [UserController::class, 'update']); // Update a user
    Route::delete('/{id}', [UserController::class, 'destroy']); // Soft delete a user
    Route::get('/{id}', [UserController::class, 'view']); // View user details
    Route::post('/restore/{id}', [UserController::class, 'restore']); // Restore a soft-deleted user
});

Route::get('/roles', GetRoles::class)->name('role.index');

