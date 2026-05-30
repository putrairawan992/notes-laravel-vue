# Fullstack Developer — Take-Home Technical Assessment

**Candidate:** [Your Name] &nbsp;|&nbsp; **Date:** May 2026 &nbsp;|&nbsp; **Prepared for:** D&O Creative

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)
![Vue](https://img.shields.io/badge/Vue-3.x-4FC08D?logo=vuedotjs)
![WordPress](https://img.shields.io/badge/WordPress-6.5+-21759B?logo=wordpress)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql)

---

## 📋 Table of Contents

- [Project Overview](#-project-overview)
- [Prerequisites](#-prerequisites)
- [Quick Start (All Components)](#-quick-start-all-components)
- [Challenge 1 — Laravel REST API](#challenge-1--laravel-rest-api)
- [Challenge 2 — WordPress Plugin](#challenge-2--wordpress-plugin-user-department)
- [Challenge 3 — Vue 3 Frontend](#challenge-3--vue-3-frontend-bonus)
- [Challenge 4 — PHP Bug Hunt](#challenge-4--php-bug-hunt-bonus)
- [Architecture Decisions](#-architecture-decisions)
- [Testing](#-testing)
- [Deploy ke Railway](#-deploy-ke-railway)

---

## 🗂 Project Overview

This repository contains solutions for all four challenges:

| # | Challenge | Stack | Type |
|---|-----------|-------|------|
| **1** | Laravel REST API — Notes App | Laravel 12, Sanctum, MySQL | ✅ Required |
| **2** | WordPress Plugin — User Department | WordPress, User Meta API | ✅ Required |
| **3** | Vue 3 Frontend — Notes App | Vue 3, Pinia, Vite | ⭐ Bonus |
| **4** | PHP Bug Hunt — Logic Error | Pure PHP | ⭐ Bonus |

```
notes-laravel-vue/
├── README.md
├── CHALLENGE_4_BUG_HUNT.md
├── laravel-api/                 # Challenge 1
├── vue-frontend/                # Challenge 3
└── wordpress-plugin/            # Challenge 2
    └── user-department/
```

---

## 📦 Prerequisites

| Software | Version | Required For |
|----------|---------|-------------|
| PHP | **8.2+** | Laravel API |
| Composer | 2.x | Laravel dependencies |
| Node.js | **18+** | Vue frontend |
| npm | 9+ | Vue frontend |
| MySQL | 8.0+ (or SQLite) | Database |
| WordPress | 6.5+ | Plugin testing |

---

## 🚀 Quick Start (All Components)

### 1. Laravel API (Backend)

```bash
cd laravel-api
composer install
cp .env.example .env           # lalu edit DB credentials
php artisan key:generate
php artisan migrate
php artisan serve               # → http://localhost:8000
```

### 2. Vue Frontend

```bash
cd vue-frontend
cp .env.example .env            # sudah ada default
npm install
npm run dev                     # → http://localhost:3000
```

### 3. WordPress Plugin

```
Copy wordpress-plugin/user-department/ → wp-content/plugins/
Activate "User Department" di WordPress admin
```

> **Full per-component docs:** [laravel-api/SETUP.md](laravel-api/SETUP.md) &nbsp;|&nbsp; [vue-frontend/SETUP.md](vue-frontend/SETUP.md)

---

## 🔗 Architecture

```
┌──────────────────┐     Bearer Token      ┌──────────────────┐
│   Vue 3 SPA      │ ───────────────────▶  │   Laravel 12     │
│   localhost:3000  │                       │   localhost:8000 │
│                   │ ◀───────────────────  │                  │
│   Pinia + Router  │     JSON Responses    │   Sanctum Auth   │
└──────────────────┘                       └──────┬───────────┘
                                                  │
                                                  ▼
                                          ┌──────────────────┐
                                          │   MySQL / SQLite │
                                          │   notes_api DB   │
                                          └──────────────────┘
```

---

## Challenge 1 — Laravel REST API

RESTful API untuk personal notes app dengan token-based authentication via Laravel Sanctum.

### Setup .env

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=notes_api
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

### API Endpoints

#### Authentication

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `POST` | `/api/register` | Register new user | ❌ |
| `POST` | `/api/login` | Login, returns Bearer token | ❌ |
| `POST` | `/api/logout` | Revoke token | ✅ |
| `GET` | `/api/profile` | Get authenticated user | ✅ |

#### Notes (all require `Authorization: Bearer <token>`)

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/notes` | List user's notes (supports `?search=&per_page=`) |
| `POST` | `/api/notes` | Create note `{title, content}` |
| `GET` | `/api/notes/{id}` | View single note |
| `PUT` | `/api/notes/{id}` | Update note `{title, content}` |
| `DELETE` | `/api/notes/{id}` | Soft-delete note |

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| `200` | Success (login, list, show, update, logout) |
| `201` | Created (register, create note) |
| `401` | Unauthenticated / invalid credentials |
| `403` | Forbidden (not your note) |
| `404` | Note not found |
| `422` | Validation error |

### Quick cURL Examples

```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John","email":"john@example.com","password":"password123","password_confirmation":"password123"}'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'

# Create Note
curl -X POST http://localhost:8000/api/notes \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <TOKEN>" \
  -d '{"title":"My Note","content":"Hello world"}'
```

### Key Implementation Details

| Feature | Implementation |
|---------|---------------|
| Auth | Laravel Sanctum (token-based, not SPA cookies) |
| Authorization | `scopeForUser()` — users only see own notes |
| Validation | `StoreNoteRequest` / `UpdateNoteRequest` Form Requests |
| Soft Deletes | `SoftDeletes` trait on Note model |
| Search | `scopeSearchByTitle()` with `LIKE %...%` |
| Pagination | Default 15 per page, configurable via `?per_page=` |

---

## Challenge 2 — WordPress Plugin: User Department

Self-contained WordPress plugin — no manual database setup. All data via WordPress User Meta API.

### Features

| Feature | Description |
|---------|-------------|
| 🔽 Department field | Dropdown on user profile & edit-user pages |
| 💾 User meta storage | `get_user_meta()` / `update_user_meta()` |
| 📋 Users list column | Department displayed in admin Users table |
| 🔍 Filter by department | Dropdown filter above Users list |
| ⚙️ Settings page | Users → Departments — add/remove/edit departments |

### Default Departments

Management · Designer · Developer · SEO Team · Finance · Operations

### Installation

```
1. Copy user-department/ → wp-content/plugins/
2. Plugins → Activate "User Department"
3. Done — Users → Your Profile → Department
```

### Hooks Used

```php
add_action('show_user_profile', ...)      // Own profile
add_action('edit_user_profile', ...)      // Admin editing user
add_action('personal_options_update', ...) // Save own profile
add_action('edit_user_profile_update', ...)// Save edited user
add_filter('manage_users_columns', ...)    // Add column
add_filter('manage_users_custom_column', ...) // Render column
add_action('restrict_manage_users', ...)   // Filter dropdown
add_filter('pre_get_users', ...)           // Apply filter
add_action('admin_menu', ...)              // Settings page
add_action('admin_init', ...)              // Register settings
```

---

## Challenge 3 — Vue 3 Frontend (Bonus)

Single-page application connecting to the Laravel API via Axios.

### Project Structure

```
src/
├── api/client.js              # Axios instance + interceptors
├── router/index.js            # Vue Router with auth guards
├── stores/
│   ├── auth.js                # Pinia — login, register, logout
│   └── notes.js               # Pinia — notes CRUD
├── components/Navbar.vue      # Nav with auth-aware links
└── views/
    ├── LoginView.vue          # Login form
    ├── RegisterView.vue       # Registration form
    ├── NotesListView.vue      # Notes list + search + pagination
    ├── NoteCreateView.vue     # Create note form
    ├── NoteDetailView.vue     # View single note
    └── NoteEditView.vue       # Edit note form
```

### Tech Choices

| Choice | Reason |
|--------|--------|
| `axios` | Interceptors for automatic Bearer token + 401 handling |
| `pinia` | Lightweight, Composition API-friendly state management |
| `vue-router` | Navigation guards protect `/notes/*` routes |
| No UI library | Pure CSS — demonstrates CSS fundamentals, zero dependencies |

### Key Behaviors

- Token stored in `localStorage`, auto-attached via request interceptor
- `401` response → auto-clear token + redirect to `/login`
- Vite proxy forwards `/api/*` → `localhost:8000/api/*` in dev

---

## Challenge 4 — PHP Bug Hunt (Bonus)

**Full answer:** [CHALLENGE_4_BUG_HUNT.md](CHALLENGE_4_BUG_HUNT.md)

> **TL;DR:** The `array_filter` callback uses `||` (OR) — should be `&&` (AND).

```php
// ❌ BUG: Returns notes that belong to user OR are not deleted
return $note['user_id'] === $userId || !$note['is_deleted'];

// ✅ FIX: Returns notes that belong to user AND are not deleted
return $note['user_id'] === $userId && !$note['is_deleted'];
```

---

## 🧠 Architecture Decisions

| Decision | Why |
|----------|-----|
| **Token-based auth** (not SPA cookies) | Simpler, works with mobile/non-browser clients, no CSRF needed |
| **Laravel 12** | Latest stable, PHP 8.2 required, improved routing with `bootstrap/app.php` |
| **Form Requests** for validation | Keeps controllers clean, reusable validation rules |
| **Soft Deletes** on notes | Recoverable data, common real-world requirement |
| **Pure CSS in Vue** | No external dependency, demonstrates CSS skills |
| **Pinia over Vuex** | Official Vue 3 recommendation, better TypeScript support |
| **WordPress User Meta API** | No custom SQL tables, portable, follows WP coding standards |

---

## 🧪 Testing

### Laravel API

```bash
cd laravel-api
php artisan test
```

### Manual Smoke Test

```bash
# 1. Register
curl -X POST localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@test.com","password":"pass1234","password_confirmation":"pass1234"}'

# 2. Login (simpan token dari response)
curl -X POST localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"pass1234"}'

# 3. Create note
curl -X POST localhost:8000/api/notes \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <TOKEN>" \
  -d '{"title":"Test","content":"Works!"}'

# 4. List notes
curl localhost:8000/api/notes \
  -H "Authorization: Bearer <TOKEN>"
```

---

## 🚢 Deploy ke Railway

Monorepo ini bisa di-deploy ke [Railway](https://railway.app) sebagai **2 service** dalam 1 project.

### Struktur Deployment

```
Railway Project: notes-laravel-vue
├── Service: laravel-api     (PHP 8.2 + MySQL)
└── Service: vue-frontend    (Node.js 18)
```

### Step-by-Step

#### 1. Siapkan Railway Project

```bash
# Install Railway CLI (opsional)
npm i -g @railway/cli

# Login
railway login

# Init project di root repo
railway init
```

#### 2. Deploy Laravel API

Di **Railway Dashboard** → New Project → **Deploy from GitHub repo**:

| Setting | Value |
|---------|-------|
| **Root Directory** | `laravel-api` |
| **Build Command** | `composer install --no-dev --optimize-autoloader` |
| **Start Command** | `php -S 0.0.0.0:${PORT:-8000} -t public/ public/router.php` |

> Railway auto-detect PHP + Composer via Nixpacks. `Procfile` sudah disediakan sebagai fallback.

Tambahkan **MySQL** database dari Railway plugin, lalu set environment variables:

```env
APP_KEY=                    # Hasil dari php artisan key:generate --show
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

SANCTUM_STATEFUL_DOMAINS=   # Kosongkan (token-only auth)
SESSION_DOMAIN=
```

Lalu jalankan migration via Railway CLI:

```bash
railway run -s laravel-api -- php artisan migrate --force
```

#### 3. Deploy Vue Frontend

Di project yang sama → **New Service** → **Deploy from GitHub repo**:

| Setting | Value |
|---------|-------|
| **Root Directory** | `vue-frontend` |
| **Build Command** | `npm install && npm run build` |
| **Start Command** | `node server.js` |

Set environment variable:

```env
VITE_API_BASE_URL=https://laravel-api.up.railway.app/api
```

> Ganti `laravel-api.up.railway.app` dengan domain Railway Laravel API kamu.

#### 4. Setelah Deploy

```bash
# Cek status
railway status

# Buka dashboard
railway open
```

- **Laravel API** → `https://laravel-api.up.railway.app/api`
- **Vue Frontend** → `https://vue-frontend.up.railway.app`

### File Deployment yang Sudah Disiapkan

| File | Fungsi |
|------|--------|
| `laravel-api/Procfile` | Start command untuk Railway |
| `laravel-api/public/router.php` | URL rewriting untuk PHP built-in server |
| `vue-frontend/Procfile` | Start command untuk Railway |
| `vue-frontend/server.js` | Express static server untuk production build |
| `vue-frontend/package.json` | `express` dependency + `"start"` script |

### Catatan Produksi

- **Storage**: Railway menyediakan persistent volume untuk `storage/` — mount di `/app/storage`
- **Logs**: `storage/logs/laravel.log` — auto-rotate via Laravel
- **CORS**: `config/cors.php` sudah dikonfigurasi untuk production
- **Asset Build**: Laravel tidak perlu `npm run build` — ini pure API, tanpa Blade views

---

<p align="center"><sub>Built with ❤️ for D&O Creative Technical Assessment — May 2026</sub></p>

