<?php

use Illuminate\Http\Request;
use App\Http\Controllers\GetRoles;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\LeisureController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\RestoTableController;
use App\Http\Controllers\Rooms\RoomController;
use App\Http\Controllers\Rooms\RoomCategoryController;

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

Route::apiResource('room-categories', RoomCategoryController::class);
Route::apiResource('rooms', RoomController::class);
Route::apiResource('leisures', LeisureController::class);
Route::apiResource('packages', PackageController::class);
Route::apiResource('agents', AgentController::class);
Route::apiResource('resto-tables', RestoTableController::class);