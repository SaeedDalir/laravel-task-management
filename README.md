### Bitbarg Task Management API

A small Laravel 12 API for managing users and tasks, using JWT, PostgreSQL and Redis.

### Tech stack

- PHP 8.2+, Laravel 12
- JWT (`tymon/jwt-auth`)
- PostgreSQL
- Redis (cache)
- Docker / Docker Compose

### Features

- **Auth**
  - Register, login, refresh (`/api/users/auth/*`)
  - JWT guard named `user`

- **Authorization**
  - Roles: `admin`, `user` (`RoleEnum`)
  - Permissions in `config/permissions.php`
  - Policies for `User` and `Task`
  - `can:*` middleware on routes

- **Users**
  - Full CRUD on users (`/api/users`)
  - `UserResource` for responses
  - Redis cache:
    - `GET /api/users`
    - `GET /api/users/{id}`

- **Tasks**
  - CRUD on tasks (`/api/tasks`)
  - Owner (`user_id`) and assigned users (pivot table `task_users`)
  - Statuses: `pending`, `completed` (`TaskStatusEnum`)
  - Separate API for status change:
    - `PATCH /api/tasks/{task}/status`
  - Filtering and search:
    - `status`, `owner_id`, `assigned_user_id`,
    - `due_date_from`, `due_date_to`,
    - `created_from`, `created_to`,
    - `search` on `title`/`description`
  - `TaskVisibleForScope` → only owner/assigned (or admin) can see tasks
  - Redis cache for `GET /api/tasks` (per user + page + size + filter)

- **Activity log**
  - `ActivityOccurred` event and `ActivityLog` model
  - Logs for important actions (login, register, user/task CRUD, etc.)

### Setup (Docker)

1. Create env file:

```bash
cp .env.example .env
```

Configure PostgreSQL, Redis and JWT values in `.env`.

2. Start services with Docker Compose:

```bash
docker-compose up -d
```

3. Install PHP dependencies inside the app container:

```bash
docker-compose exec app composer install
```

4. Run migrations, generate app key and JWT secret inside the container:

```bash
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan jwt:secret
```

The API is exposed through Nginx in Docker (e.g. `http://localhost:8000`).

### Tests

Simple feature tests for Auth, User and Task:

```bash
docker-compose exec app php artisan test
```

### Response format

All endpoints return JSON in this shape:

```json
{
  "success": true,
  "code": 200,
  "message": "",
  "data": {},
  "meta": {}
}
```

Protected endpoints require a JWT token:

```http
Authorization: Bearer <token>
```
