# Tax Service

Сервис для хранения и управления статистикой по продажам билетов. Построен на Laravel с использованием Clean Architecture.

## Требования

- Docker
- Docker Compose
- Make (опционально)

## Быстрый старт

```bash
# Клонирование и установка
make install

# Или вручную:
docker compose build
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

После установки:
- **Веб-интерфейс:** `http://localhost:8080`
- **API:** `http://localhost:8080/api/v1`
- **Swagger UI:** `http://localhost:8080/api/documentation`

## Учетные данные

### Администратор
- Email: `admin@taxservice.local`
- Password: `password`

### Инспектор
- Email: `tax@taxservice.local`
- Password: `password`

## Роли пользователей

| Роль | Описание | Доступ |
|------|----------|--------|
| `admin` | Администратор | Полный доступ: просмотр статистики, управление пользователями |
| `tax_officer` | Инспектор | Только просмотр статистики |

## Функционал веб-интерфейса

### Статистика (Dashboard)
- Таблица со всеми записями статистики
- Пагинация (20 записей на странице)
- Поиск по названию мероприятия, организации и площадки
- Фильтры по датам мероприятия (от/до)
- Сортировка по всем полям (дата мероприятия, дата создания, название, сумма, кол-во билетов)

### Управление пользователями (только admin)
- Создание новых пользователей
- Редактирование пользователей
- Удаление пользователей
- Назначение ролей

### Управление API ключами (только admin)
- Создание API ключей для внешних сервисов
- Активация/деактивация ключей
- Просмотр статистики использования
- Документация по интеграции

## Архитектура проекта

Проект использует Clean Architecture с разделением на слои:

```
src/
├── Domain/                 # Доменный слой (бизнес-логика)
│   ├── Statistics/
│   │   ├── Entities/       # Сущности
│   │   └── Repositories/   # Интерфейсы репозиториев
│   └── User/
│       ├── Entities/
│       └── Repositories/
├── Application/            # Слой приложения
│   └── Statistics/
│       ├── DTOs/           # Data Transfer Objects
│       └── UseCases/       # Use Cases (бизнес-сценарии)
└── Infrastructure/         # Инфраструктурный слой
    └── Persistence/
        └── Eloquent/
            ├── Models/     # Eloquent модели
            └── Repositories/ # Реализации репозиториев
```

## API Endpoints

### Аутентификация

| Метод | Endpoint | Описание |
|-------|----------|----------|
| POST | `/api/v1/login` | Авторизация |
| POST | `/api/v1/logout` | Выход (требует токен) |
| GET | `/api/v1/user` | Получить текущего пользователя |

### Внешний API (для сервисов - HMAC авторизация)

| Метод | Endpoint | Описание |
|-------|----------|----------|
| POST | `/api/v1/external/statistics` | Добавить статистику (HMAC) |

### Статистика (требует авторизации по токену)

| Метод | Endpoint | Описание |
|-------|----------|----------|
| GET | `/api/v1/statistics` | Список статистики |
| POST | `/api/v1/statistics` | Создать запись |
| GET | `/api/v1/statistics/{id}` | Получить запись |
| PUT | `/api/v1/statistics/{id}` | Обновить запись |
| DELETE | `/api/v1/statistics/{id}` | Удалить запись |

## Примеры API запросов

### Авторизация

```bash
curl -X POST http://localhost:8080/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@taxservice.local",
    "password": "password"
  }'
```

### Создание статистики

```bash
curl -X POST http://localhost:8080/api/v1/statistics \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "event_id": 123,
    "session_id": 456,
    "event_name": "Концерт группы XYZ",
    "organization_name": "ТОО Концерт",
    "venue_name": "Дворец Республики",
    "date_time": "2024-06-15 19:00:00",
    "total_tickets_available": 1000,
    "total_amount_sold": 150000.00,
    "total_tickets_sold": 750,
    "free_tickets_count": 200,
    "invitation_tickets_count": 50,
    "refunded_tickets_count": 10
  }'
```

### Получение списка с фильтрами

```bash
curl -X GET "http://localhost:8080/api/v1/statistics?page=1&per_page=20" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Внешний API (HMAC авторизация)

Для внешних сервисов используется авторизация по API ключам с HMAC подписью.

### Тестовые ключи

- **Public Key:** `pub_test_1234567890abcdef12345678`
- **Secret Key:** `sec_test_abcdef1234567890abcdef12`

### Формат Authorization заголовка

```
Authorization: HMAC {public_key}:{signature}:{timestamp}
```

### Создание подписи

```php
$timestamp = time();
$payload = json_encode($data); // тело запроса
$dataToSign = $timestamp . '.' . $payload;
$signature = hash_hmac('sha256', $dataToSign, $secretKey);
```

### Пример запроса с HMAC

```php
<?php
$publicKey = 'pub_test_1234567890abcdef12345678';
$secretKey = 'sec_test_abcdef1234567890abcdef12';

