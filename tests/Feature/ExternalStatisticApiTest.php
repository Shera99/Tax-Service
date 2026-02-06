<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExternalStatisticApiTest extends TestCase
{
    use RefreshDatabase;

    private string $publicKey;
    private string $secretKey;
    private ApiKey $apiKey;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем API ключ для тестов
        $this->publicKey = 'pub_test_' . fake()->uuid();
        $this->secretKey = 'sec_test_' . fake()->uuid();

        $this->apiKey = ApiKey::create([
            'name' => 'Test API Key',
            'public_key' => $this->publicKey,
            'secret_key' => $this->secretKey,
            'is_active' => true,
        ]);
    }

    /**
     * Создает Authorization header с HMAC подписью.
     */
    private function createAuthHeader(string $payload, ?int $timestamp = null, ?string $publicKey = null, ?string $secretKey = null): string
    {
        $timestamp = $timestamp ?? time();
        $publicKey = $publicKey ?? $this->publicKey;
        $secretKey = $secretKey ?? $this->secretKey;

        $signature = ApiKey::createSignature($secretKey, $payload, (string) $timestamp);

        return "HMAC {$publicKey}:{$signature}:{$timestamp}";
    }

    /**
     * Возвращает валидные данные статистики.
     */
    private function getValidStatisticData(): array
    {
        return [
            'event_id' => 123,
            'session_id' => 456,
            'event_name' => 'Концерт группы Test',
            'organization_name' => 'ТОО Тестовая организация',
            'venue_name' => 'Дворец Республики',
            'date_time' => '2024-06-15 19:00:00',
            'total_tickets_available' => 1000,
            'total_amount_sold' => 150000.00,
            'total_tickets_sold' => 750,
            'free_tickets_count' => 200,
            'invitation_tickets_count' => 50,
            'refunded_tickets_count' => 10,
        ];
    }

    #[Test]
    public function it_creates_statistic_with_valid_hmac_signature(): void
    {
        $data = $this->getValidStatisticData();
        $payload = json_encode($data);

        $response = $this->postJson('/api/v1/external/statistics', $data, [
            'Authorization' => $this->createAuthHeader($payload),
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Статистика успешно добавлена',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'event_id',
                    'session_id',
                    'event_name',
                    'organization_name',
                    'venue_name',
                    'date_time',
                    'total_tickets_available',
                    'total_amount_sold',
                    'total_tickets_sold',
                    'free_tickets_count',
                    'invitation_tickets_count',
                    'refunded_tickets_count',
                ],
            ]);

        $this->assertDatabaseHas('statistics', [
            'event_name' => 'Концерт группы Test',
            'organization_name' => 'ТОО Тестовая организация',
        ]);
    }

    #[Test]
    public function it_rejects_request_without_authorization_header(): void
    {
        $response = $this->postJson('/api/v1/external/statistics', $this->getValidStatisticData());

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Отсутствует заголовок Authorization. Формат: HMAC public_key:signature:timestamp',
            ]);
    }

    #[Test]
    public function it_rejects_request_with_invalid_authorization_format(): void
    {
        $response = $this->postJson('/api/v1/external/statistics', $this->getValidStatisticData(), [
            'Authorization' => 'Bearer some_token',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Отсутствует заголовок Authorization. Формат: HMAC public_key:signature:timestamp',
            ]);
    }

    #[Test]
    public function it_rejects_request_with_malformed_hmac_header(): void
    {
        $response = $this->postJson('/api/v1/external/statistics', $this->getValidStatisticData(), [
            'Authorization' => 'HMAC invalid_format',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Неверный формат Authorization. Ожидается: HMAC public_key:signature:timestamp',
            ]);
    }

    #[Test]
    public function it_rejects_request_with_expired_timestamp(): void
    {
        $data = $this->getValidStatisticData();
        $payload = json_encode($data);
        $expiredTimestamp = time() - 400; // 6+ минут назад

        $response = $this->postJson('/api/v1/external/statistics', $data, [
            'Authorization' => $this->createAuthHeader($payload, $expiredTimestamp),
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Запрос устарел. Timestamp не должен отличаться более чем на 5 минут.',
            ]);
    }

    #[Test]
    public function it_rejects_request_with_future_timestamp(): void
    {
        $data = $this->getValidStatisticData();
        $payload = json_encode($data);
        $futureTimestamp = time() + 400; // 6+ минут в будущем

        $response = $this->postJson('/api/v1/external/statistics', $data, [
            'Authorization' => $this->createAuthHeader($payload, $futureTimestamp),
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Запрос устарел. Timestamp не должен отличаться более чем на 5 минут.',
            ]);
    }

    #[Test]
    public function it_rejects_request_with_invalid_public_key(): void
    {
        $data = $this->getValidStatisticData();
        $payload = json_encode($data);

        $response = $this->postJson('/api/v1/external/statistics', $data, [
            'Authorization' => $this->createAuthHeader($payload, null, 'pub_invalid_key'),
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Недействительный API ключ.',
            ]);
    }

    #[Test]
    public function it_rejects_request_with_inactive_api_key(): void
    {
        $this->apiKey->update(['is_active' => false]);

        $data = $this->getValidStatisticData();
        $payload = json_encode($data);

        $response = $this->postJson('/api/v1/external/statistics', $data, [
            'Authorization' => $this->createAuthHeader($payload),
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Недействительный API ключ.',
            ]);
    }

    #[Test]
    public function it_rejects_request_with_invalid_signature(): void
    {
        $data = $this->getValidStatisticData();
        $payload = json_encode($data);
        $timestamp = time();

        // Используем неправильный secret key для создания подписи
        $wrongSignature = ApiKey::createSignature('wrong_secret_key', $payload, (string) $timestamp);

        $response = $this->postJson('/api/v1/external/statistics', $data, [
            'Authorization' => "HMAC {$this->publicKey}:{$wrongSignature}:{$timestamp}",
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Неверная подпись запроса.',
            ]);
    }

    #[Test]
    public function it_rejects_request_with_tampered_payload(): void
    {
        $data = $this->getValidStatisticData();
        $payload = json_encode($data);

        // Создаем подпись для оригинальных данных
        $authHeader = $this->createAuthHeader($payload);

        // Изменяем данные после создания подписи
        $data['total_amount_sold'] = 999999.99;

        $response = $this->postJson('/api/v1/external/statistics', $data, [
            'Authorization' => $authHeader,
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Неверная подпись запроса.',
            ]);
    }

    #[Test]
    public function it_updates_last_used_at_on_successful_request(): void
    {
        $this->assertNull($this->apiKey->last_used_at);

        $data = $this->getValidStatisticData();
        $payload = json_encode($data);

        $response = $this->postJson('/api/v1/external/statistics', $data, [
            'Authorization' => $this->createAuthHeader($payload),
        ]);

        $response->assertStatus(201);

        $this->apiKey->refresh();
        $this->assertNotNull($this->apiKey->last_used_at);
    }

    #[Test]
    public function it_validates_required_fields(): void
    {
        $data = []; // Пустые данные
        $payload = json_encode($data);

        $response = $this->postJson('/api/v1/external/statistics', $data, [
            'Authorization' => $this->createAuthHeader($payload),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'event_name',
                'organization_name',
                'date_time',
                'total_tickets_available',
                'total_amount_sold',
                'total_tickets_sold',
                'free_tickets_count',
                'invitation_tickets_count',
                'refunded_tickets_count',
            ]);
    }

    #[Test]
    public function it_validates_event_name_max_length(): void
    {
        $data = $this->getValidStatisticData();
        $data['event_name'] = str_repeat('a', 256);
        $payload = json_encode($data);

        $response = $this->postJson('/api/v1/external/statistics', $data, [
            'Authorization' => $this->createAuthHeader($payload),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['event_name']);
    }

    #[Test]
    public function it_validates_date_time_format(): void
    {
        $data = $this->getValidStatisticData();
        $data['date_time'] = 'invalid-date';
        $payload = json_encode($data);

        $response = $this->postJson('/api/v1/external/statistics', $data, [
            'Authorization' => $this->createAuthHeader($payload),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date_time']);
    }

    #[Test]
    public function it_validates_numeric_fields_are_non_negative(): void
    {
        $data = $this->getValidStatisticData();
        $data['total_tickets_sold'] = -1;
        $data['total_amount_sold'] = -100;
        $payload = json_encode($data);

        $response = $this->postJson('/api/v1/external/statistics', $data, [
            'Authorization' => $this->createAuthHeader($payload),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['total_tickets_sold', 'total_amount_sold']);
    }

    #[Test]
    public function it_accepts_request_within_5_minute_window(): void
    {
        $data = $this->getValidStatisticData();
        $payload = json_encode($data);
        $validTimestamp = time() - 290; // ~4.8 минуты назад (в пределах допустимого)

        $response = $this->postJson('/api/v1/external/statistics', $data, [
            'Authorization' => $this->createAuthHeader($payload, $validTimestamp),
        ]);

        $response->assertStatus(201);
    }
}
