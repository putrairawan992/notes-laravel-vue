<template>
  <div>
    <div class="actions-bar">
      <h1 class="page-title">My Notes</h1>
      <router-link to="/notes/create" class="btn btn-primary">
        + New Note
      </router-link>
    </div>

    <!-- Search -->
    <div class="form-group">
      <input
        v-model="search"
        type="text"
        class="form-control"
        placeholder="Search notes by title..."
        @input="onSearch"
      />
    </div>

    <!-- Error -->
    <div v-if="notesStore.error" class="alert alert-error">
      {{ notesStore.error }}
    </div>

    <!-- Loading -->
    <div v-if="notesStore.loading && notesStore.notes.length === 0" class="empty-state">
      <p>Loading notes...</p>
    </div>

    <!-- Empty -->
    <div v-else-if="notesStore.notes.length === 0" class="empty-state">
      <p>📭 No notes yet.</p>
      <router-link to="/notes/create" class="btn btn-primary">
        Create your first note
      </router-link>
    </div>

    <!-- Notes List -->
    <div v-else>
      <div v-for="note in notesStore.notes" :key="note.id" class="card note-card">
        <div class="note-card-body">
          <h3 class="note-title">
            <router-link :to="`/notes/${note.id}`">{{ note.title }}</router-link>
          </h3>
          <p class="note-preview">{{ truncate(note.content, 150) }}</p>
          <div class="note-meta">
            {{ formatDate(note.created_at) }}
          </div>
        </div>
        <div class="note-card-actions">
          <router-link :to="`/notes/${note.id}/edit`" class="btn btn-sm btn-primary">
            Edit
          </router-link>
          <button class="btn btn-sm btn-danger" @click="confirmDelete(note)">
            Delete
          </button>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="notesStore.pagination" class="pagination">
        <button
          class="btn btn-sm btn-secondary"
          :disabled="notesStore.pagination.currentPage <= 1"
          @click="goToPage(notesStore.pagination.currentPage - 1)"
        >
          ← Prev
        </button>
        <span class="pagination-info">
          Page {{ notesStore.pagination.currentPage }} of
          {{ notesStore.pagination.lastPage }}
          ({{ notesStore.pagination.total }} total)
        </span>
        <button
          class="btn btn-sm btn-secondary"
          :disabled="notesStore.pagination.currentPage >= notesStore.pagination.lastPage"
          @click="goToPage(notesStore.pagination.currentPage + 1)"
        >
          Next →
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useNotesStore } from '../stores/notes';

const notesStore = useNotesStore();
const search = ref('');

let searchTimer = null;

function onSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    notesStore.fetchNotes({ search: search.value });
  }, 300);
}

function goToPage(page) {
  notesStore.fetchNotes({ search: search.value, page });
}

function truncate(text, maxLength) {
  if (!text) return '';
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

async function confirmDelete(note) {
  if (window.confirm(`Are you sure you want to delete "${note.title}"?`)) {
    try {
      await notesStore.deleteNote(note.id);
    } catch {
      // Error already set in store.
    }
  }
}

onMounted(() => {
  notesStore.fetchNotes();
});
</script>

<style scoped>
.note-card {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.note-card-body {
  flex: 1;
  min-width: 0;
}

.note-title a {
  color: #2c3e50;
  text-decoration: none;
  font-size: 1.1rem;
}

.note-title a:hover {
  color: #4a6cf7;
}

.note-preview {
  color: #7f8c8d;
  font-size: 0.9rem;
  margin: 0.4rem 0;
  white-space: pre-wrap;
  word-break: break-word;
}

.note-meta {
  font-size: 0.75rem;
  color: #bdc3c7;
}

.note-card-actions {
  display: flex;
  gap: 0.4rem;
  flex-shrink: 0;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 1.5rem;
}

.pagination-info {
  font-size: 0.85rem;
  color: #7f8c8d;
}
</style>
