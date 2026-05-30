<template>
  <div>
    <!-- Loading -->
    <div v-if="notesStore.loading && !notesStore.currentNote" class="empty-state">
      <p>Loading note...</p>
    </div>

    <!-- Error -->
    <div v-else-if="notesStore.error && !notesStore.currentNote" class="alert alert-error">
      {{ notesStore.error }}
    </div>

    <!-- Not Found -->
    <div v-else-if="!notesStore.currentNote" class="empty-state">
      <p>Note not found.</p>
      <router-link to="/notes" class="btn btn-primary">Back to Notes</router-link>
    </div>

    <!-- Edit Form -->
    <div v-else>
      <h1 class="page-title">Edit Note</h1>

      <div v-if="notesStore.error" class="alert alert-error">
        {{ notesStore.error }}
      </div>

      <form @submit.prevent="handleUpdate">
        <div class="form-group">
          <label for="title">Title</label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            class="form-control"
            placeholder="Note title"
            required
            maxlength="255"
          />
          <div v-if="validationErrors.title" class="form-error">
            {{ validationErrors.title[0] }}
          </div>
        </div>

        <div class="form-group">
          <label for="content">Content</label>
          <textarea
            id="content"
            v-model="form.content"
            class="form-control"
            placeholder="Write your note here..."
            required
            rows="8"
          ></textarea>
          <div v-if="validationErrors.content" class="form-error">
            {{ validationErrors.content[0] }}
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="notesStore.loading">
            {{ notesStore.loading ? 'Updating...' : 'Update Note' }}
          </button>
          <router-link :to="`/notes/${props.id}`" class="btn btn-secondary">
            Cancel
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, watch } from 'vue';
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

const form = reactive({
  title: '',
  content: '',
});

const validationErrors = ref({});

// Once the note is loaded, populate the form.
watch(
  () => notesStore.currentNote,
  (note) => {
    if (note) {
      form.title = note.title;
      form.content = note.content;
    }
  },
  { immediate: true },
);

async function handleUpdate() {
  validationErrors.value = {};
  try {
    await notesStore.updateNote(props.id, form);
    router.push(`/notes/${props.id}`);
  } catch (err) {
    if (err.response?.status === 422) {
      validationErrors.value = err.response.data.errors || {};
    }
  }
}

onMounted(() => {
  notesStore.fetchNote(props.id);
});
</script>

<style scoped>
.form-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1rem;
}
</style>
