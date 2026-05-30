import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
  {
    path: '/',
    redirect: '/notes',
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/LoginView.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../views/RegisterView.vue'),
    meta: { guest: true },
  },
  {
    path: '/notes',
    name: 'NotesList',
    component: () => import('../views/NotesListView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/notes/create',
    name: 'NoteCreate',
    component: () => import('../views/NoteCreateView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/notes/:id',
    name: 'NoteDetail',
    component: () => import('../views/NoteDetailView.vue'),
    meta: { requiresAuth: true },
    props: true,
  },
  {
    path: '/notes/:id/edit',
    name: 'NoteEdit',
    component: () => import('../views/NoteEditView.vue'),
    meta: { requiresAuth: true },
    props: true,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

/**
 * Navigation guard — redirect unauthenticated users to login.
 */
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login', query: { redirect: to.fullPath } });
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'NotesList' });
  } else {
    next();
  }
});

export default router;
