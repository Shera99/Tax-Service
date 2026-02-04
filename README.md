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

## Учетные данные

### Администратор
- Email: `admin@taxservice.local`
- Password: `password`

### Налоговик
- Email: `tax@taxservice.local`
- Password: `password`

## Роли пользователей

| Роль | Описание | Доступ |
|------|----------|--------|
| `admin` | Администратор | Полный доступ: просмотр статистики, управление пользователями |
| `tax_officer` | Налоговик | Только просмотр статистики |

## Функционал веб-интерфейса

### Статистика (Dashboard)
- Таблица со всеми записями статистики
- Пагинация (20 записей на странице)
- Поиск по названию мероприятия и организации
- Фильтры по датам (от/до)
- Сортировка по всем полям (дата, название, сумма, кол-во билетов)

### Управление пользователями (только admin)
- Создание новых пользователей
- Редактирование пользователей
- Удаление пользователей
- Назначение ролей

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

### Статистика (требует авторизации)

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
    "event_name": "Концерт группы XYZ",
    "organization_name": "ТОО Концерт",
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

## Структура таблицы Statistics

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint | ID записи |
| event_name | varchar(255) | Название события |
| organization_name | varchar(255) | Название организации |
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

- **app** - PHP-FPM 8.3 контейнер
- **nginx** - Nginx веб-сервер (порт 8080)
- **db** - PostgreSQL 15 (порт 5432)

## База данных

Используется PostgreSQL 15. Параметры подключения:

- Host: `db` (внутри Docker) / `localhost` (снаружи)
- Port: `5432`
- Database: `tax_service`
- Username: `tax_user`
- Password: `secret`

## Технологии

- **Backend:** Laravel 12, PHP 8.3
- **Database:** PostgreSQL 15
- **Frontend:** Tailwind CSS (CDN), Alpine.js
- **Auth API:** Laravel Sanctum
- **Architecture:** Clean Architecture
- **Containerization:** Docker, Docker Compose
