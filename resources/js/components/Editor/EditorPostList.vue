<template>
  <div class="editor-posts">
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-medium text-gray-900">Kelola Post Saya</h2>
          <div class="flex items-center space-x-4">
            <!-- Filter Status -->
            <select 
              v-model="filters.status" 
              @change="fetchPosts"
              class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Semua Status</option>
              <option value="published">Published</option>
              <option value="pending">Pending</option>
              <option value="draft">Draft</option>
            </select>

            <!-- Search Input -->
            <div class="relative">
              <input
                type="text"
                v-model="filters.search"
                @input="debouncedSearch"
                placeholder="Cari post..."
                class="border border-gray-300 rounded-md pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64"
              >
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>

            <!-- Add Post Button -->
            <router-link
              to="/editor/posts/create"
              class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Buat Post
            </router-link>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="px-6 py-8">
        <div class="flex justify-center items-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
          <span class="ml-3 text-gray-500">Memuat post...</span>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="px-6 py-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          <p class="font-medium">Error:</p>
          <p>{{ error }}</p>
          <button 
            @click="fetchPosts" 
            class="mt-2 bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700"
          >
            Coba Lagi
          </button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="!posts || posts.data.length === 0" class="px-6 py-8">
        <div class="text-center text-gray-500">
          <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <p class="text-lg font-medium">Belum ada post</p>
          <p class="mt-1">Anda belum membuat post apapun.</p>
          <router-link
            to="/editor/posts/create"
            class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
          >
            Buat Post Pertama
          </router-link>
        </div>
      </div>

      <!-- Posts Table -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Publikasi</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="post in posts.data" :key="post.id" class="hover:bg-gray-50">
              <!-- Title -->
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900 max-w-xs truncate">{{ post.title }}</div>
                <div class="text-sm text-gray-500 mt-1 truncate max-w-xs">
                  {{ truncateText(post.content, 60) }}
                </div>
              </td>

              <!-- Status -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClasses(post.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ getStatusText(post.status) }}
                </span>
              </td>

              <!-- Created Date -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(post.created_at) }}
              </td>

              <!-- Published Date -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ post.published_at ? formatDate(post.published_at) : '-' }}
              </td>

              <!-- Actions -->
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                  <!-- Edit Button -->
                  <router-link
                    :to="`/editor/posts/${post.id}/edit`"
                    class="text-blue-600 hover:text-blue-900 flex items-center"
                    title="Edit Post"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                  </router-link>

                  <!-- View Button -->
                  <a
                    v-if="post.status === 'published'"
                    :href="`/posts/${post.slug || post.id}`"
                    target="_blank"
                    class="text-green-600 hover:text-green-900 flex items-center"
                    title="Lihat Post"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Lihat
                  </a>

                  <!-- Delete Button -->
                  <button 
                    @click="deletePost(post)"
                    class="text-red-600 hover:text-red-900 flex items-center"
                    title="Hapus Post"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="posts && posts.meta && posts.meta.last_page > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <Pagination 
          :current-page="posts.meta.current_page" 
          :last-page="posts.meta.last_page"
          :total="posts.meta.total"
          @page-changed="handlePageChange"
        />
        
      </div>
      <div class="px-8 py-4 bg-gray-50 border-t border-gray-200">
          <button @click="goDashboard" class="text-blue-600 hover:text-blue-800 font-medium">
              ← Kembali
          </button>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal
      v-model="showDeleteModal"
      title="Hapus Post"
      max-width="sm"
      @close="closeDeleteModal"
    >
      <template #content>
        <div class="text-center">
          <svg class="w-16 h-16 text-red-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
          
          <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Penghapusan</h3>
          <p class="text-gray-600 mb-4">
            Apakah Anda yakin ingin menghapus post <strong>"{{ postToDelete?.title }}"</strong>?
            Tindakan ini tidak dapat dibatalkan.
          </p>

          <div class="flex justify-center space-x-3">
            <button
              @click="closeDeleteModal"
              class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
            >
              Batal
            </button>
            <button
              @click="confirmDelete"
              :disabled="loadingDelete"
              class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50"
            >
              <span v-if="loadingDelete">Menghapus...</span>
              <span v-else>Hapus</span>
            </button>
          </div>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '../../composables/useApi'
