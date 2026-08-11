<?php

use App\Http\Controllers\Central\Admin\CentralAuthController;
use App\Http\Controllers\Central\Admin\CentralDashboardController;
use App\Http\Controllers\Central\Admin\TenantManagementController;
use App\Http\Controllers\Central\RegisterTenantController;
use App\Http\Controllers\SmsController;
use App\Http\Middleware\EnsureCentralDomain;
use App\Http\Middleware\EnsureCentralUser;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Central Domain Routes (Protected by EnsureCentralDomain middleware)
$centralDomains = array_unique(array_filter(config('tenancy.central_domains')));

foreach ($centralDomains as $domain) {
    Route::domain($domain)->middleware(EnsureCentralDomain::class)->group(function () {
        // Central Domain Landing Page
        Route::get('/', function () {
            return Inertia::render('Welcome', [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
                'laravelVersion' => Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
        })->name('welcome');

        // Central domain login alias
        Route::get('/login', fn() => redirect()->route('central.admin.login'))->name('central.login');

        // Central Tenant Self-Onboarding Routes
        Route::prefix('register-tenant')->name('central.register-tenant.')->group(function () {
            Route::get('', [RegisterTenantController::class, 'create'])->name('create');
            Route::post('', [RegisterTenantController::class, 'store'])->name('store');
        });
        Route::get('/api/central/check-subdomain', [RegisterTenantController::class, 'checkSubdomain'])->name('central.check-subdomain');

        Route::get('/onboarding/wizard', [\App\Http\Controllers\OnboardingWizardController::class, 'wizard'])->name('central.onboarding.wizard');
        Route::post('/api/central/apply-preset', [\App\Http\Controllers\OnboardingWizardController::class, 'applyPreset'])->name('central.onboarding.apply-preset');

        // Central SMS Callback
        Route::post('/messaging/sms-callback', [SmsController::class, 'callback']);

        Route::middleware(['auth'])->group(function () {
            Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
        });

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
                    Route::put('/{tenant}', [TenantManagementController::class, 'update'])->name('update');
                    Route::post('/add-custom-domain', [TenantManagementController::class, 'addCustomDomain'])->name('add-custom-domain');
                    Route::post('/migrate', [TenantManagementController::class, 'migrate'])->name('migrate');
                });
            });
        });
    });
}

require __DIR__ . '/auth.php';
