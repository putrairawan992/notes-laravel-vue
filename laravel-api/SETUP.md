# Laravel REST API — Notes App Setup

## Prerequisites
- PHP 8.2+
- Composer
- MySQL / PostgreSQL / SQLite

> **Framework:** Laravel 12

## Quick Start

```bash
# 1. Install dependencies (Laravel + Sanctum)
composer install

# 2. Generate APP_KEY
php artisan key:generate

# 3. Sesuaikan .env (database, dll) — default sudah siap untuk MySQL
#    Edit file .env sesuai environment kamu

# 4. Jalankan migration
php artisan migrate

# 5. Jalankan server
php artisan serve
```

API siap diakses di **http://localhost:8000/api**.

## Konfigurasi .env

Pastikan pengaturan berikut di file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=notes_api
DB_USERNAME=root
DB_PASSWORD=

# Untuk Vue frontend (port 3000) + Sanctum SPA auth
SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

## Yang Sudah Ada di Project Ini

Semua file kustom sudah include — tidak perlu copy-paste:

| File | Keterangan |
|------|------------|
| `app/Models/Note.php` | Model Note dengan SoftDeletes + scope query |
| `app/Http/Controllers/Api/AuthController.php` | Register, login, logout, profile |
| `app/Http/Controllers/Api/NoteController.php` | Full CRUD notes dengan ownership check |
| `app/Http/Requests/StoreNoteRequest.php` | Validasi create note |
| `app/Http/Requests/UpdateNoteRequest.php` | Validasi update note |
| `database/migrations/*_create_notes_table.php` | Migration tabel notes |
| `routes/api.php` | Definisi route API |
| `config/sanctum.php` | Konfigurasi Sanctum |
| `config/cors.php` | CORS untuk Vue frontend |

## ⚠️ Yang Perlu Dilakukan Manual

Update **`app/Models/User.php`** — tambahkan trait `HasApiTokens`:
```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // ...
}
```

## API Endpoints

### Authentication
| Method | Endpoint        | Description          | Auth Required |
|--------|-----------------|----------------------|---------------|
| POST   | /api/register   | Register a new user  | No            |
| POST   | /api/login      | Login & get token    | No            |
| POST   | /api/logout     | Revoke token         | Yes           |
| GET    | /api/profile    | Get user profile     | Yes           |

### Notes
| Method | Endpoint         | Description              | Auth Required |
|--------|------------------|--------------------------|---------------|
| GET    | /api/notes       | List user's notes        | Yes           |
| POST   | /api/notes       | Create a new note        | Yes           |
| GET    | /api/notes/{id}  | View a single note       | Yes           |
| PUT    | /api/notes/{id}  | Update a note            | Yes           |
| DELETE | /api/notes/{id}  | Soft-delete a note       | Yes           |

### Query Parameters for GET /api/notes
- `search` — Filter notes by title (partial match)
- `per_page` — Pagination size (default: 15)

### Example Requests

**Register:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123","password_confirmation":"password123"}'
```

**Login:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'
```

**Create Note (use token from login):**
```bash
curl -X POST http://localhost:8000/api/notes \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{"title":"My First Note","content":"Hello world!"}'
```

## HTTP Status Codes Used
- `200` — Success (login, list, show, update, logout)
- `201` — Created (register, create note)
- `401` — Unauthenticated
- `403` — Forbidden
- `404` — Not found (note doesn't exist or doesn't belong to user)
- `422` — Validation error
