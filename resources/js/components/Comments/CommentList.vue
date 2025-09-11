<template>
  <div class="comments-section">
    <!-- Section Header -->
    <div class="border-b border-gray-200 pb-4 mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Komentar</h2>
      <p class="text-gray-600 mt-1">
        {{ comments.length }} komentar
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
      <span class="ml-3 text-gray-500">Memuat komentar...</span>
    </div>

    <!-- Empty State -->
    <div v-else-if="comments.length === 0" class="text-center py-8">
      <div class="text-gray-400 mb-4">
        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
      </div>
      <p class="text-gray-500">Belum ada komentar untuk berita ini.</p>
      <p class="text-gray-400 text-sm mt-1">Jadilah yang pertama memberikan komentar!</p>
    </div>

    <!-- Comments List -->
    <div v-else class="space-y-6">
      <div v-for="comment in comments" :key="comment.id" class="comment-item">
        <div class="flex space-x-3">
          <!-- Avatar -->
          <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
              <span class="text-white font-medium text-sm">
                {{ getInitials(comment.name) }}
              </span>
            </div>
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-gray-900">
                {{ comment.name }}
              </p>
              <span class="text-xs text-gray-500">
                {{ formatTime(comment.created_at) }}
              </span>
            </div>
            
            <div class="mt-1 text-sm text-gray-700">
              <p class="whitespace-pre-line">{{ comment.content }}</p>
            </div>

            <!-- Admin Badge (jika admin) -->
            <div v-if="comment.user && comment.user.role === 'admin'" class="mt-1">
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Admin
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Load More Button (jika ada pagination) -->
    <div v-if="hasMore && !loading" class="mt-6 text-center">
      <button
        @click="loadMore"
        :disabled="loadingMore"
        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
      >
        <span v-if="loadingMore">Memuat...</span>
        <span v-else>Muat Lebih Banyak</span>
      </button>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useApi } from '../../composables/useApi'

export default {
  name: 'CommentList',
  props: {
    postId: {
      type: [String, Number],
      required: true
    },
    initialComments: {
      type: Array,
      default: () => []
    }
  },
  setup(props) {
    const comments = ref(props.initialComments)
    const loading = ref(!props.initialComments.length)
    const loadingMore = ref(false)
    const currentPage = ref(1)
    const hasMore = ref(false)
    const { makeRequest } = useApi()

    const getInitials = (name) => {
      if (!name) return 'U'
      return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
    }

    const formatTime = (dateString) => {
      try {
        const date = new Date(dateString)
        const now = new Date()
        const diffTime = Math.abs(now - date)
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))
        
        if (diffDays === 0) {
          return 'Hari ini ' + date.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
          })
        } else if (diffDays === 1) {
          return 'Kemarin ' + date.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
          })
        } else if (diffDays < 7) {
          return `${diffDays} hari yang lalu`
        } else {
          return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
          })
        }
      } catch {
        return dateString
      }
    }

    const fetchComments = async (page = 1) => {
      try {
        if (page === 1) {
          loading.value = true
        } else {
          loadingMore.value = true
        }

        const response = await makeRequest(`/api/posts/${props.postId}/comments`, 'GET', null, {
          params: {
            page: page,
            per_page: 10,
            status: 'approved' // Hanya ambil comment yang approved
          }
        })

        if (response.success) {
          if (page === 1) {
            comments.value = response.data
          } else {
            comments.value = [...comments.value, ...response.data]
          }
          
          // Check jika ada halaman berikutnya
          hasMore.value = response.meta.current_page < response.meta.last_page
          currentPage.value = response.meta.current_page
        }
        
      } catch (error) {
        console.error('Failed to fetch comments:', error)
      } finally {
        loading.value = false
        loadingMore.value = false
      }
    }

    const loadMore = () => {
      fetchComments(currentPage.value + 1)
    }

    onMounted(() => {
      if (props.initialComments.length === 0) {
        fetchComments()
      } else {
        // Jika ada initial comments, check pagination
        hasMore.value = comments.value.length >= 10 // Asumsi per_page = 10
      }
    })

    return {
      comments,
      loading,
      loadingMore,
      hasMore,
      getInitials,
      formatTime,
      loadMore,
      fetchComments
    }
  }
}
</script>

<style scoped>
.comments-section {
  max-width: 800px;
  margin: 0 auto;
}

.comment-item {
  padding: 1rem;
  border-radius: 0.5rem;
  background-color: #f9fafb;
  transition: background-color 0.2s ease;
}

.comment-item:hover {
  background-color: #f3f4f6;
}
</style>