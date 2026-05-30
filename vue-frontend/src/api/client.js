import axios from 'axios';

/**
 * Axios instance pre-configured for the Laravel API.
 *
 * - Development (npm run dev): Uses Vite proxy → /api/* forwarded to localhost:8000/api/*
 * - Production: Reads VITE_API_BASE_URL from .env (e.g. https://api.mydomain.com/api)
 */
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

/**
 * Request interceptor — attach Bearer token if available.
 */
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

/**
 * Response interceptor — handle 401 globally.
 */
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('auth_token');
      // Redirect to login if not already there.
      if (window.location.pathname !== '/login') {
        window.location.href = '/login';
      }
    }
    return Promise.reject(error);
  },
);

export default apiClient;
