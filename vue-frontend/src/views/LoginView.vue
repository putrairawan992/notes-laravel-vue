<template>
  <div class="auth-page">
    <div class="card auth-card">
      <h1 class="page-title">Login</h1>

      <div v-if="auth.error" class="alert alert-error">{{ auth.error }}</div>

      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="email">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="form-control"
            placeholder="you@example.com"
            required
            autocomplete="email"
          />
          <div v-if="validationErrors.email" class="form-error">
            {{ validationErrors.email[0] }}
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            class="form-control"
            placeholder="••••••••"
            required
            autocomplete="current-password"
          />
          <div v-if="validationErrors.password" class="form-error">
            {{ validationErrors.password[0] }}
          </div>
        </div>

        <button type="submit" class="btn btn-primary" :disabled="auth.loading" style="width:100%">
          {{ auth.loading ? 'Logging in...' : 'Login' }}
        </button>
      </form>

      <p class="auth-footer">
        Don't have an account?
        <router-link to="/register">Register here</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const form = reactive({
  email: '',
  password: '',
});

const validationErrors = ref({});

async function handleLogin() {
  validationErrors.value = {};
  try {
    await auth.login(form.email, form.password);
    const redirect = route.query.redirect || '/notes';
    router.push(redirect);
  } catch (err) {
    if (err.response?.status === 422) {
      validationErrors.value = err.response.data.errors || {};
    }
  }
}
</script>

<style scoped>
.auth-page {
  display: flex;
  justify-content: center;
  padding-top: 3rem;
}

.auth-card {
  width: 100%;
  max-width: 420px;
}

.auth-footer {
  text-align: center;
  margin-top: 1.2rem;
  font-size: 0.9rem;
  color: #7f8c8d;
}

.auth-footer a {
  color: #4a6cf7;
  text-decoration: none;
}
</style>
