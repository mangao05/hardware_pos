<?php

use Illuminate\Http\Request;
use App\Http\Controllers\GetRoles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\LeisureController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\Authentication\Login;
use App\Http\Controllers\HistoryLogController;
use App\Http\Controllers\RestoTableController;
use App\Http\Controllers\Rooms\RoomController;
use App\Http\Controllers\Authentication\Logout;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FoodCategoryController;
use App\Http\Controllers\ReservationDetailsController;
use App\Http\Controllers\Rooms\RoomCategoryController;
use App\Http\Controllers\UserVerifyPasswordController;

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

Route::get('/roles', GetRoles::class)->name('role.index');
Route::post('/login', Login::class)->name('auth.login');

Route::middleware(['auth'])->group(function () {

    Route::middleware('check.role:Super Admin,Front Desk,Supervisor')->group(function () {
        Route::get('/dashboard', function () {
            return view('features.dashboard');
        })->name('cms.dashboard');

        Route::get('/room-category', function () {
            return view('features.room_category');
        });

        Route::get('/booking', function () {
            return view('features.booking');
        });
    });

    Route::middleware('check.role:Super Admin')->group(function () {
        Route::get('/user-management', function () {
            return view('features.user_management');
        });

        Route::get('/rooms', function () {
            return view('features.rooms');
        });

        Route::get('/package', function () {
            return view('features.package');
        });

        Route::get('/leisures-add-ons', function () {
            return view('features.leisures_add_ons');
        });

        Route::get('/pos', function () {
            return view('pos.pos_order');
        });
    });

    Route::post('/logout', Logout::class)->name('auth.logout');

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
});

Route::post('checkout', [ReservationController::class, 'checkout']);

Route::get('check-password', function () {
    return \Hash::check('Pantukan@2025', auth()->user()->password);
});
