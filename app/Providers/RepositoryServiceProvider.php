<?php

namespace App\Providers;

use App\Repositories\Contracts\EventRepositoryInterface;
use App\Repositories\Contracts\MatchRepositoryInterface;
use App\Repositories\Contracts\RegistrationRepositoryInterface;
use App\Repositories\Contracts\RewardClaimRepositoryInterface;
use App\Repositories\Contracts\SquadRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EventRepository;
use App\Repositories\Eloquent\MatchRepository;
use App\Repositories\Eloquent\RegistrationRepository;
use App\Repositories\Eloquent\RewardClaimRepository;
use App\Repositories\Eloquent\SquadRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
        $this->app->bind(SquadRepositoryInterface::class, SquadRepository::class);
        $this->app->bind(RegistrationRepositoryInterface::class, RegistrationRepository::class);
        $this->app->bind(MatchRepositoryInterface::class, MatchRepository::class);
        $this->app->bind(RewardClaimRepositoryInterface::class, RewardClaimRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
