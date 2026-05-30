<template>
  <div class="auth-page">
    <div class="card auth-card">
      <h1 class="page-title">Register</h1>

      <div v-if="auth.error" class="alert alert-error">{{ auth.error }}</div>

      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label for="name">Name</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            class="form-control"
            placeholder="Your name"
            required
            autocomplete="name"
          />
          <div v-if="validationErrors.name" class="form-error">
            {{ validationErrors.name[0] }}
          </div>
        </div>

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
            placeholder="Min. 8 characters"
            required
            autocomplete="new-password"
          />
          <div v-if="validationErrors.password" class="form-error">
            {{ validationErrors.password[0] }}
          </div>
        </div>

        <div class="form-group">
          <label for="password_confirmation">Confirm Password</label>
          <input
            id="password_confirmation"
            v-model="form.passwordConfirmation"
            type="password"
            class="form-control"
            placeholder="Repeat your password"
            required
            autocomplete="new-password"
          />
        </div>

        <button type="submit" class="btn btn-primary" :disabled="auth.loading" style="width:100%">
          {{ auth.loading ? 'Creating account...' : 'Register' }}
        </button>
      </form>

      <p class="auth-footer">
        Already have an account?
        <router-link to="/login">Login here</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const router = useRouter();

const form = reactive({
  name: '',
  email: '',
  password: '',
  passwordConfirmation: '',
});

const validationErrors = ref({});

async function handleRegister() {
  validationErrors.value = {};
  try {
    await auth.register(
      form.name,
      form.email,
      form.password,
      form.passwordConfirmation,
    );
    router.push('/notes');
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
  padding-top: 2rem;
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
