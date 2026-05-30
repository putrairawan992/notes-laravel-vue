import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import apiClient from '../api/client';

/**
 * Pinia store for authentication state management.
 */
export const useAuthStore = defineStore('auth', () => {
  // --- State ---
  const user = ref(null);
  const token = ref(localStorage.getItem('auth_token') || null);
  const loading = ref(false);
  const error = ref(null);

  // --- Getters ---
  const isAuthenticated = computed(() => !!token.value);
  const currentUser = computed(() => user.value);

  // --- Actions ---
  function setAuth(tokenValue, userData) {
    token.value = tokenValue;
    user.value = userData;
    localStorage.setItem('auth_token', tokenValue);
  }

  function clearAuth() {
    token.value = null;
    user.value = null;
    localStorage.removeItem('auth_token');
  }

  async function register(name, email, password, passwordConfirmation) {
    loading.value = true;
    error.value = null;
    try {
      const response = await apiClient.post('/register', {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation,
      });
      setAuth(response.data.token, response.data.user);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed.';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function login(email, password) {
    loading.value = true;
    error.value = null;
    try {
      const response = await apiClient.post('/login', { email, password });
      setAuth(response.data.token, response.data.user);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed.';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function logout() {
    try {
      await apiClient.post('/logout');
    } catch {
      // Proceed even if server call fails.
    } finally {
      clearAuth();
    }
  }

  async function fetchProfile() {
    try {
      const response = await apiClient.get('/profile');
      user.value = response.data.user;
    } catch {
      clearAuth();
    }
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    currentUser,
    register,
    login,
    logout,
    fetchProfile,
    clearAuth,
  };
});
