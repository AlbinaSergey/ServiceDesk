# ERZSS ServiceDesk — skeleton

Минимальный, но уже рабочий каркас модульной ServiceDesk-платформы на PHP 8.1+.

## Что реализовано

- `public/index.php` + `.htaccess` (front controller);
- bootstrap приложения (`core/bootstrap/app.php`);
- загрузка окружения из `.env` (`core/bootstrap/env.php`);
- DI-контейнер (`core/Bootstrap/Container.php`);
- роутер с группами, middleware и **динамическими параметрами** (`core/Router.php`);
- security headers middleware;
- helpers: `jsonResponse`, `redirect`, `view`, `e`;
- `ErrorHandler` и `EventBus`;
- базовая тема: `home` + `error`.

## Роуты

- `GET /` — заглушка портала;
- `GET /api/v1/health` — health-check;
- `GET /api/v1/modules/{module}` — пример динамического роутинга.

## Быстрый запуск

```bash
cp .env.example .env
php -S 0.0.0.0:8080 -t public
```

Открыть:
- `http://localhost:8080/`
- `http://localhost:8080/api/v1/health`
- `http://localhost:8080/api/v1/modules/tickets`

## Миграции

```bash
cp .env.example .env
php core/migrate.php
```

## Следующий шаг

Этап 0.3: следующий микро-шаг — добавить `CacheService`, затем `ServiceLocator`.


## Что делать дальше

Подробный пошаговый план (от текущего skeleton к Этапу 2 MVP) см. в `docs/IMPLEMENTATION_PLAN.md`.
