<?php

declare(strict_types=1);

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ContributionTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OfferingController;
use App\Http\Controllers\OfferingTypeController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\TitheController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::middleware([
        'auth',
        'verified',
    ])->group(function () {

        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Setup & Organization Hierarchy Routes
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

                Route::prefix('organization')
                    ->name('-organization')
                    ->controller(OrganizationController::class)
                    ->group(function () {
                        Route::get('', 'index');
                        Route::post('levels', 'storeLevel')->name('-levels-store');
                        Route::post('units', 'storeUnit')->name('-units-store');
                        Route::delete('units/{unit}', 'destroyUnit')->name('-units-destroy');
                    });
            });

        // Financial & Accounting Routes
        Route::prefix('accounts')
            ->name('accounts')
            ->group(function () {
                Route::prefix('contributions')
                    ->name('-contributions')
                    ->controller(ContributionTypeController::class)
                    ->group(function () {
                        Route::get('', 'index');
                        Route::get('{contribution_type}', 'show')->name('-show');
                        Route::post('', 'store')->name('-store');
                        Route::patch('{contribution_type}', 'update')->name('-update');
                        Route::delete('', 'destroy')->name('-destroy');
                    });

                Route::prefix('contributions')
                    ->name('-contributions')
                    ->controller(ContributionController::class)
                    ->group(function () {
                        Route::post('register', 'store')->name('-register');
                    });

                Route::prefix('tithes')
                    ->name('-tithes')
                    ->controller(TitheController::class)
                    ->group(function () {
                        Route::get('', 'index');
                        Route::post('', 'store')->name('-store');
                        Route::patch('{tithe}', 'update')->name('-update');
                        Route::delete('', 'destroy')->name('-destroy');
                    });

                Route::prefix('offerings')
                    ->name('-offerings')
                    ->controller(OfferingController::class)
                    ->group(function () {
                        Route::get('', 'index');
                        Route::post('', 'store')->name('-store');
                        Route::patch('{offering}', 'update')->name('-update');
                        Route::delete('', 'destroy')->name('-destroy');
                    });

                Route::prefix('offering-types')
                    ->name('-offering-types')
                    ->controller(OfferingTypeController::class)
                    ->group(function () {
                        Route::get('', 'index');
                        Route::post('', 'store')->name('-store');
                        Route::patch('{offering}', 'update')->name('-update');
                        Route::delete('', 'destroy')->name('-destroy');
                    });

                Route::prefix('payments')
                    ->name('-payments')
                    ->controller(PaymentController::class)
                    ->group(function () {
                        Route::get('', 'index');
                        Route::post('', 'store')->name('-store');
                        Route::patch('', 'update')->name('-update');
                        Route::delete('', 'destroy')->name('-destroy');
                    });
            });

        // User Management
        Route::prefix('users')
            ->name('users.')
            ->controller(UserController::class)
            ->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('', 'store')->name('store');
                Route::get('{user}/edit', 'edit')->name('edit');
                Route::patch('{user}', 'update')->name('update');
                Route::delete('{user}', 'destroy')->name('destroy');
                Route::patch('{user}/toggle-status', 'toggleStatus')->name('toggle-status');
                Route::post('{user}/reset-password', 'resetPassword')->name('reset-password');
            });

        // Role Management
        Route::prefix('roles')
            ->name('roles.')
            ->controller(\App\Http\Controllers\RoleController::class)
            ->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('', 'store')->name('store');
                Route::get('{role}/edit', 'edit')->name('edit');
                Route::patch('{role}', 'update')->name('update');
                Route::delete('{role}', 'destroy')->name('destroy');
                Route::get('{role}/users', 'assignUsers')->name('users');
                Route::post('{role}/users', 'syncUsers')->name('users.sync');
            });

        // Member Management
        Route::prefix('members')
            ->name('members')
            ->controller(MemberController::class)
            ->group(function () {
                Route::get('', 'index');
                Route::post('', 'store')->name('-store');
                Route::patch('{member}', 'update')->name('-update');
                Route::delete('{member}', 'destroy')->name('-delete');
                Route::post('photo', 'photo')->name('-photo');
                Route::get('/download', 'download')->name('-download');
            });

        // Attendance Management
        Route::prefix('attendance')
            ->controller(AttendanceController::class)
            ->name('attendance')
            ->group(function () {
                Route::get('', 'index');
                Route::get('show/{attendance}', 'show')->name('-show');
                Route::post('', 'store')->name('-store');
                Route::get('mark/{attendance}', 'mark')->name('-mark');
                Route::patch('mark/{attendance}', 'markPost')->name('-mark-post');
                Route::patch('{attendance}', 'update')->name('-update');
                Route::delete('{attendance}', 'destroy')->name('-delete');
                Route::post('photo', 'photo')->name('-photo');
            });

        // SMS Messaging
        Route::prefix('messaging')
            ->name('messaging')
            ->group(function () {
                Route::prefix('sms')
                    ->controller(SmsController::class)
                    ->name('-sms')
                    ->group(function () {
                        Route::get('', 'index');
                        Route::post('', 'store')->name('-store');
                        Route::patch('', 'update')->name('-update');
                    });
            });
    });

    require __DIR__.'/auth.php';
});

