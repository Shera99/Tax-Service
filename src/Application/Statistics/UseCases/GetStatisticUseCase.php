<?php

declare(strict_types=1);

namespace Application\Statistics\UseCases;

use Domain\Statistics\Entities\Statistic;
use Domain\Statistics\Repositories\StatisticRepositoryInterface;

final class GetStatisticUseCase
{
    public function __construct(
        private StatisticRepositoryInterface $repository,
    ) {
    }

    public function execute(int $id): ?Statistic
    {
        return $this->repository->findById($id);
    }
}
