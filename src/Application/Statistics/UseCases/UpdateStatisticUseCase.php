<?php

declare(strict_types=1);

namespace Application\Statistics\UseCases;

use Application\Statistics\DTOs\UpdateStatisticDTO;
use Domain\Statistics\Entities\Statistic;
use Domain\Statistics\Repositories\StatisticRepositoryInterface;

final class UpdateStatisticUseCase
{
    public function __construct(
        private StatisticRepositoryInterface $repository,
    ) {
    }

    public function execute(UpdateStatisticDTO $dto): ?Statistic
    {
        $existingStatistic = $this->repository->findById($dto->id);

        if ($existingStatistic === null) {
            return null;
        }

        $statistic = new Statistic(
            id: $dto->id,
            eventId: $dto->eventId,
            sessionId: $dto->sessionId,
            eventName: $dto->eventName,
            organizationName: $dto->organizationName,
            venueName: $dto->venueName,
            dateTime: $dto->dateTime,
            totalTicketsAvailable: $dto->totalTicketsAvailable,
            totalAmountSold: $dto->totalAmountSold,
            totalTicketsSold: $dto->totalTicketsSold,
            freeTicketsCount: $dto->freeTicketsCount,
            invitationTicketsCount: $dto->invitationTicketsCount,
            refundedTicketsCount: $dto->refundedTicketsCount,
        );

        return $this->repository->update($statistic);
    }
}
