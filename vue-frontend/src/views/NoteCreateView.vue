<template>
  <div>
    <h1 class="page-title">Create New Note</h1>

    <div v-if="notesStore.error" class="alert alert-error">
      {{ notesStore.error }}
    </div>

    <form @submit.prevent="handleCreate">
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
          {{ notesStore.loading ? 'Saving...' : 'Save Note' }}
        </button>
        <router-link to="/notes" class="btn btn-secondary">Cancel</router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useNotesStore } from '../stores/notes';

const notesStore = useNotesStore();
const router = useRouter();

const form = reactive({
  title: '',
  content: '',
});

const validationErrors = ref({});

async function handleCreate() {
  validationErrors.value = {};
  try {
    await notesStore.createNote(form);
    router.push('/notes');
  } catch (err) {
    if (err.response?.status === 422) {
      validationErrors.value = err.response.data.errors || {};
    }
  }
}
</script>

<style scoped>
.form-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1rem;
}
</style>
