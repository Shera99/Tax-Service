.PHONY: help build up down restart logs shell migrate seed fresh test

# Default target
help:
	@echo "Available commands:"
	@echo "  make build      - Build Docker containers"
	@echo "  make up         - Start Docker containers"
	@echo "  make down       - Stop Docker containers"
	@echo "  make restart    - Restart Docker containers"
	@echo "  make logs       - View container logs"
	@echo "  make shell      - Access PHP container shell"
	@echo "  make migrate    - Run database migrations"
	@echo "  make seed       - Run database seeders"
	@echo "  make fresh      - Fresh migrations with seed"
	@echo "  make test       - Run tests"
	@echo "  make install    - Install dependencies and setup"

# Build containers
build:
	docker compose build

# Start containers
up:
	docker compose up -d

# Stop containers
down:
	docker compose down

# Restart containers
restart: down up

# View logs
logs:
	docker compose logs -f

# Access PHP container shell
shell:
	docker compose exec app bash

# Run migrations
migrate:
	docker compose exec app php artisan migrate

# Run seeders
seed:
	docker compose exec app php artisan db:seed

# Fresh migrations with seed
fresh:
	docker compose exec app php artisan migrate:fresh --seed

# Run tests
test:
	docker compose exec app php artisan test

# Full install and setup
install: build up
	docker compose exec app composer install
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate --seed
	@echo ""
	@echo "==================================="
	@echo "Installation complete!"
	@echo "API is available at: http://localhost:8080/api/v1"
	@echo ""
	@echo "Default admin credentials:"
	@echo "  Email: admin@taxservice.local"
	@echo "  Password: password"
	@echo "==================================="
