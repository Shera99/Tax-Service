<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authorization = $request->header('Authorization');

        if (!$authorization || !str_starts_with($authorization, 'HMAC ')) {
            return response()->json([
                'success' => false,
                'message' => 'Отсутствует заголовок Authorization. Формат: HMAC public_key:signature:timestamp',
            ], 401);
        }

        // Парсим Authorization: HMAC public_key:signature:timestamp
        $credentials = substr($authorization, 5); // Убираем "HMAC "
        $parts = explode(':', $credentials);

        if (count($parts) !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный формат Authorization. Ожидается: HMAC public_key:signature:timestamp',
            ], 401);
        }

        [$publicKey, $signature, $timestamp] = $parts;

        // Проверяем timestamp (не старше 5 минут)
        $requestTime = (int) $timestamp;
        $currentTime = time();

        if (abs($currentTime - $requestTime) > 300) {
            return response()->json([
                'success' => false,
                'message' => 'Запрос устарел. Timestamp не должен отличаться более чем на 5 минут.',
            ], 401);
        }

        // Находим API ключ
        $apiKey = ApiKey::where('public_key', $publicKey)
            ->where('is_active', true)
            ->first();

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Недействительный API ключ.',
            ], 401);
        }

        // Получаем тело запроса для проверки подписи
        $payload = $request->getContent() ?: '';

        // Проверяем подпись
        if (!$apiKey->verifySignature($signature, $payload, $timestamp)) {
            return response()->json([
                'success' => false,
                'message' => 'Неверная подпись запроса.',
            ], 401);
        }

        // Обновляем время последнего использования
        $apiKey->markAsUsed();

        // Добавляем API ключ в request для использования в контроллере
        $request->attributes->set('api_key', $apiKey);

        return $next($request);
    }
}
