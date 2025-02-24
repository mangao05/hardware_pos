<?php

use App\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\LeisureController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\HistoryLogController;
use App\Http\Controllers\RestoTableController;
use App\Http\Controllers\Rooms\RoomController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FoodCategoryController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ReservationDetailsController;
use App\Http\Controllers\Rooms\RoomCategoryController;
use App\Http\Controllers\UserVerifyPasswordController;


Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::get('/{id}', [UserController::class, 'view']);
    Route::post('/restore/{id}', [UserController::class, 'restore']);
    // Route for updating user profile (name, password, image, etc.)
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('users.profile');
});


Route::resource('room-categories', RoomCategoryController::class)->only([
    'index',
    'store',
    'show',
    'update',
    'destroy'
]);

Route::resource('rooms-data', RoomController::class)->only([
    'index',
    'store',
    'show',
    'update',
    'destroy'
]);

Route::resource('leisures', LeisureController::class)->only([
    'index',
    'store',
    'show',
    'update',
    'destroy'
]);

Route::resource('packages', PackageController::class)->only([
    'index',
    'store',
    'show',
    'update',
    'destroy'
]);

Route::resource('agents', AgentController::class)->only([
    'index',
    'store',
    'show',
    'update',
    'destroy'
]);

Route::resource('resto-tables', RestoTableController::class)->only([
    'index',
    'store',
    'show',
    'update',
    'destroy'
]);

Route::resource('reservations', ReservationController::class)->only([
    'index',
    'store',
    'show',
    'update',
    'destroy'
]);

Route::put('reservations/{reservation}/update-status/{room}', [ReservationController::class, 'updateReservationRoomStatus']);
Route::post('reservations/change-room/{reservation}', [ReservationController::class, 'changeReservationRoom']);

Route::delete('reservation-rooms/{id}', [ReservationDetailsController::class, 'deleteReservationRoomDetails']);

Route::get('history-logs', [HistoryLogController::class, 'list']);
// Route::post(on-rooms/{id}/addons', [ReservationDetailsController::class, 'listAddons']);

Route::get('reports/rooms-status', [ReportsController::class, 'room_statuses']);
Route::get('reports/sales-summary', [ReportsController::class, 'sales_summary']);
Route::get('reports/walk-in/payments-summary', [ReportsController::class, 'payments_summary']);

Route::get('/categories/{category}/available-rooms', [RoomCategoryController::class, 'getAvailableRooms'])
    ->name('categories.available-rooms');

Route::post('/user/verify-password', [UserVerifyPasswordController::class, 'verify']);
Route::post('/payments/delete', [ReservationController::class, 'voidPayment']);
Route::get('/profile', function () {
    $roles = \App\Models\Role::pluck('name', 'id');
    return view('account.profile', compact('roles'));
});

Route::resource('foods', FoodController::class);
Route::resource('food-categories', FoodCategoryController::class);
Route::resource('payment-methods', PaymentMethodController::class);