<?php

declare(strict_types=1);

namespace Application\Statistics\UseCases;

use Application\Statistics\DTOs\CreateStatisticDTO;
use Domain\Statistics\Entities\Statistic;
use Domain\Statistics\Repositories\StatisticRepositoryInterface;

final class CreateStatisticUseCase
{
    public function __construct(
        private StatisticRepositoryInterface $repository,
    ) {
    }

    public function execute(CreateStatisticDTO $dto): Statistic
    {
        $statistic = new Statistic(
            id: null,
            eventName: $dto->eventName,
            organizationName: $dto->organizationName,
            dateTime: $dto->dateTime,
            totalTicketsAvailable: $dto->totalTicketsAvailable,
            totalAmountSold: $dto->totalAmountSold,
            totalTicketsSold: $dto->totalTicketsSold,
            freeTicketsCount: $dto->freeTicketsCount,
            invitationTicketsCount: $dto->invitationTicketsCount,
            refundedTicketsCount: $dto->refundedTicketsCount,
        );

        return $this->repository->save($statistic);
    }
}
