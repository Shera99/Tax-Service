<?php

declare(strict_types=1);

namespace Application\Statistics\UseCases;

use Domain\Statistics\Repositories\StatisticRepositoryInterface;

final class DeleteStatisticUseCase
{
    public function __construct(
        private StatisticRepositoryInterface $repository,
    ) {
    }

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
