<?php

declare(strict_types=1);

namespace Application\Statistics\DTOs;

use DateTimeImmutable;

final readonly class UpdateStatisticDTO
{
    public function __construct(
        public int $id,
        public string $eventName,
        public string $organizationName,
        public ?string $venueName,
        public DateTimeImmutable $dateTime,
        public int $totalTicketsAvailable,
        public float $totalAmountSold,
        public int $totalTicketsSold,
        public int $freeTicketsCount,
        public int $invitationTicketsCount,
        public int $refundedTicketsCount,
    ) {
    }

    public static function fromArray(array $data, int $id): self
    {
        return new self(
            id: $id,
            eventName: $data['event_name'],
            organizationName: $data['organization_name'],
            venueName: $data['venue_name'] ?? null,
            dateTime: new DateTimeImmutable($data['date_time']),
            totalTicketsAvailable: (int) $data['total_tickets_available'],
            totalAmountSold: (float) $data['total_amount_sold'],
            totalTicketsSold: (int) $data['total_tickets_sold'],
            freeTicketsCount: (int) $data['free_tickets_count'],
            invitationTicketsCount: (int) $data['invitation_tickets_count'],
            refundedTicketsCount: (int) $data['refunded_tickets_count'],
        );
    }
}
