<template>
  <div class="admin-comments">
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-medium text-gray-900">Kelola Komentar</h2>
          <div class="flex items-center space-x-4">
            <!-- Filter Status -->
            <select 
              v-model="filters.status" 
              @change="fetchComments"
              class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Semua Status</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
              <option value="spam">Spam</option>
            </select>

            <!-- Search Input -->
            <div class="relative">
              <input
                type="text"
                v-model="filters.search"
                @input="debouncedSearch"
                placeholder="Cari komentar..."
                class="border border-gray-300 rounded-md pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64"
              >
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="px-6 py-8">
        <div class="flex justify-center items-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
          <span class="ml-3 text-gray-500">Memuat komentar...</span>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="px-6 py-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          <p class="font-medium">Error:</p>
          <p>{{ error }}</p>
          <button 
            @click="fetchComments" 
            class="mt-2 bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700"
          >
            Coba Lagi
          </button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="!comments || comments.data.length === 0" class="px-6 py-8">
        <div class="text-center text-gray-500">
          <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
          </svg>
          <p class="text-lg font-medium">Tidak ada komentar</p>
          <p class="mt-1">Tidak ada komentar yang sesuai dengan filter Anda.</p>
        </div>
      </div>

      <!-- Comments Table -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Post</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="comment in comments.data" :key="comment.id">
              <!-- Comment Content -->
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900 max-w-xs truncate">{{ comment.content }}</div>
                <button 
                  @click="toggleExpand(comment.id)"
                  class="text-blue-600 text-xs hover:text-blue-800 mt-1"
                >
                  {{ expandedComments[comment.id] ? 'Sembunyikan' : 'Lihat selengkapnya' }}
                </button>
                <div v-if="expandedComments[comment.id]" class="mt-2 p-3 bg-gray-50 rounded-md">
                  <p class="text-sm text-gray-700">{{ comment.content }}</p>
                </div>
              </td>

              <!-- Post Title -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900" v-if="comment.post">
                  {{ comment.post.title }}
                </div>
                <div class="text-sm text-gray-500" v-else>
                  Post telah dihapus
                </div>
              </td>

              <!-- Author -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                      <span class="text-sm font-medium text-gray-700">
                        {{ comment.name ? comment.name.charAt(0).toUpperCase() : 'A' }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ comment.name || 'Anonymous' }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ comment.email }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Status -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClasses(comment.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ getStatusText(comment.status) }}
                </span>
              </td>

              <!-- Date -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(comment.created_at) }}
              </td>

              <!-- Actions -->
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                  <!-- Approve Button -->
                  <button 
                    v-if="comment.status !== 'approved'"
                    @click="updateCommentStatus(comment.id, 'approved')"
                    class="text-green-600 hover:text-green-900 flex items-center"
                    title="Setujui Komentar"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Approve
                  </button>

                  <!-- Reject Button -->
                  <button 
                    v-if="comment.status !== 'rejected'"
                    @click="updateCommentStatus(comment.id, 'rejected')"
                    class="text-red-600 hover:text-red-900 flex items-center"
                    title="Tolak Komentar"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Reject
                  </button>

                  <!-- Mark as Spam -->
                  <button 
                    v-if="comment.status !== 'spam'"
                    @click="updateCommentStatus(comment.id, 'spam')"
                    class="text-yellow-600 hover:text-yellow-900 flex items-center"
                    title="Tandai sebagai Spam"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Spam
                  </button>

                  <!-- Delete Button -->
                  <button 
                    @click="deleteComment(comment)"
                    class="text-gray-600 hover:text-gray-900 flex items-center"
                    title="Hapus Komentar"
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
      <div v-if="comments && comments.meta && comments.meta.last_page > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <Pagination 
          :current-page="comments.meta.current_page" 
          :last-page="comments.meta.last_page"
          :total="comments.meta.total"
          @page-changed="handlePageChange"
        />
      </div>
    </div>

    <!-- Bulk Actions -->
    <div v-if="selectedComments.length > 0" class="fixed bottom-4 right-4 bg-white shadow-lg rounded-lg p-4 border">
      <div class="flex items-center space-x-4">
        <span class="text-sm text-gray-600">
          {{ selectedComments.length }} komentar dipilih
        </span>
        <select 
          v-model="bulkAction" 
          class="border border-gray-300 rounded-md px-3 py-1 text-sm"
        >
          <option value="">Pilih Aksi</option>
          <option value="approve">Setujui</option>
          <option value="reject">Tolak</option>
          <option value="spam">Tandai sebagai Spam</option>
          <option value="delete">Hapus</option>
        </select>
        <button 
          @click="applyBulkAction"
          :disabled="!bulkAction"
          class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 disabled:opacity-50"
        >
          Terapkan
        </button>
        <button 
          @click="clearSelection"
          class="text-gray-600 hover:text-gray-800 text-sm"
        >
          Batal
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import { useApi } from '../../../composables/useApi'
import Pagination from '../../Shared/Pagination.vue'

