# Установка Tax Service на сервер (без Docker)

Данная инструкция описывает ручную установку проекта на сервер с Ubuntu/Debian.

## Требования

- Ubuntu 20.04+ / Debian 11+
- PHP 8.4+
- PostgreSQL 15+
- Nginx
- Composer 2.x
- Git

## 1. Установка зависимостей

### Обновление системы

```bash
sudo apt update && sudo apt upgrade -y
```

### Установка PHP 8.4

```bash
# Добавление репозитория PHP
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Установка PHP и расширений
sudo apt install -y php8.4-fpm php8.4-cli php8.4-common \
    php8.4-pgsql php8.4-mbstring php8.4-xml php8.4-curl \
    php8.4-zip php8.4-bcmath php8.4-intl php8.4-readline
```

### Установка PostgreSQL 15

```bash
# Добавление репозитория PostgreSQL
sudo sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list'
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
sudo apt update

# Установка PostgreSQL
sudo apt install -y postgresql-15

# Запуск и автозапуск
sudo systemctl start postgresql
sudo systemctl enable postgresql
```

### Установка Nginx

```bash
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### Установка Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### Установка Git

```bash
sudo apt install -y git
```

## 2. Настройка PostgreSQL

### Создание базы данных и пользователя

```bash
sudo -u postgres psql
```

```sql
-- Создание пользователя
CREATE USER tax_user WITH PASSWORD 'your_secure_password';

-- Создание базы данных
CREATE DATABASE tax_service OWNER tax_user;

-- Предоставление прав
GRANT ALL PRIVILEGES ON DATABASE tax_service TO tax_user;

-- Выход
\q
```

## 3. Развертывание проекта

### Клонирование репозитория

```bash
cd /var/www
sudo git clone https://github.com/your-repo/tax-service.git
sudo chown -R www-data:www-data tax-service
cd tax-service
```

### Установка зависимостей

```bash
sudo -u www-data composer install --no-dev --optimize-autoloader
```

### Настройка окружения

```bash
# Копирование конфига
sudo -u www-data cp .env.example .env

# Редактирование конфига
sudo nano .env
```

Измените следующие параметры в `.env`:

```env
APP_NAME="Tax Service"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tax_service
DB_USERNAME=tax_user
DB_PASSWORD=your_secure_password

