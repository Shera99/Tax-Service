<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statistics\StoreStatisticRequest;
use Application\Statistics\DTOs\CreateStatisticDTO;
use Application\Statistics\UseCases\CreateStatisticUseCase;
use Illuminate\Http\JsonResponse;
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
