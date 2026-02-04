<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use DateTimeImmutable;
use Domain\Statistics\Entities\Statistic;
use Domain\Statistics\Repositories\StatisticRepositoryInterface;
use Infrastructure\Persistence\Eloquent\Models\StatisticModel;

final class EloquentStatisticRepository implements StatisticRepositoryInterface
{
    public function findById(int $id): ?Statistic
    {
        $model = StatisticModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    /**
     * @return Statistic[]
     */
    public function findAll(int $page = 1, int $perPage = 15): array
    {
        $models = StatisticModel::query()
            ->orderBy('date_time', 'desc')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return $models->map(fn($model) => $this->toEntity($model))->toArray();
    }

    public function save(Statistic $statistic): Statistic
    {
        $model = StatisticModel::create([
            'event_name' => $statistic->getEventName(),
            'organization_name' => $statistic->getOrganizationName(),
            'venue_name' => $statistic->getVenueName(),
            'date_time' => $statistic->getDateTime()->format('Y-m-d H:i:s'),
            'total_tickets_available' => $statistic->getTotalTicketsAvailable(),
            'total_amount_sold' => $statistic->getTotalAmountSold(),
            'total_tickets_sold' => $statistic->getTotalTicketsSold(),
            'free_tickets_count' => $statistic->getFreeTicketsCount(),
            'invitation_tickets_count' => $statistic->getInvitationTicketsCount(),
            'refunded_tickets_count' => $statistic->getRefundedTicketsCount(),
        ]);

        return $this->toEntity($model);
    }

    public function update(Statistic $statistic): Statistic
    {
        $model = StatisticModel::findOrFail($statistic->getId());

        $model->update([
            'event_name' => $statistic->getEventName(),
            'organization_name' => $statistic->getOrganizationName(),
            'venue_name' => $statistic->getVenueName(),
            'date_time' => $statistic->getDateTime()->format('Y-m-d H:i:s'),
            'total_tickets_available' => $statistic->getTotalTicketsAvailable(),
            'total_amount_sold' => $statistic->getTotalAmountSold(),
            'total_tickets_sold' => $statistic->getTotalTicketsSold(),
            'free_tickets_count' => $statistic->getFreeTicketsCount(),
            'invitation_tickets_count' => $statistic->getInvitationTicketsCount(),
            'refunded_tickets_count' => $statistic->getRefundedTicketsCount(),
        ]);

        return $this->toEntity($model->fresh());
    }

    public function delete(int $id): bool
    {
        return StatisticModel::destroy($id) > 0;
    }

    public function count(): int
    {
        return StatisticModel::count();
    }

    private function toEntity(StatisticModel $model): Statistic
    {
        return new Statistic(
            id: $model->id,
            eventName: $model->event_name,
            organizationName: $model->organization_name,
            venueName: $model->venue_name,
            dateTime: new DateTimeImmutable($model->date_time->format('Y-m-d H:i:s')),
            totalTicketsAvailable: $model->total_tickets_available,
            totalAmountSold: (float) $model->total_amount_sold,
            totalTicketsSold: $model->total_tickets_sold,
            freeTicketsCount: $model->free_tickets_count,
            invitationTicketsCount: $model->invitation_tickets_count,
            refundedTicketsCount: $model->refunded_tickets_count,
            createdAt: $model->created_at ? new DateTimeImmutable($model->created_at->format('Y-m-d H:i:s')) : null,
            updatedAt: $model->updated_at ? new DateTimeImmutable($model->updated_at->format('Y-m-d H:i:s')) : null,
        );
    }
}
