<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statistics\StoreStatisticRequest;
use App\Http\Requests\Statistics\UpdateStatisticRequest;
use Application\Statistics\DTOs\CreateStatisticDTO;
use Application\Statistics\DTOs\UpdateStatisticDTO;
use Application\Statistics\UseCases\CreateStatisticUseCase;
use Application\Statistics\UseCases\DeleteStatisticUseCase;
use Application\Statistics\UseCases\GetAllStatisticsUseCase;
use Application\Statistics\UseCases\GetStatisticUseCase;
use Application\Statistics\UseCases\UpdateStatisticUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StatisticController extends Controller
{
    public function __construct(
        private readonly CreateStatisticUseCase $createStatisticUseCase,
        private readonly UpdateStatisticUseCase $updateStatisticUseCase,
        private readonly GetStatisticUseCase $getStatisticUseCase,
        private readonly GetAllStatisticsUseCase $getAllStatisticsUseCase,
        private readonly DeleteStatisticUseCase $deleteStatisticUseCase,
    ) {
    }

    /**
     * Display a listing of the statistics.
     */
    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 15);

        $result = $this->getAllStatisticsUseCase->execute($page, $perPage);

        return response()->json([
            'success' => true,
            'data' => $result['data'],
            'meta' => $result['meta'],
        ]);
    }

    /**
     * Store a newly created statistic in storage.
     */
    public function store(StoreStatisticRequest $request): JsonResponse
    {
        $dto = CreateStatisticDTO::fromArray($request->validated());
        $statistic = $this->createStatisticUseCase->execute($dto);

        return response()->json([
            'success' => true,
            'message' => 'Статистика успешно создана',
            'data' => $statistic->toArray(),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified statistic.
     */
    public function show(int $id): JsonResponse
    {
        $statistic = $this->getStatisticUseCase->execute($id);

        if ($statistic === null) {
            return response()->json([
                'success' => false,
                'message' => 'Статистика не найдена',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $statistic->toArray(),
        ]);
    }

    /**
     * Update the specified statistic in storage.
     */
    public function update(UpdateStatisticRequest $request, int $id): JsonResponse
    {
        $dto = UpdateStatisticDTO::fromArray($request->validated(), $id);
        $statistic = $this->updateStatisticUseCase->execute($dto);

        if ($statistic === null) {
            return response()->json([
                'success' => false,
                'message' => 'Статистика не найдена',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Статистика успешно обновлена',
            'data' => $statistic->toArray(),
        ]);
    }

    /**
     * Remove the specified statistic from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->deleteStatisticUseCase->execute($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Статистика не найдена',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Статистика успешно удалена',
        ]);
    }
}
