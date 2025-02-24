<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
        return redirect()->to('/pos');
    }
    return view('login_page');
})->name('login');

Route::post('/login', Login::class)->name('auth.login');

Route::middleware(['auth'])->group(function () {


    Route::get('/pos', function () {
        return view('pos.pos_order');
    });

    Route::post('/logout', Logout::class)->name('auth.logout');

    Route::get('/profile', function () {
        $roles = \App\Models\Role::pluck('name', 'id');
        return view('account.profile', compact('roles'));
    });
});
