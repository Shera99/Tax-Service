<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statistics\StoreStatisticRequest;
use Application\Statistics\DTOs\CreateStatisticDTO;
use Application\Statistics\UseCases\CreateStatisticUseCase;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for external services to submit statistics.
 * Uses API key authentication (HMAC signature).
 */
class ExternalStatisticController extends Controller
{
    public function __construct(
        private readonly CreateStatisticUseCase $createStatisticUseCase,
    ) {
    }

    /**
     * Store a newly created statistic from external service.
     */
    #[OA\Post(
        path: "/external/statistics",
        summary: "Добавить статистику (внешний сервис)",
        description: "Создание записи статистики от внешнего сервиса с HMAC авторизацией",
        tags: ["External"],
        security: [["hmacAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CreateStatisticRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Статистика успешно создана",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Статистика успешно добавлена"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Statistic")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Ошибка авторизации",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Неверная подпись запроса")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ошибка валидации"),
                        new OA\Property(property: "errors", type: "object")
                    ]
                )
            )
        ]
    )]
    public function store(StoreStatisticRequest $request): JsonResponse
    {
        $dto = CreateStatisticDTO::fromArray($request->validated());
        $statistic = $this->createStatisticUseCase->execute($dto);

        return response()->json([
            'success' => true,
            'message' => 'Статистика успешно добавлена',
            'data' => $statistic->toArray(),
        ], Response::HTTP_CREATED);
    }
}
