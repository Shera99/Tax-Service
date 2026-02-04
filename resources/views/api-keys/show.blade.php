@extends('layouts.app')

@section('title', 'API Ключ: ' . $apiKey->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('api-keys.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Назад к списку
        </a>
        <h1 class="text-2xl font-bold text-gray-800 mt-4">
            <i class="fas fa-key text-blue-500 mr-2"></i>{{ $apiKey->name }}
        </h1>
    </div>

    @if(session('secret_key'))
    <!-- Secret Key Alert (shown only once after creation) -->
    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
            </div>
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-semibold text-green-800">API ключ успешно создан!</h3>
                <p class="mt-1 text-sm text-green-700">Сохраните секретный ключ прямо сейчас. Он больше не будет показан!</p>

                <div class="mt-4 bg-white border border-green-300 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Секретный ключ (Secret Key)</label>
                    <div class="flex items-center space-x-2">
                        <code id="secret-key" class="flex-1 bg-gray-100 px-3 py-2 rounded text-sm font-mono break-all">{{ session('secret_key') }}</code>
                        <button onclick="copyToClipboard('secret-key')" class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Key Details -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Информация о ключе</h2>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Название</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $apiKey->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Статус</dt>
                <dd class="mt-1">
                    @if($apiKey->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>Активен
                    </span>
                    @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times-circle mr-1"></i>Неактивен
                    </span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Публичный ключ (Public Key)</dt>
                <dd class="mt-1">
                    <div class="flex items-center space-x-2">
                        <code id="public-key" class="bg-gray-100 px-2 py-1 rounded text-sm font-mono">{{ $apiKey->public_key }}</code>
                        <button onclick="copyToClipboard('public-key')" class="p-1 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Последнее использование</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $apiKey->last_used_at ? $apiKey->last_used_at->format('d.m.Y H:i:s') : 'Никогда' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Создан</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $apiKey->created_at->format('d.m.Y H:i:s') }}</dd>
            </div>
        </dl>
    </div>

    <!-- Usage Instructions -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Как использовать</h2>

        <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">1. Формат Authorization заголовка</h3>
                <code class="text-sm bg-white px-3 py-2 rounded border block">Authorization: HMAC {public_key}:{signature}:{timestamp}</code>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">2. Как создать подпись (signature)</h3>
                <pre class="text-sm bg-white px-3 py-2 rounded border overflow-x-auto"><code>// PHP пример
$timestamp = time();
$payload = json_encode($data); // тело запроса
$dataToSign = $timestamp . '.' . $payload;
$signature = hash_hmac('sha256', $dataToSign, $secretKey);</code></pre>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">3. Пример запроса (cURL)</h3>
                <pre class="text-sm bg-white px-3 py-2 rounded border overflow-x-auto"><code>curl -X POST {{ url('/api/v1/external/statistics') }} \
  -H "Content-Type: application/json" \
  -H "Authorization: HMAC {{ $apiKey->public_key }}:{signature}:{timestamp}" \
  -d '{
    "event_name": "Концерт",
    "organization_name": "ТОО Организатор",
    "date_time": "2024-06-15 19:00:00",
    "total_tickets_available": 1000,
    "total_amount_sold": 150000.00,
    "total_tickets_sold": 750,
    "free_tickets_count": 200,
    "invitation_tickets_count": 50,
    "refunded_tickets_count": 10
  }'</code></pre>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">4. Полный PHP пример</h3>
                <pre class="text-sm bg-white px-3 py-2 rounded border overflow-x-auto"><code>&lt;?php
$publicKey = '{{ $apiKey->public_key }}';
$secretKey = 'YOUR_SECRET_KEY'; // Секретный ключ

$data = [
    'event_name' => 'Концерт',
    'organization_name' => 'ТОО Организатор',
    'date_time' => '2024-06-15 19:00:00',
    'total_tickets_available' => 1000,
    'total_amount_sold' => 150000.00,
    'total_tickets_sold' => 750,
    'free_tickets_count' => 200,
    'invitation_tickets_count' => 50,
    'refunded_tickets_count' => 10,
];

$timestamp = time();
$payload = json_encode($data);
$signature = hash_hmac('sha256', $timestamp . '.' . $payload, $secretKey);

$ch = curl_init('{{ url('/api/v1/external/statistics') }}');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "Authorization: HMAC {$publicKey}:{$signature}:{$timestamp}",
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

echo $response;</code></pre>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(elementId) {
    const text = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert('Скопировано в буфер обмена!');
    });
}
</script>
@endsection
