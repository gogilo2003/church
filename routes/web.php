<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TitheController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OfferingController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ContributionTypeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('setup')
        ->name('setup')
        ->group(function () {
            Route::prefix('departments')
                ->name('-departments')
                ->group(function () {
                    Route::get('', [DepartmentController::class, 'index']);
                    Route::post('', [DepartmentController::class, 'store'])->name('-store');
                    Route::patch('', [DepartmentController::class, 'update'])->name('-update');
                    Route::delete('', [DepartmentController::class, 'destroy'])->name('-destroy');
                });
        });

    Route::prefix('accounts')
        ->name('accounts')
        ->group(function () {
            Route::prefix('contributions')
                ->name('-contributions')
                ->group(function () {
                    Route::get('', [ContributionTypeController::class, 'index']);
                    Route::get('{contribution_type}', [ContributionTypeController::class, 'show'])->name('-show');
                    Route::post('', [ContributionTypeController::class, 'store'])->name('-store');
                    Route::patch('{contribution_type}', [ContributionTypeController::class, 'update'])->name('-update');
                    Route::delete('', [ContributionTypeController::class, 'destroy'])->name('-destroy');
                    Route::post('register', [ContributionController::class, 'store'])->name('-register');
                });

            Route::prefix('tithes')
                ->name('-tithes')
                ->group(function () {
                    Route::get('', [TitheController::class, 'index']);
                    Route::post('', [TitheController::class, 'store'])->name('-store');
                    Route::patch('{tithe}', [TitheController::class, 'update'])->name('-update');
                    Route::delete('', [TitheController::class, 'destroy'])->name('-destroy');
                });

            Route::prefix('offerings')
                ->name('-offerings')
                ->group(function () {
                    Route::get('', [OfferingController::class, 'index']);
                    Route::post('', [OfferingController::class, 'store'])->name('-store');
                    Route::patch('{offering}', [OfferingController::class, 'update'])->name('-update');
                    Route::delete('', [OfferingController::class, 'destroy'])->name('-destroy');
                });

            Route::prefix('payments')
                ->name('-payments')
                ->group(function () {
                    Route::get('', [PaymentController::class, 'index']);
                    Route::post('', [PaymentController::class, 'store'])->name('-store');
                    Route::patch('', [PaymentController::class, 'update'])->name('-update');
                    Route::delete('', [PaymentController::class, 'destroy'])->name('-destroy');
                });
        });


    Route::prefix('users')
        ->name('users')
        ->group(function () {
            Route::get('', [UserController::class, 'index']);
            Route::post('', [UserController::class, 'store'])->name('-store');
            Route::patch('', [UserController::class, 'update'])->name('-update');
            Route::delete('', [UserController::class, 'destroy'])->name('-destroy');
        });
    Route::prefix('members')
        ->name('members')
        ->group(function () {
            Route::get('', [MemberController::class, 'index']);
            Route::post('', [MemberController::class, 'store'])->name('-store');
            Route::patch('{member}', [MemberController::class, 'update'])->name('-update');
            Route::delete('{member}', [MemberController::class, 'destroy'])->name('-delete');
            Route::post('photo', [MemberController::class, 'photo'])->name('-photo');
        });
    Route::prefix('attendance')
        ->name('attendance')
        ->group(function () {
            Route::get('', [AttendanceController::class, 'index']);
            Route::get('show/{attendance}', [AttendanceController::class, 'show'])->name('-show');
            Route::post('', [AttendanceController::class, 'store'])->name('-store');
            Route::get('mark/{attendance}', [AttendanceController::class, 'mark'])->name('-mark');
            Route::patch('mark/{attendance}', [AttendanceController::class, 'markPost'])->name('-mark-post');
            Route::patch('{attendance}', [AttendanceController::class, 'update'])->name('-update');
            Route::delete('{attendance}', [AttendanceController::class, 'destroy'])->name('-delete');
            Route::post('photo', [AttendanceController::class, 'photo'])->name('-photo');
        });
});
