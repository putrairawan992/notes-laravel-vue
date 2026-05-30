# Vue 3 Frontend — Notes App Setup

## Prerequisites
- Node.js 18+
- npm (or yarn / pnpm)
- Laravel 12 API running at http://localhost:8000

## Installation

```bash
cd vue-frontend

# 1. Konfigurasi environment (opsional, sudah ada default)
cp .env.example .env   # atau langsung edit .env yang sudah ada

npm install
npm run dev
```

The dev server will start at **http://localhost:3000**.

## Environment Configuration (`.env`)

File `.env` mengatur URL API backend:

```env
# Development — gunakan Vite proxy (default)
VITE_API_BASE_URL=http://localhost:8000/api

# Production — ganti dengan URL production
# VITE_API_BASE_URL=https://api.mydomain.com/api
```

Jika `VITE_API_BASE_URL` tidak diset, Axios client akan fallback ke `/api` (mengandalkan Vite proxy).

## Proxy Configuration

Vite's dev server proxies all `/api/*` requests to `http://localhost:8000/api/*`, so you don't need to worry about CORS during development. This is configured in `vite.config.js`.

## Project Structure

```
vue-frontend/
├── index.html
├── package.json
├── vite.config.js
├── public/
└── src/
    ├── main.js                  # App entry point
    ├── App.vue                  # Root component with global styles
    ├── api/
    │   └── client.js            # Axios instance with token management
    ├── router/
    │   └── index.js             # Vue Router with auth guards
    ├── stores/
    │   ├── auth.js              # Pinia auth store (login, register, logout)
    │   └── notes.js             # Pinia notes store (CRUD operations)
    ├── components/
    │   └── Navbar.vue           # Navigation bar
    └── views/
        ├── LoginView.vue        # Login form
        ├── RegisterView.vue     # Registration form
        ├── NotesListView.vue    # Notes list with search & pagination
        ├── NoteCreateView.vue   # Create new note form
        ├── NoteDetailView.vue   # View single note
        └── NoteEditView.vue     # Edit note form
```

## Features

- ✅ Vue 3 Composition API with `<script setup>`
- ✅ Pinia for state management
- ✅ Vue Router with protected route guards
- ✅ Axios interceptors for automatic token injection
- ✅ Search/filter notes by title
- ✅ Pagination
- ✅ Form validation error handling
- ✅ 401 auto-redirect to login
- ✅ Responsive, clean UI (no external UI library — pure CSS)

## Build for Production

```bash
npm run build
```

Output will be in the `dist/` folder. Serve it with any static file server or integrate with Laravel's `public/` directory.
