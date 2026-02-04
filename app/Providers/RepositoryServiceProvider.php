<?php

namespace App\Providers;

use Application\Statistics\UseCases\CreateStatisticUseCase;
use Application\Statistics\UseCases\DeleteStatisticUseCase;
use Application\Statistics\UseCases\GetAllStatisticsUseCase;
use Application\Statistics\UseCases\GetStatisticUseCase;
use Application\Statistics\UseCases\UpdateStatisticUseCase;
use Domain\Statistics\Repositories\StatisticRepositoryInterface;
use Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentStatisticRepository;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(
            StatisticRepositoryInterface::class,
            EloquentStatisticRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        // Use Case bindings
        $this->app->bind(CreateStatisticUseCase::class, function ($app) {
            return new CreateStatisticUseCase(
                $app->make(StatisticRepositoryInterface::class)
            );
        });

        $this->app->bind(UpdateStatisticUseCase::class, function ($app) {
            return new UpdateStatisticUseCase(
                $app->make(StatisticRepositoryInterface::class)
            );
        });

        $this->app->bind(GetStatisticUseCase::class, function ($app) {
            return new GetStatisticUseCase(
                $app->make(StatisticRepositoryInterface::class)
            );
        });

        $this->app->bind(GetAllStatisticsUseCase::class, function ($app) {
            return new GetAllStatisticsUseCase(
                $app->make(StatisticRepositoryInterface::class)
            );
        });

        $this->app->bind(DeleteStatisticUseCase::class, function ($app) {
            return new DeleteStatisticUseCase(
                $app->make(StatisticRepositoryInterface::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
