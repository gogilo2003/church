<?php

namespace App\Providers;

use App\Events\PaymentRegistered;
use App\Listeners\UpdateContributionStatus;
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
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\RoleRepositoryInterface::class,
            \App\Repositories\Eloquent\RoleRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\MemberRepositoryInterface::class,
            \App\Repositories\Eloquent\MemberRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\HouseholdRepositoryInterface::class,
            \App\Repositories\Eloquent\HouseholdRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\VisitorFollowUpRepositoryInterface::class,
            \App\Repositories\Eloquent\VisitorFollowUpRepository::class
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
