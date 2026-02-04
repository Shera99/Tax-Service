<?php

declare(strict_types=1);

namespace Domain\Statistics\Repositories;

use Domain\Statistics\Entities\Statistic;

interface StatisticRepositoryInterface
{
    public function findById(int $id): ?Statistic;

    /**
     * @return Statistic[]
     */
    public function findAll(int $page = 1, int $perPage = 15): array;

    public function save(Statistic $statistic): Statistic;

    public function update(Statistic $statistic): Statistic;

    public function delete(int $id): bool;

    public function count(): int;
}
