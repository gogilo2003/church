<?php

namespace App\Providers;

use App\Events\PaymentRegistered;
use App\Listeners\UpdateContributionStatus;
use App\Repositories\Contracts\HouseholdRepositoryInterface;
use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\TenantUserRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\VisitorFollowUpRepositoryInterface;
use App\Repositories\Eloquent\HouseholdRepository;
use App\Repositories\Eloquent\MemberRepository;
use App\Repositories\Eloquent\RoleRepository;
use App\Repositories\Eloquent\TenantUserRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\VisitorFollowUpRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            RoleRepositoryInterface::class,
            RoleRepository::class
        );

        $this->app->bind(
            MemberRepositoryInterface::class,
            MemberRepository::class
        );

        $this->app->bind(
            HouseholdRepositoryInterface::class,
            HouseholdRepository::class
        );

        $this->app->bind(
            VisitorFollowUpRepositoryInterface::class,
            VisitorFollowUpRepository::class
        );

        $this->app->bind(
            TenantUserRepositoryInterface::class,
            TenantUserRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Schema::defaultStringLength(191);
        Event::listen(PaymentRegistered::class, UpdateContributionStatus::class);
    }
}
