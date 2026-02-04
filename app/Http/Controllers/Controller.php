<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Tax Service External API",
    description: "Внешний API для отправки статистики продаж билетов",
    contact: new OA\Contact(
        name: "API Support",
        email: "support@taxservice.local"
    )
)]
#[OA\Server(
    url: "/api/v1",
    description: "API Server"
)]
#[OA\SecurityScheme(
    securityScheme: "hmacAuth",
    type: "apiKey",
    in: "header",
    name: "Authorization",
    description: "HMAC авторизация. Формат: HMAC {public_key}:{signature}:{timestamp}"
)]
#[OA\Tag(
    name: "External",
    description: "API для внешних сервисов (HMAC авторизация)"
)]
abstract class Controller
{
    //
}
