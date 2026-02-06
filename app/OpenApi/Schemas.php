<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Statistic",
    title: "Statistic",
    description: "Модель статистики мероприятия",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "event_id", type: "integer", nullable: true, example: 123, description: "ID события во внешней системе"),
        new OA\Property(property: "session_id", type: "integer", nullable: true, example: 456, description: "ID сессии во внешней системе"),
        new OA\Property(property: "event_name", type: "string", example: "Концерт группы XYZ"),
        new OA\Property(property: "organization_name", type: "string", example: "ТОО Организатор"),
        new OA\Property(property: "venue_name", type: "string", nullable: true, example: "Дворец Республики", description: "Название площадки"),
        new OA\Property(property: "date_time", type: "string", format: "date-time", example: "2024-06-15 19:00:00"),
        new OA\Property(property: "total_tickets_available", type: "integer", example: 1000, description: "Всего билетов под продажу"),
        new OA\Property(property: "total_amount_sold", type: "number", format: "float", example: 150000.00, description: "Сумма продаж"),
        new OA\Property(property: "total_tickets_sold", type: "integer", example: 750, description: "Кол-во проданных билетов"),
        new OA\Property(property: "free_tickets_count", type: "integer", example: 200, description: "Кол-во непроданных билетов"),
        new OA\Property(property: "invitation_tickets_count", type: "integer", example: 50, description: "Кол-во пригласительных билетов"),
        new OA\Property(property: "refunded_tickets_count", type: "integer", example: 10, description: "Кол-во возвращенных билетов"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-15 10:30:00"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-15 10:30:00"),
    ]
)]
#[OA\Schema(
    schema: "CreateStatisticRequest",
    title: "CreateStatisticRequest",
    description: "Запрос на создание статистики",
    required: [
        "event_name",
        "organization_name",
        "date_time",
        "total_tickets_available",
        "total_amount_sold",
        "total_tickets_sold",
        "free_tickets_count",
        "invitation_tickets_count",
        "refunded_tickets_count"
    ],
    properties: [
        new OA\Property(property: "event_id", type: "integer", minimum: 1, nullable: true, example: 123, description: "ID события во внешней системе (опционально)"),
        new OA\Property(property: "session_id", type: "integer", minimum: 1, nullable: true, example: 456, description: "ID сессии во внешней системе (опционально)"),
        new OA\Property(property: "event_name", type: "string", maxLength: 255, example: "Концерт группы XYZ", description: "Название мероприятия"),
        new OA\Property(property: "organization_name", type: "string", maxLength: 255, example: "ТОО Организатор", description: "Название организации"),
        new OA\Property(property: "venue_name", type: "string", maxLength: 255, nullable: true, example: "Дворец Республики", description: "Название площадки (опционально)"),
        new OA\Property(property: "date_time", type: "string", format: "date-time", example: "2024-06-15 19:00:00", description: "Дата и время в формате Y-m-d H:i:s"),
        new OA\Property(property: "total_tickets_available", type: "integer", minimum: 0, example: 1000, description: "Всего билетов под продажу"),
        new OA\Property(property: "total_amount_sold", type: "number", format: "float", minimum: 0, example: 150000.00, description: "Сумма продаж"),
        new OA\Property(property: "total_tickets_sold", type: "integer", minimum: 0, example: 750, description: "Кол-во проданных билетов"),
        new OA\Property(property: "free_tickets_count", type: "integer", minimum: 0, example: 200, description: "Кол-во непроданных билетов"),
        new OA\Property(property: "invitation_tickets_count", type: "integer", minimum: 0, example: 50, description: "Кол-во пригласительных билетов"),
        new OA\Property(property: "refunded_tickets_count", type: "integer", minimum: 0, example: 10, description: "Кол-во возвращенных билетов"),
    ]
)]
class Schemas
{
    // This class is used only for OpenAPI schema definitions
}
