<template>
  <div id="app">
    <Navbar />
    <main class="container">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue';
</script>

<style>
/* ---------- Global Reset & Base ---------- */
*,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
    Ubuntu, Cantarell, sans-serif;
  background: #f4f6f9;
  color: #2c3e50;
  line-height: 1.6;
}

.container {
  max-width: 800px;
  margin: 2rem auto;
  padding: 0 1rem;
}

/* ---------- Shared UI ---------- */
.card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  padding: 1.5rem;
  margin-bottom: 1rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.5rem 1.2rem;
  border: none;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  transition: background 0.2s, opacity 0.2s;
}

.btn:hover {
  opacity: 0.9;
}

.btn-primary {
  background: #4a6cf7;
  color: #fff;
}

.btn-danger {
  background: #e74c3c;
  color: #fff;
}

.btn-secondary {
  background: #95a5a6;
  color: #fff;
}

.btn-success {
  background: #27ae60;
  color: #fff;
}

.btn-sm {
  padding: 0.3rem 0.7rem;
  font-size: 0.8rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  font-weight: 600;
  margin-bottom: 0.3rem;
  font-size: 0.9rem;
}

.form-control {
  width: 100%;
  padding: 0.6rem 0.8rem;
  border: 1px solid #dcdde1;
  border-radius: 6px;
  font-size: 0.95rem;
  transition: border-color 0.2s;
}

.form-control:focus {
  outline: none;
  border-color: #4a6cf7;
  box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.15);
}

textarea.form-control {
  min-height: 120px;
  resize: vertical;
}

.alert {
  padding: 0.75rem 1rem;
  border-radius: 6px;
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

.alert-error {
  background: #fdecea;
  color: #c0392b;
  border: 1px solid #f5c6cb;
}

.alert-success {
  background: #eafaf1;
  color: #1e8449;
  border: 1px solid #c3e6cb;
}

.form-error {
  color: #e74c3c;
  font-size: 0.8rem;
  margin-top: 0.2rem;
}

.page-title {
  font-size: 1.6rem;
  font-weight: 700;
  margin-bottom: 1.2rem;
}

.actions-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.empty-state {
  text-align: center;
  padding: 3rem 1rem;
  color: #95a5a6;
}

.empty-state p {
  margin-bottom: 1rem;
  font-size: 1.1rem;
}
</style>
