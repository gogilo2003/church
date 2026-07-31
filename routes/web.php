<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Central\Admin\CentralDashboardController;
use App\Http\Controllers\Central\Admin\TenantManagementController;
use App\Http\Controllers\Central\RegisterTenantController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ContributionTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OfferingController;
use App\Http\Controllers\OfferingTypeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\TitheController;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Central Tenant Self-Onboarding Routes
Route::prefix('register-tenant')->name('central.register-tenant.')->group(function () {
    Route::get('', [RegisterTenantController::class, 'create'])->name('create');
    Route::post('', [RegisterTenantController::class, 'store'])->name('store');
});
Route::get('/api/central/check-subdomain', [RegisterTenantController::class, 'checkSubdomain'])->name('central.check-subdomain');

Route::post('/messaging/sms-callback', [SmsController::class, 'callback']);

Route::middleware([
    'auth',
    'verified',
])->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Central Admin Dashboard & Tenant Management Routes
    Route::get('/admin', CentralDashboardController::class)->name('central.admin.dashboard');

    Route::prefix('admin/tenants')->name('central.admin.tenants.')->group(function () {
        Route::get('', [TenantManagementController::class, 'index'])->name('index');
        Route::post('', [TenantManagementController::class, 'store'])->name('store');
        Route::post('/add-custom-domain', [TenantManagementController::class, 'addCustomDomain'])->name('add-custom-domain');
        Route::post('/migrate', [TenantManagementController::class, 'migrate'])->name('migrate');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

    Route::prefix('users')
        ->name('users')
        ->controller(UserController::class)
        ->group(function () {
            Route::get('', 'index');
            Route::post('', 'store')->name('-store');
            Route::patch('', 'update')->name('-update');
            Route::delete('', 'destroy')->name('-destroy');
        });

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
