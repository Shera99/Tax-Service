<?php

declare(strict_types=1);

namespace Domain\Statistics\Entities;

use DateTimeImmutable;

final class Statistic
{
    public function __construct(
        private ?int $id,
        private string $eventName,
        private string $organizationName,
        private ?string $venueName,
        private DateTimeImmutable $dateTime,
        private int $totalTicketsAvailable,
        private float $totalAmountSold,
        private int $totalTicketsSold,
        private int $freeTicketsCount,
        private int $invitationTicketsCount,
        private int $refundedTicketsCount,
        private ?DateTimeImmutable $createdAt = null,
        private ?DateTimeImmutable $updatedAt = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getOrganizationName(): string
    {
        return $this->organizationName;
    }

    public function getVenueName(): ?string
    {
        return $this->venueName;
    }

    public function getDateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function getTotalTicketsAvailable(): int
    {
        return $this->totalTicketsAvailable;
    }

    public function getTotalAmountSold(): float
    {
        return $this->totalAmountSold;
    }

    public function getTotalTicketsSold(): int
    {
        return $this->totalTicketsSold;
    }

    public function getFreeTicketsCount(): int
    {
        return $this->freeTicketsCount;
    }

    public function getInvitationTicketsCount(): int
    {
        return $this->invitationTicketsCount;
    }

    public function getRefundedTicketsCount(): int
    {
        return $this->refundedTicketsCount;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'event_name' => $this->eventName,
            'organization_name' => $this->organizationName,
            'venue_name' => $this->venueName,
            'date_time' => $this->dateTime->format('Y-m-d H:i:s'),
            'total_tickets_available' => $this->totalTicketsAvailable,
            'total_amount_sold' => $this->totalAmountSold,
            'total_tickets_sold' => $this->totalTicketsSold,
            'free_tickets_count' => $this->freeTicketsCount,
            'invitation_tickets_count' => $this->invitationTicketsCount,
            'refunded_tickets_count' => $this->refundedTicketsCount,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