$data = [
    'event_id' => 123, // опционально
    'session_id' => 456, // опционально
    'event_name' => 'Концерт',
    'organization_name' => 'ТОО Организатор',
    'venue_name' => 'Дворец Республики', // опционально
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

$ch = curl_init('http://localhost:8080/api/v1/external/statistics');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "Authorization: HMAC {$publicKey}:{$signature}:{$timestamp}",
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
```

### Успешный ответ (201 Created)

```json
{
    "success": true,
    "message": "Статистика успешно добавлена",
    "data": {
        "id": 1,
        "event_id": 123,
        "session_id": 456,
        "event_name": "Концерт",
        "organization_name": "ТОО Организатор",
        "venue_name": "Дворец Республики",
        "date_time": "2024-06-15 19:00:00",
        "total_tickets_available": 1000,
        "total_amount_sold": 150000.00,
        "total_tickets_sold": 750,
        "free_tickets_count": 200,
        "invitation_tickets_count": 50,
        "refunded_tickets_count": 10,
        "created_at": "2024-01-15 10:30:00",
        "updated_at": "2024-01-15 10:30:00"
    }
}
```

### Ошибки авторизации (401 Unauthorized)

**Отсутствует заголовок Authorization:**
```json
{
    "success": false,
    "message": "Отсутствует заголовок Authorization. Формат: HMAC public_key:signature:timestamp"
}
```

**Неверный формат заголовка:**
```json
{
    "success": false,
    "message": "Неверный формат Authorization. Ожидается: HMAC public_key:signature:timestamp"
}
```

**Недействительный API ключ:**
```json
{
    "success": false,
    "message": "Недействительный API ключ."
}
```

**Неверная подпись:**
```json
{
    "success": false,
    "message": "Неверная подпись запроса."
}
```

**Запрос устарел (timestamp отличается более чем на 5 минут):**
```json
{
    "success": false,
    "message": "Запрос устарел. Timestamp не должен отличаться более чем на 5 минут."
}
```

### Ошибки валидации (422 Unprocessable Entity)

```json
{
    "message": "Название события обязательно (and 8 more errors)",
    "errors": {
        "event_name": ["Название события обязательно"],
        "organization_name": ["Название организации обязательно"],
        "date_time": ["Дата и время обязательны"],
        "total_tickets_available": ["Количество доступных билетов обязательно"],
        "total_amount_sold": ["Сумма продаж обязательна"],
        "total_tickets_sold": ["Количество проданных билетов обязательно"],
        "free_tickets_count": ["Количество непроданных билетов обязательно"],
        "invitation_tickets_count": ["Количество пригласительных билетов обязательно"],
        "refunded_tickets_count": ["Количество возвращенных билетов обязательно"]
    }
}
```

**Ошибка формата даты:**
```json
{
    "message": "Дата и время должны быть в формате Y-m-d H:i:s",
    "errors": {
        "date_time": ["Дата и время должны быть в формате Y-m-d H:i:s"]
    }
}
```

**Отрицательные значения:**
```json
{
    "message": "Количество проданных билетов не может быть отрицательным",
    "errors": {
        "total_tickets_sold": ["Количество проданных билетов не может быть отрицательным"],
        "total_amount_sold": ["Сумма продаж не может быть отрицательной"]
    }
}
```

## Структура таблицы Statistics

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint | ID записи |
| event_id | bigint | ID события во внешней системе (опционально) |
| session_id | bigint | ID сессии во внешней системе (опционально) |
| event_name | varchar(255) | Название события |
| organization_name | varchar(255) | Название организации |
| venue_name | varchar(255) | Название площадки (опционально) |
| date_time | datetime | Дата и время сессии |
| total_tickets_available | int | Всего билетов под продажу |
| total_amount_sold | decimal(12,2) | Сумма продаж |
| total_tickets_sold | int | Количество проданных билетов |
| free_tickets_count | int | Количество непроданных билетов |
| invitation_tickets_count | int | Количество пригласительных билетов |
| refunded_tickets_count | int | Количество возвращенных билетов |
| created_at | timestamp | Дата создания |
| updated_at | timestamp | Дата обновления |

## Команды Docker

```bash
make build      # Сборка контейнеров
make up         # Запуск контейнеров
make down       # Остановка контейнеров
make restart    # Перезапуск контейнеров
make logs       # Просмотр логов
make shell      # Доступ к shell PHP контейнера
make migrate    # Запуск миграций
make seed       # Запуск сидеров
make fresh      # Пересоздание БД с сидами
make test       # Запуск тестов
```

## Структура Docker

- **app** - PHP-FPM 8.4 контейнер
- **nginx** - Nginx веб-сервер (порт 8080)
- **db** - PostgreSQL 15 (порт 5432)

## База данных

Используется PostgreSQL 15. Параметры подключения:

- Host: `db` (внутри Docker) / `localhost` (снаружи)
- Port: `5432`
- Database: `tax_service`
- Username: `tax_user`
- Password: `secret`

## Тестирование

```bash
# Запуск всех тестов
make test

# Или напрямую
docker compose exec app php artisan test

# Запуск конкретного теста
docker compose exec app php artisan test --filter=ExternalStatisticApiTest
```

## Технологии

- **Backend:** Laravel 12, PHP 8.4
- **Database:** PostgreSQL 15
- **Frontend:** Tailwind CSS (CDN), Alpine.js
- **Auth API:** Laravel Sanctum
- **External API:** HMAC SHA-256 signature
- **API Docs:** L5-Swagger (OpenAPI)
- **Architecture:** Clean Architecture
- **Containerization:** Docker, Docker Compose