import Pagination from '../Shared/Pagination.vue'
import Modal from '../Shared/Modal.vue'

export default {
  name: 'EditorPostList',
  components: {
    Pagination,
    Modal
  },
  setup() {
    const router = useRouter()
    const posts = ref(null)
    const loading = ref(true)
    const error = ref('')
    const showDeleteModal = ref(false)
    const loadingDelete = ref(false)
    const postToDelete = ref(null)
    
    const filters = ref({
      status: '',
      search: '',
      page: 1,
      per_page: 15
    })

    const { makeRequest } = useApi()

    const getStatusText = (status) => {
      const statusMap = {
        published: 'Published',
        pending: 'Pending',
        draft: 'Draft',
        archived: 'Archived'
      }
      return statusMap[status] || status
    }

    const getStatusClasses = (status) => {
      const classes = {
        published: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        draft: 'bg-gray-100 text-gray-800',
        archived: 'bg-red-100 text-red-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const formatDate = (dateString) => {
      try {
        return new Date(dateString).toLocaleDateString('id-ID', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        })
      } catch {
        return dateString
      }
    }

    const truncateText = (text, length) => {
      if (!text) return ''
      return text.length > length ? text.substring(0, length) + '...' : text
    }

    const fetchPosts = async () => {
      try {
        loading.value = true
        error.value = ''
        
        const params = {
          page: filters.value.page,
          per_page: filters.value.per_page,
          status: filters.value.status,
          search: filters.value.search,
          my_posts: true // Parameter khusus untuk ambil post milik sendiri
        }

        const response = await makeRequest('/api/editor/posts', 'GET', null, { params })
        
        if (response.success) {
          posts.value = {
            data: response.data,
            meta: response.meta
          }
        } else {
          throw new Error(response.message || 'Failed to fetch posts')
        }
        
      } catch (err) {
        console.error('Failed to fetch posts:', err)
        error.value = err.message || 'Gagal memuat post'
      } finally {
        loading.value = false
      }
    }

    const deletePost = (post) => {
      postToDelete.value = post
      showDeleteModal.value = true
    }

    const confirmDelete = async () => {
      if (!postToDelete.value) return

      try {
        loadingDelete.value = true
        const response = await makeRequest(`/api/editor/posts/${postToDelete.value.id}`, 'DELETE')
        
        if (response.success) {
          // Remove from local state
          if (posts.value && posts.value.data) {
            posts.value.data = posts.value.data.filter(p => p.id !== postToDelete.value.id)
          }
          
          alert('Post berhasil dihapus')
          closeDeleteModal()
          fetchPosts() // Refresh list
        }
        
      } catch (err) {
        console.error('Failed to delete post:', err)
        alert('Gagal menghapus post: ' + (err.response?.data?.message || err.message))
      } finally {
        loadingDelete.value = false
      }
    }

    const closeDeleteModal = () => {
      showDeleteModal.value = false
      postToDelete.value = null
      loadingDelete.value = false
    }

    const handlePageChange = (page) => {
      filters.value.page = page
      fetchPosts()
    }

    const goDashboard = () => {
      router.push('/editor/dashboard')
    }

    const debouncedSearch = debounce(() => {
      filters.value.page = 1
      fetchPosts()
    }, 500)

    // Debounce function
    function debounce(func, wait) {
      let timeout
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout)
          func(...args)
        }
        clearTimeout(timeout)
        timeout = setTimeout(later, wait)
      }
    }

    onMounted(() => {
      fetchPosts()
    })

    watch(() => filters.value.status, () => {
      filters.value.page = 1
      fetchPosts()
    })

    return {
      posts,
      loading,
      error,
      filters,
      showDeleteModal,
      loadingDelete,
      postToDelete,
      getStatusText,
      getStatusClasses,
      formatDate,
      truncateText,
      deletePost,
      confirmDelete,
      closeDeleteModal,
      handlePageChange,
      debouncedSearch,
      fetchPosts,
      goDashboard
    }
  }
}
</script>

<style scoped>
.editor-posts {
  min-height: 600px;
}

tr:hover {
  background-color: #f9fafb;
}
</style>