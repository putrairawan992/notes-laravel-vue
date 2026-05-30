<template>
  <nav class="navbar">
    <div class="navbar-inner">
      <router-link to="/notes" class="navbar-brand">📝 Notes App</router-link>
      <div class="navbar-actions">
        <template v-if="auth.isAuthenticated">
          <span class="navbar-user">{{ auth.currentUser?.name }}</span>
          <button class="btn btn-sm btn-secondary" @click="handleLogout">
            Logout
          </button>
        </template>
        <template v-else>
          <router-link to="/login" class="btn btn-sm btn-primary">Login</router-link>
          <router-link to="/register" class="btn btn-sm btn-secondary">Register</router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

async function handleLogout() {
  await auth.logout();
  router.push('/login');
}
</script>

<style scoped>
.navbar {
  background: #2c3e50;
  color: #fff;
  padding: 0 1rem;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.navbar-inner {
  max-width: 900px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 56px;
}

.navbar-brand {
  color: #fff;
  font-size: 1.2rem;
  font-weight: 700;
  text-decoration: none;
}

.navbar-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.navbar-user {
  font-size: 0.85rem;
  opacity: 0.85;
}
</style>
