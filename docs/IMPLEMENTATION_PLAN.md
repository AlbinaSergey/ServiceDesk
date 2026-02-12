# ERZSS ServiceDesk — что делать дальше

Документ отвечает на вопрос: **«что еще нужно сделать?»** после текущего skeleton.

## Текущее состояние (по факту репозитория)

Сделано:
- базовый bootstrap, DI, роутинг, layout;
- health endpoint;
- базовые security headers, ErrorHandler, EventBus.

Не сделано (критично):
- авторизация/сессии/RBAC/CSRF;
- модульный реестр и kill-switch;
- тикеты (MVP-ядро ServiceDesk).

---

## Приоритет №1 — закрыть Этап 0.2 (БД + миграции)

### 0.2.1 DatabaseService ✅ (сделано)
**Файлы:**
- `core/Services/DatabaseService.php`

**Критерий готовности:**
- singleton PDO;
- `utf8mb4`, исключения включены;
- DSN из `.env`.

### 0.2.2 MigrationService ✅ (сделано)
**Файлы:**
- `core/Services/MigrationService.php`
- `core/migrate.php`

**Критерий готовности:**
- сканирование `core/migrations/*.sql` + `modules/*/migrations/*.sql`;
- таблица `migrations` создаётся автоматически;
- каждая миграция выполняется в транзакции;
- повторный запуск не применяет уже выполненные.

### 0.2.3–0.2.13 Core SQL-миграции ✅ (базовый набор сделан)
**Папка:**
- `core/migrations/`

**Минимальный список:**
- `users`, `branches`, `settings`, `module_registry`, `auth_logs`, `audit_log`, `security_log`, `job_runs`, `feature_flags`, `seed_core`.

**Критерий готовности:**
- `php core/migrate.php` проходит без ошибок на пустой БД.

---

## Приоритет №2 — закрыть Этап 0.3 (core-сервисы) ← следующий шаг

1. `AuditService` ✅ (запись изменений в `audit_log`).
2. `SecurityLogger` ✅ (события безопасности в `security_log`).
3. `CacheService` ← следующий шаг (файловый cache для справочников).
4. `ServiceLocator` (межмодульные вызовы, без прямых SQL между модулями).
5. Расширение Error pages: 403/404/500 + единый стиль.

**Критерий готовности Этапа 0:**
- сайт открывается;
- миграции применяются;
- EventBus dispatch работает;
- ошибки и security-события логируются.

---

## Приоритет №3 — Этап 1.1 (авторизация по PIN)

### Обязательный минимум
- `SessionService`
- `AuthService`
- `RateLimiter`
- `AuthMiddleware`
- `CSRFMiddleware`
- `AuthController` + login/logout
- `themes/default/pages/login.php`

### Acceptance criteria
- PIN-вход (6 цифр) работает;
- после 5 неверных попыток включается блокировка;
- логин/логаут проверены smoke-тестом;
- записи об авторизации пишутся в `auth_logs`.

---

## Приоритет №4 — Этап 1.2 (RBAC)

- `RBACService` + `RBACMiddleware`;
- role-based меню;
- базовый админ CRUD: users/branches;
- запрет на доступ к чужим данным (403 + security log).

---

## Приоритет №5 — начало MVP (Этап 2)

Первые 2 спринта MVP:
1. Справочники + нумерация `DD.MM.YYYY-XXX`.
2. Создание заявки + регистрация + событие `ticket.registered`.

**Почему это важно:**
без этого нельзя проверить end-to-end ценность платформы (заявка от создания до обработки).

---

## Практичный план на ближайшие 10 рабочих дней

### Неделя 1
- День 1–2: DatabaseService + MigrationService + `core/migrate.php`.
- День 3: core-миграции + seed.
- День 4: AuditService + SecurityLogger.
- День 5: Smoke и фиксация в README/changelog.

### Неделя 2
- День 1–2: Session/Auth/RateLimiter.
- День 3: Auth/CSRF middleware + login page.
- День 4: RBACService + RBAC middleware.
- День 5: CRUD users/branches (минимум) + smoke.

---

## Definition of Done для каждого микро-шага

- код запускается локально;
- есть минимальный smoke-test командой;
- обновлён README или `docs/*`;
- нет regression по `/` и `/api/v1/health`.
