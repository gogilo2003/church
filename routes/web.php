<?php

use App\Http\Controllers\Central\Admin\CentralAuthController;
use App\Http\Controllers\Central\Admin\CentralDashboardController;
use App\Http\Controllers\Central\Admin\TenantManagementController;
use App\Http\Controllers\Central\RegisterTenantController;
use App\Http\Controllers\SmsController;
use App\Http\Middleware\EnsureCentralUser;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

// Central Domain Landing Page
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Central domain login alias
Route::get('/login', fn () => redirect()->route('central.admin.login'))->name('login');

// Central Tenant Self-Onboarding Routes
Route::prefix('register-tenant')->name('central.register-tenant.')->group(function () {
    Route::get('', [RegisterTenantController::class, 'create'])->name('create');
    Route::post('', [RegisterTenantController::class, 'store'])->name('store');
});
Route::get('/api/central/check-subdomain', [RegisterTenantController::class, 'checkSubdomain'])->name('central.check-subdomain');

// Central SMS Callback
Route::post('/messaging/sms-callback', [SmsController::class, 'callback']);

// Central Platform Staff Authentication Routes
Route::prefix('admin')->name('central.admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [CentralAuthController::class, 'create'])->name('login');
        Route::post('/login', [CentralAuthController::class, 'store'])->name('login.store');
    });

    Route::middleware(['auth', EnsureCentralUser::class])->group(function () {
        Route::post('/logout', [CentralAuthController::class, 'destroy'])->name('logout');
        Route::get('', CentralDashboardController::class)->name('dashboard');

        Route::prefix('tenants')->name('tenants.')->group(function () {
            Route::get('', [TenantManagementController::class, 'index'])->name('index');
            Route::post('', [TenantManagementController::class, 'store'])->name('store');
            Route::post('/add-custom-domain', [TenantManagementController::class, 'addCustomDomain'])->name('add-custom-domain');
            Route::post('/migrate', [TenantManagementController::class, 'migrate'])->name('migrate');
        });
    });
});
