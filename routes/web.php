<?php

use App\Http\Controllers\GetRoles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Authentication\Login;
use App\Http\Controllers\Authentication\Logout;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('cms.dashboard');
    }
    return view('login_page');
})->name('login');

Route::get('/dashboard', function () {
    return view('features.dashboard');
})->name('cms.dashboard');

Route::get('/user-management', function () {
    return view('features.user_management');
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
Route::post('/login', Login::class)->name('auth.login');
Route::post('/logout', Logout::class)->name('auth.logout');
