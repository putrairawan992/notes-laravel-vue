import { defineStore } from 'pinia';
import { ref } from 'vue';
import apiClient from '../api/client';

/**
 * Pinia store for notes CRUD state management.
 */
export const useNotesStore = defineStore('notes', () => {
  // --- State ---
  const notes = ref([]);
  const currentNote = ref(null);
  const pagination = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // --- Actions ---
  async function fetchNotes(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await apiClient.get('/notes', { params });
      notes.value = response.data.data;
      pagination.value = {
        currentPage: response.data.current_page,
        lastPage: response.data.last_page,
        total: response.data.total,
      };
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to load notes.';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchNote(id) {
    loading.value = true;
    error.value = null;
    try {
      const response = await apiClient.get(`/notes/${id}`);
      currentNote.value = response.data.note;
      return response.data.note;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to load note.';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function createNote(data) {
    loading.value = true;
    error.value = null;
    try {
      const response = await apiClient.post('/notes', data);
      notes.value.unshift(response.data.note);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create note.';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateNote(id, data) {
    loading.value = true;
    error.value = null;
    try {
      const response = await apiClient.put(`/notes/${id}`, data);
      // Update in local list.
      const index = notes.value.findIndex((n) => n.id === id);
      if (index !== -1) {
        notes.value[index] = response.data.note;
      }
      currentNote.value = response.data.note;
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update note.';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deleteNote(id) {
    loading.value = true;
    error.value = null;
    try {
      await apiClient.delete(`/notes/${id}`);
      notes.value = notes.value.filter((n) => n.id !== id);
      if (currentNote.value?.id === id) {
        currentNote.value = null;
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete note.';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  return {
    notes,
    currentNote,
    pagination,
    loading,
    error,
    fetchNotes,
    fetchNote,
    createNote,
    updateNote,
    deleteNote,
  };
});
