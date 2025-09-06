<template>
  <div class="post-form">
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">
          {{ isEdit ? 'Edit Post' : 'Buat Post Baru' }}
        </h2>
        <p v-if="isEdit && !postData" class="text-sm text-red-600 mt-1">
          Memuat data post...
        </p>
      </div>
      
      <div class="p-6">
        <!-- Loading state -->
        <div v-if="loadingPost" class="flex justify-center items-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
          <span class="ml-3 text-gray-500">Memuat data post...</span>
        </div>

        <!-- Error Messages -->
        <div v-else-if="loadError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <p>{{ loadError }}</p>
        </div>

        <!-- Form Content -->
        <div v-else>
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
            <!-- Form fields -->
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

            <div class="mb-6">
              <label for="published_at" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Publikasi</label>
              <input
                type="datetime-local"
                id="published_at"
                v-model="form.published_at"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': errors.published_at }"
              />
              <p class="text-gray-500 text-sm mt-1">Kosongkan jika post masih draft</p>
            </div>

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
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useApi } from '../../../composables/useApi'

export default {
  props: {
    post: {
      type: Object,
      default: null
    },
    postId: {
      type: [String, Number],
      default: null
    },
    isEdit: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {
    const router = useRouter()
    const route = useRoute()
    const { makeRequest, loading: apiLoading, error: apiError } = useApi()
    
    const form = ref({
      title: '',
      content: '',
      published_at: ''
    })

    const postData = ref(props.post || null)
    const loadingPost = ref(props.isEdit && !props.post)
    const loadError = ref('')
    const errors = ref({})
    const successMessage = ref('')
    const globalError = ref('')
    const loading = ref(false)

    // Computed property untuk menentukan apakah dalam mode edit
    const isEditMode = computed(() => {
      return props.isEdit || route.name === 'admin.posts.edit'
    })

    // Computed property untuk mendapatkan ID post
    const currentPostId = computed(() => {
      return props.postId || route.params.id || (postData.value ? postData.value.id : null)
    })

    const formatDateTimeLocal = (dateTime) => {
      if (!dateTime) return ''
      const date = new Date(dateTime)
      const localDateTime = new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
      return localDateTime.toISOString().slice(0, 16)
    }

    // Fetch post data jika dalam mode edit dan tidak ada data post
    const fetchPostData = async () => {
      if (!isEditMode.value || !currentPostId.value) return
      
      if (postData.value) {
        // Jika sudah ada data post dari props, isi form
        fillForm()
        return
      }

      loadingPost.value = true
      loadError.value = ''

      try {
        const response = await makeRequest(`/api/admin/posts/${currentPostId.value}`)
        
        if (response.success) {
          postData.value = response.data
          fillForm()
        } else {
          loadError.value = 'Gagal memuat data post'
        }
      } catch (error) {
        console.error('Error fetching post:', error)
        loadError.value = error.response?.data?.message || 'Gagal memuat data post'
      } finally {
        loadingPost.value = false
      }
    }

    // Mengisi form dengan data post
    const fillForm = () => {
      if (!postData.value) return
      
      form.value = {
        title: postData.value.title || '',
        content: postData.value.content || '',
        published_at: postData.value.published_at ? formatDateTimeLocal(postData.value.published_at) : ''
      }
    }

    const submitForm = async () => {
      errors.value = {}
      successMessage.value = ''
      globalError.value = ''
      loading.value = true

      try {
        let url, method
        
        if (isEditMode.value && currentPostId.value) {
          url = `/api/admin/posts/${currentPostId.value}`
          method = 'put'
        } else {
          url = '/api/admin/posts'
          method = 'post'
        }

        const response = await makeRequest(url, method, form.value)
        
        if (response.success) {
          successMessage.value = isEditMode.value ? 'Post berhasil diperbarui!' : 'Post berhasil dibuat!'
          
          setTimeout(() => {
            router.push('/admin/posts')
          }, 1500)
        }
        
      } catch (error) {
        console.error('Form submission error:', error)
        
        if (error.response?.status === 422) {
          errors.value = error.response.data.errors || {}
        } else if (error.response?.status === 403) {
          globalError.value = 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.'
        } else if (error.response?.status === 401) {
          globalError.value = 'Sesi Anda telah habis. Silakan login kembali.'
          setTimeout(() => {
            router.push('/login')
          }, 2000)
        } else {
          globalError.value = error.response?.data?.message || 'Terjadi kesalahan. Silakan coba lagi.'
        }
      } finally {
        loading.value = false
      }
    }

    // Watch untuk perubahan props
    watch(() => props.post, (newPost) => {
      if (newPost) {
        postData.value = newPost
        fillForm()
      }
    })

    watch(() => props.postId, (newPostId) => {
      if (newPostId && isEditMode.value) {
        fetchPostData()
      }
    })

    // Load data saat komponen mounted
    onMounted(() => {
      console.log('AdminPostForm mounted', {
        isEdit: isEditMode.value,
        postId: currentPostId.value,
        hasPostData: !!postData.value,
        props: props
      })

      if (isEditMode.value) {
        if (postData.value) {
          fillForm()
        } else if (currentPostId.value) {
          fetchPostData()
        } else {
          loadError.value = 'ID post tidak valid'
        }
      }
    })

    return {
      form,
      postData,
      loadingPost,
      loadError,
      errors,
      loading,
      successMessage,
      globalError,
      isEdit: isEditMode,
      submitForm,
      formatDateTimeLocal
    }
  }
}
</script>