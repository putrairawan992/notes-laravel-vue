<template>
  <div>
    <!-- Loading -->
    <div v-if="notesStore.loading && !notesStore.currentNote" class="empty-state">
      <p>Loading note...</p>
    </div>

    <!-- Error -->
    <div v-else-if="notesStore.error" class="alert alert-error">
      {{ notesStore.error }}
    </div>

    <!-- Not Found -->
    <div v-else-if="!notesStore.currentNote" class="empty-state">
      <p>Note not found.</p>
      <router-link to="/notes" class="btn btn-primary">Back to Notes</router-link>
    </div>

    <!-- Note Detail -->
    <div v-else>
      <div class="actions-bar">
        <h1 class="page-title">{{ notesStore.currentNote.title }}</h1>
        <div>
          <router-link
            :to="`/notes/${notesStore.currentNote.id}/edit`"
            class="btn btn-sm btn-primary"
          >
            Edit
          </router-link>
          <button class="btn btn-sm btn-danger" @click="handleDelete" style="margin-left:0.5rem">
            Delete
          </button>
        </div>
      </div>

      <div class="card">
        <div class="note-content">{{ notesStore.currentNote.content }}</div>
      </div>

      <div class="note-meta-footer">
        <span>Created: {{ formatDate(notesStore.currentNote.created_at) }}</span>
        <span>Updated: {{ formatDate(notesStore.currentNote.updated_at) }}</span>
      </div>

      <router-link to="/notes" class="btn btn-secondary" style="margin-top:1rem">
        ← Back to Notes
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useNotesStore } from '../stores/notes';

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
});

const notesStore = useNotesStore();
const route = useRoute();
const router = useRouter();

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

async function handleDelete() {
  const title = notesStore.currentNote?.title || 'this note';
  if (window.confirm(`Are you sure you want to delete "${title}"?`)) {
    try {
      await notesStore.deleteNote(props.id);
      router.push('/notes');
    } catch {
      // Error already set in store.
    }
  }
}

onMounted(() => {
  notesStore.fetchNote(props.id);
});
</script>

<style scoped>
.note-content {
  white-space: pre-wrap;
  word-break: break-word;
  line-height: 1.7;
  font-size: 1rem;
}

.note-meta-footer {
  display: flex;
  gap: 1.5rem;
  font-size: 0.8rem;
  color: #bdc3c7;
  margin-top: 0.5rem;
}
</style>
