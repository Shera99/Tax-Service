<?php

declare(strict_types=1);

namespace Application\Statistics\UseCases;

use Domain\Statistics\Repositories\StatisticRepositoryInterface;

final class GetAllStatisticsUseCase
{
    public function __construct(
        private StatisticRepositoryInterface $repository,
    ) {
    }

    /**
     * @return array{data: array, meta: array}
     */
    public function execute(int $page = 1, int $perPage = 15): array
    {
        $statistics = $this->repository->findAll($page, $perPage);
        $total = $this->repository->count();

        return [
            'data' => array_map(fn($stat) => $stat->toArray(), $statistics),
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
            ],
        ];
    }
}