SESSION_DRIVER=database
CACHE_STORE=database
```

### Генерация ключа приложения

```bash
sudo -u www-data php artisan key:generate
```

### Запуск миграций

```bash
sudo -u www-data php artisan migrate --force
```

### Создание начальных данных (опционально)

```bash
# Создание администратора и тестовых данных
sudo -u www-data php artisan db:seed --force
```

Или создайте администратора вручную:

```bash
sudo -u www-data php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@yourdomain.com',
    'password' => Hash::make('your_secure_password'),
    'role' => 'admin',
]);
```

### Оптимизация для production

```bash
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan event:cache
```

### Настройка прав доступа

```bash
sudo chown -R www-data:www-data /var/www/tax-service
sudo chmod -R 755 /var/www/tax-service
sudo chmod -R 775 /var/www/tax-service/storage
sudo chmod -R 775 /var/www/tax-service/bootstrap/cache
```

## 4. Настройка Nginx

### Создание конфигурации сайта

```bash
sudo nano /etc/nginx/sites-available/tax-service
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    
    # Редирект на HTTPS (раскомментируйте после настройки SSL)
    # return 301 https://$server_name$request_uri;
    
    root /var/www/tax-service/public;
    index index.php;

    charset utf-8;

    # Логи
    access_log /var/log/nginx/tax-service-access.log;
    error_log /var/log/nginx/tax-service-error.log;

    # Основные правила
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Обработка PHP
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Запрет доступа к скрытым файлам
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Статические файлы
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Безопасность
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
```

### Включение сайта

```bash
sudo ln -s /etc/nginx/sites-available/tax-service /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 5. Настройка SSL (Let's Encrypt)

```bash
# Установка Certbot
sudo apt install -y certbot python3-certbot-nginx

# Получение сертификата
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Автоматическое обновление (уже настроено)
sudo systemctl status certbot.timer
```

После установки SSL, обновите конфигурацию Nginx:

```bash
sudo nano /etc/nginx/sites-available/tax-service
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.com www.your-domain.com;

    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    ssl_trusted_certificate /etc/letsencrypt/live/your-domain.com/chain.pem;

    # SSL настройки
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 1d;

    root /var/www/tax-service/public;
    index index.php;

    charset utf-8;

    access_log /var/log/nginx/tax-service-access.log;
    error_log /var/log/nginx/tax-service-error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
}
```

```bash
sudo nginx -t
sudo systemctl reload nginx
```

## 6. Настройка PHP-FPM

### Оптимизация настроек

```bash
sudo nano /etc/php/8.4/fpm/pool.d/www.conf
```

Рекомендуемые настройки для сервера с 2GB RAM:

```ini
pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 3
pm.max_spare_servers = 10
pm.max_requests = 500
```

```bash
sudo systemctl restart php8.4-fpm
```

## 7. Настройка Cron (планировщик задач)

```bash
sudo crontab -e -u www-data
```

Добавьте строку:

```
* * * * * cd /var/www/tax-service && php artisan schedule:run >> /dev/null 2>&1
```

## 8. Настройка очередей (опционально)

### Создание systemd сервиса

```bash
sudo nano /etc/systemd/system/tax-service-worker.service
```

```ini
[Unit]
Description=Tax Service Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
RestartSec=3
WorkingDirectory=/var/www/tax-service
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl daemon-reload
sudo systemctl enable tax-service-worker
sudo systemctl start tax-service-worker
```

## 9. Мониторинг и логи

### Просмотр логов приложения

```bash
tail -f /var/www/tax-service/storage/logs/laravel.log
```

### Просмотр логов Nginx

```bash
tail -f /var/log/nginx/tax-service-error.log
tail -f /var/log/nginx/tax-service-access.log
```

### Просмотр логов PHP-FPM

```bash
tail -f /var/log/php8.4-fpm.log
```

## 10. Обновление проекта

```bash
cd /var/www/tax-service

# Включение режима обслуживания
sudo -u www-data php artisan down

# Получение обновлений
sudo -u www-data git pull origin main

# Установка зависимостей
sudo -u www-data composer install --no-dev --optimize-autoloader

# Миграции
sudo -u www-data php artisan migrate --force

# Очистка кэша
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Выключение режима обслуживания
sudo -u www-data php artisan up
```

## 11. Резервное копирование

### Скрипт резервного копирования

```bash
sudo nano /usr/local/bin/backup-tax-service.sh
```

```bash
#!/bin/bash

BACKUP_DIR="/var/backups/tax-service"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="tax_service"
DB_USER="tax_user"

mkdir -p $BACKUP_DIR

# Бэкап базы данных
PGPASSWORD="your_secure_password" pg_dump -U $DB_USER -h localhost $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Бэкап файлов (storage)
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz -C /var/www/tax-service storage

# Удаление старых бэкапов (старше 30 дней)
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completed: $DATE"
```

```bash
sudo chmod +x /usr/local/bin/backup-tax-service.sh

# Добавление в cron (ежедневно в 3:00)
sudo crontab -e
```

```
0 3 * * * /usr/local/bin/backup-tax-service.sh >> /var/log/backup-tax-service.log 2>&1
```

## Проверка работоспособности

После установки проверьте:

1. **Веб-интерфейс:** `https://your-domain.com`
2. **API:** `https://your-domain.com/api/v1`
3. **Swagger:** `https://your-domain.com/api/documentation`

### Swagger пустая страница

Если Swagger UI показывает пустую страницу, выполните следующие шаги:

```bash
cd /var/www/tax-service

# 1. Публикация assets Swagger UI
sudo -u www-data php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"

# 2. Генерация документации
sudo -u www-data php artisan l5-swagger:generate

# 3. Очистка кэша
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache

# 4. Проверка прав на папку storage/api-docs
sudo chmod -R 775 /var/www/tax-service/storage/api-docs

# 5. Создание символической ссылки на storage (если не создана)
sudo -u www-data php artisan storage:link
```

Также убедитесь, что в `.env` установлены правильные значения:

```
APP_URL=https://your-domain.com
L5_SWAGGER_CONST_HOST=https://your-domain.com
```

## Устранение неполадок

### 502 Bad Gateway

```bash
# Проверка статуса PHP-FPM
sudo systemctl status php8.4-fpm

# Перезапуск
sudo systemctl restart php8.4-fpm
```

### Permission denied

```bash
sudo chown -R www-data:www-data /var/www/tax-service
sudo chmod -R 775 /var/www/tax-service/storage
```

### Ошибки подключения к БД

```bash
# Проверка PostgreSQL
sudo systemctl status postgresql

# Проверка подключения
psql -U tax_user -h localhost -d tax_service
```