export default {
  name: 'AdminCommentList',
  components: {
    Pagination
  },
  setup() {
    const comments = ref(null)
    const loading = ref(true)
    const error = ref('')
    const expandedComments = ref({})
    const selectedComments = ref([])
    const bulkAction = ref('')
    
    const filters = ref({
      status: '',
      search: '',
      page: 1,
      per_page: 20
    })

    const { makeRequest } = useApi()

    const getStatusText = (status) => {
      const statusMap = {
        pending: 'Pending',
        approved: 'Approved',
        rejected: 'Rejected',
        spam: 'Spam'
      }
      return statusMap[status] || status
    }

    const getStatusClasses = (status) => {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        spam: 'bg-gray-100 text-gray-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const formatDate = (dateString) => {
      try {
        return new Date(dateString).toLocaleDateString('id-ID', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        })
      } catch {
        return dateString
      }
    }

    const toggleExpand = (commentId) => {
      expandedComments.value = {
        ...expandedComments.value,
        [commentId]: !expandedComments.value[commentId]
      }
    }

    const fetchComments = async () => {
      try {
        loading.value = true
        error.value = ''
        
        const params = {
          page: filters.value.page,
          per_page: filters.value.per_page,
          status: filters.value.status,
          search: filters.value.search
        }

        const response = await makeRequest('/api/admin/comments', 'GET', null, { params })
        
        
        if (response.success) {
          comments.value = {
            data: response.data,
            meta: response.meta
          }
        } else {
          throw new Error(response.message || 'Failed to fetch comments')
        }
        
      } catch (err) {
        console.error('Failed to fetch comments:', err)
        error.value = err.message || 'Gagal memuat komentar'
      } finally {
        loading.value = false
      }
    }

    const updateCommentStatus = async (commentId, status) => {
      try {
        const response = await makeRequest(`/api/admin/comments/${commentId}/status`, 'PUT', { status })
        
        if (response.success) {
          // Update local state
          if (comments.value && comments.value.data) {
            const commentIndex = comments.value.data.findIndex(c => c.id === commentId)
            if (commentIndex !== -1) {
              comments.value.data[commentIndex].status = status
            }
          }
          
          // Show success message
          alert('Status komentar berhasil diperbarui')
        }
        
      } catch (err) {
        console.error('Failed to update comment status:', err)
        alert('Gagal memperbarui status komentar: ' + (err.response?.data?.message || err.message))
      }
    }

    const deleteComment = async (comment) => {
      if (!confirm(`Apakah Anda yakin ingin menghapus komentar dari "${comment.name}"?`)) {
        return
      }

      try {
        const response = await makeRequest(`/api/admin/comments/${comment.id}`, 'DELETE')
        
        if (response.success) {
          // Remove from local state
          if (comments.value && comments.value.data) {
            comments.value.data = comments.value.data.filter(c => c.id !== comment.id)
          }
          
          alert('Komentar berhasil dihapus')
        }
        
      } catch (err) {
        console.error('Failed to delete comment:', err)
        alert('Gagal menghapus komentar: ' + (err.response?.data?.message || err.message))
      }
    }

    const handlePageChange = (page) => {
      filters.value.page = page
      fetchComments()
    }

    const debouncedSearch = debounce(() => {
      filters.value.page = 1
      fetchComments()
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
      fetchComments()
    })

    watch(() => filters.value.status, () => {
      filters.value.page = 1
      fetchComments()
    })

    return {
      comments,
      loading,
      error,
      filters,
      expandedComments,
      selectedComments,
      bulkAction,
      getStatusText,
      getStatusClasses,
      formatDate,
      toggleExpand,
      updateCommentStatus,
      deleteComment,
      handlePageChange,
      debouncedSearch,
      fetchComments
    }
  }
}
</script>

<style scoped>
.admin-comments {
  min-height: 600px;
}

tr:hover {
  background-color: #f9fafb;
}
</style>