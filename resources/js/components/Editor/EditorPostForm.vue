<template>
  <div class="editor-post-form">
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">
          {{ isEdit ? 'Edit Post' : 'Buat Post Baru' }}
        </h2>
      </div>
      
      <div class="p-6">
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

        <!-- Global Error Message -->
        <div v-if="globalError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {{ globalError }}
        </div>

        <form @submit.prevent="submitForm">
          <!-- Title -->
          <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Post *</label>
            <input
              type="text"
              id="title"
              v-model="form.title"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-500': errors.title }"
              placeholder="Masukkan judul post"
            />
          </div>

          <!-- Content -->
          <div class="mb-4">
            <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Konten *</label>
            <textarea
              id="content"
              v-model="form.content"
              required
              rows="10"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-500': errors.content }"
              placeholder="Tulis konten post di sini..."
            ></textarea>
          </div>

          <!-- Status -->
          <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
            <select
              id="status"
              v-model="form.status"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-500': errors.status }"
            >
              <option value="draft">Draft</option>
              <option value="pending">Pending Review</option>
              <option value="published">Publish</option>
            </select>
            <p class="text-gray-500 text-sm mt-1">
              Pilih "Pending Review" untuk mengirim post kepada admin untuk persetujuan.
            </p>
          </div>

          <!-- Published At -->
          <div class="mb-6" v-if="form.status === 'published'">
            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Publikasi</label>
            <input
              type="datetime-local"
              id="published_at"
              v-model="form.published_at"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-500': errors.published_at }"
            />
          </div>

          <!-- Form Actions -->
          <div class="flex items-center justify-between">
            <button
              type="button"
              @click="$router.back()"
              class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
            >
              Batal
            </button>
            
            <button
              type="submit"
              :disabled="loading"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading">Menyimpan...</span>
              <span v-else>{{ isEdit ? 'Update Post' : 'Buat Post' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useApi } from '../../composables/useApi'

export default {
  name: 'EditorPostForm',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const { makeRequest, loading: apiLoading, error: apiError } = useApi()
    
    const form = ref({
      title: '',
      content: '',
      status: 'draft',
      published_at: ''
    })

    const errors = ref({})
    const loading = ref(false)
    const successMessage = ref('')
    const globalError = ref('')
    const isEdit = ref(false)
    const postId = ref(null)

    const formatDateTimeLocal = (dateTime) => {
      if (!dateTime) return ''
      const date = new Date(dateTime)
      const localDateTime = new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
      return localDateTime.toISOString().slice(0, 16)
    }

    const fetchPost = async () => {
      if (!postId.value) return

      try {
        loading.value = true
        const response = await makeRequest(`/api/editor/posts/${postId.value}`)
        
        if (response.success) {
          const post = response.data
          form.value = {
            title: post.title,
            content: post.content,
            status: post.status,
            published_at: post.published_at ? formatDateTimeLocal(post.published_at) : ''
          }
        }
        
      } catch (err) {
        console.error('Failed to fetch post:', err)
        globalError.value = 'Gagal memuat data post'
      } finally {
        loading.value = false
      }
    }

    const submitForm = async () => {
      try {
        loading.value = true
        errors.value = {}
        successMessage.value = ''
        globalError.value = ''

        let url, method
        if (isEdit.value) {
          url = `/api/editor/posts/${postId.value}`
          method = 'PUT'
        } else {
          url = '/api/editor/posts'
          method = 'POST'
        }

        // Prepare data
        const data = { ...form.value }
        
        const response = await makeRequest(url, method, data)
        
        if (response.success) {
          successMessage.value = isEdit.value ? 'Post berhasil diperbarui!' : 'Post berhasil dibuat!'
          
          setTimeout(() => {
            router.push('/editor/posts')
          }, 1500)
        }
        
      } catch (err) {
        console.error('Form submission error:', err)
        
        if (err.response?.status === 422) {
          errors.value = err.response.data.errors || {}
        } else if (err.response?.status === 403) {
          globalError.value = 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.'
        } else if (err.response?.status === 404) {
          globalError.value = 'Post tidak ditemukan.'
        } else {
          globalError.value = err.response?.data?.message || 'Terjadi kesalahan. Silakan coba lagi.'
        }
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      // Check if in edit mode
      if (route.params.id) {
        isEdit.value = true
        postId.value = route.params.id
        fetchPost()
      }
    })

    return {
      form,
      errors,
      loading,
      successMessage,
      globalError,
      isEdit,
      submitForm
    }
  }
}
</script>