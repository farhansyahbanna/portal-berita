<template>
  <div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
      <h2 class="text-lg font-medium text-gray-900">Daftar Post</h2>
      <p class="text-sm text-gray-500 mt-1" v-if="posts && posts.meta">
        Menampilkan {{ posts.meta.from }} sampai {{ posts.meta.to }} dari {{ posts.meta.total }} post
      </p>
    </div>
    
    <!-- Loading State -->
    <div v-if="loading" class="px-6 py-8">
      <div class="flex justify-center items-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        <span class="ml-3 text-gray-500">Memuat data...</span>
      </div>
    </div>
    
    <!-- Error State -->
    <div v-else-if="error" class="px-6 py-4">
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <p class="font-medium">Error:</p>
        <p>{{ error }}</p>
      </div>
    </div>
    
    <!-- Empty State -->
    <div v-else-if="!posts || !posts.data || posts.data.length === 0" class="px-6 py-8">
      <div class="text-center text-gray-500">
        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-lg font-medium">Tidak ada post yang ditemukan</p>
        <p class="mt-1">Post yang Anda cari tidak ditemukan atau belum ada post yang dibuat.</p>
      </div>
    </div>
    
    <!-- Content -->
    <div v-else class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="post in posts.data" :key="post.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ post.title }}</div>
              <div class="text-sm text-gray-500 truncate max-w-xs">
                {{ truncateText(post.excerpt || post.content, 60) }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusClasses(post.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ getStatusText(post.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ post.user?.name || 'Unknown' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(post.created_at) }}
            </td>
      
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <div class="flex space-x-2">
                <router-link 
                  :to="{
                    name: 'admin.posts.edit',
                    params: { id: post.id },
                    props: { post: post }
                  }" 
                  class="text-blue-600 hover:text-blue-900 flex items-center"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Edit
                </router-link>
                <button @click="deletePost(post)" class="text-red-600 hover:text-red-900 flex items-center">
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
    <div class="px-8 py-4">
        <button @click="goDashboard" class="text-blue-600 hover:text-blue-800 font-medium">
            ← Kembali
        </button>
     </div>
    
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '../../../composables/useApi'
import Pagination from '../../Shared/Pagination.vue'

export default {
  name: 'AdminPostList',
  components: {
    Pagination
  },
  props: {
    initialPosts: {
      type: Object,
      default: null
    },
    filters: {
      type: Object,
      default: () => ({})
    }
  },
  setup(props, { emit }) {
    const router = useRouter()
    const posts = ref(props.initialPosts || null)
    const loading = ref(!props.initialPosts)
    const error = ref('')
    const { makeRequest } = useApi()

    // Watch untuk perubahan filters
    watch(() => props.filters, (newFilters) => {
      fetchPosts(newFilters)
    }, { deep: true })

    // Watch untuk perubahan initialPosts
    watch(() => props.initialPosts, (newPosts) => {
      if (newPosts) {
        posts.value = newPosts
        loading.value = false
        
      }
    })

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

    const currentFilters = ref({
      page: 1,
      per_page: 10,
      status: '',
      search: ''
    })

    const fetchPosts = async (filters = {}) => {
      try {
        loading.value = true
        error.value = ''
        
        const params = {
          ...currentFilters.value,
          ...props.filters
        }
        
        
        const response = await makeRequest('/api/admin/posts', 'GET', null, { params })
        
        
        // Handle response format
        if (response && response.data) {
          posts.value = response
          console.log('Posts data set:', posts.value)
        } else {
          // Jika response tidak memiliki structure yang diharapkan
          posts.value = {
            data: response || [],
            meta: {
              current_page: 1,
              per_page: params.per_page,
              total: response?.length || 0,
              last_page: 1,
              from: 1,
              to: response?.length || 0
            }
          }
        }
        
        emit('posts-loaded', posts.value)
        
      } catch (err) {
        console.error('Failed to fetch posts:', err)
        error.value = err.message || 'Gagal memuat data post'
      } finally {
        loading.value = false
      }
    }

    const handlePageChange = (page) => {
      console.log('Page changed to:', page)
      currentFilters.value.page = page
      fetchPosts()
    }

    watch(() => props.filters, (newFilters) => {
      console.log('Filters changed:', newFilters)
      // Reset ke page 1 ketika filter berubah
      currentFilters.value.page = 1
      currentFilters.value = {
        ...currentFilters.value,
        ...newFilters
      }
      fetchPosts()
    }, { deep: true })

     const debounce = (func, wait) => {
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

    const editPost = (post) => {
      // Method 1: Navigate dengan membawa data post sebagai props
      router.push({
        name: 'admin.posts.edit',
        params: { id: post.id },
        props: { post: post } // Kirim data post sebagai props
      })

    }

    const deletePost = async (post) => {
      if (confirm(`Apakah Anda yakin ingin menghapus post "${post.title}"?`)) {
        try {
          await makeRequest(`/api/admin/posts/${post.id}`, 'DELETE')
          
          // Refresh data
          fetchPosts(props.filters)
          
          // Tampilkan notifikasi sukses
          emit('show-notification', {
            type: 'success',
            message: 'Post berhasil dihapus'
          })
          
        } catch (err) {
          console.error('Failed to delete post:', err)
          
          // Tampilkan notifikasi error
          emit('show-notification', {
            type: 'error',
            message: err.message || 'Gagal menghapus post'
          })
        }
      }
    }

    const debouncedSearch = debounce(() => {
      currentFilters.value.page = 1
      fetchPosts()
    }, 500)

    const goDashboard = () => {
      router.push('/admin/dashboard')
    }

    // Fetch data jika tidak ada initial data
    onMounted(() => {
      console.log('AdminPostList mounted')
      if (!props.initialPosts) {
        fetchPosts(props.filters)
      } else {
        console.log('Using initial posts from props:', props.initialPosts)
      }
    })

    return {
      posts,
      loading,
      error,
      getStatusText,
      getStatusClasses,
      formatDate,
      truncateText,
      handlePageChange,
      editPost,
      deletePost,
      debouncedSearch,
      fetchPosts,
      goDashboard
    }
  }
}
</script>