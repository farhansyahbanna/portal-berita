<template>
  <div class="comment-form">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Komentar</h3>
    
    <form @submit.prevent="submitComment">
      <!-- Error Messages -->
      <div v-if="Object.keys(errors).length > 0" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul>
          <li v-for="(errorMessages, field) in errors" :key="field">
            <span v-for="(message, index) in errorMessages" :key="index">{{ message }}</span>
          </li>
        </ul>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ successMessage }}
      </div>

      <!-- Form untuk user yang belum login -->
      <div v-if="!isAuthenticated" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
          <input
            type="text"
            id="name"
            v-model="form.name"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{ 'border-red-500': errors.name }"
            placeholder="Masukkan nama Anda"
          />
        </div>
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
          <input
            type="email"
            id="email"
            v-model="form.email"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{ 'border-red-500': errors.email }"
            placeholder="Masukkan email Anda"
          />
        </div>
      </div>

      <!-- Comment Content -->
      <div class="mb-4">
        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Komentar *</label>
        <textarea
          id="content"
          v-model="form.content"
          required
          rows="4"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          :class="{ 'border-red-500': errors.content }"
          placeholder="Tulis komentar Anda di sini..."
        ></textarea>
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        :disabled="loading"
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
      >
        <span v-if="loading">Mengirim...</span>
        <span v-else>Kirim Komentar</span>
      </button>

      <!-- Info untuk moderated comments -->
      <p class="text-sm text-gray-500 mt-3">
        ⓘ Komentar akan ditinjau oleh admin sebelum ditampilkan
      </p>
    </form>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useApi } from '../../composables/useApi'

export default {
  name: 'CommentForm',
  props: {
    postId: {
      type: [String, Number],
      required: true
    }
  },
  setup(props, { emit }) {
    const authStore = useAuthStore()
    const { makeRequest } = useApi()
    
    const form = ref({
      name: '',
      email: '',
      content: ''
    })

    const errors = ref({})
    const loading = ref(false)
    const successMessage = ref('')

    const isAuthenticated = computed(() => authStore.isAuthenticated)

    // Jika user sudah login, isi otomatis data user
    if (isAuthenticated.value && authStore.user) {
      form.value.name = authStore.user.name
      form.value.email = authStore.user.email
    }

    const submitComment = async () => {
      try {
        loading.value = true
        errors.value = {}
        successMessage.value = ''

      const payload = {
        post_id: props.postId,       
        name: form.value.name,
        email: form.value.email,
        content: form.value.content
      }

        const response = await makeRequest(`/api/posts/${props.postId}/comments`, 'POST', payload)
        
        if (response.success) {
          successMessage.value = 'Komentar berhasil dikirim! Menunggu persetujuan admin.'
          form.value.content = ''
          
          
          emit('comment-added')
        }
        
      } catch (error) {
        console.error('Failed to submit comment:', error)
        
        if (error.response?.status === 422) {
          errors.value = error.response.data.errors || {}
        } else {
          errors.value = { general: [error.response?.data?.message || 'Gagal mengirim komentar'] }
        }
      } finally {
        loading.value = false
      }
    }

    return {
      form,
      errors,
      loading,
      successMessage,
      isAuthenticated,
      submitComment
    }
  }
}
</script>

<style scoped>
.comment-form {
  background-color: white;
  padding: 1.5rem;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}
</style>