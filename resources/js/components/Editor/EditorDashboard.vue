<template>
  <div class="editor-dashboard container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Dashboard Editor</h1>
      <p class="text-gray-600 mt-2">Selamat datang, {{ user?.name }}! Kelola konten Anda di sini.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <StatCard 
        title="Total Post" 
        :value="stats.total_posts || 0" 
        icon="📝"
        color="blue"
        :loading="loadingStats"
      />
      <StatCard 
        title="Published" 
        :value="stats.published_posts || 0" 
        icon="✅"
        color="green"
        :loading="loadingStats"
      />
      <StatCard 
        title="Pending Review" 
        :value="stats.pending_posts || 0" 
        icon="⏳"
        color="yellow"
        :loading="loadingStats"
      />
      <StatCard 
        title="Draft" 
        :value="stats.draft_posts || 0" 
        icon="📄"
        color="purple"
        :loading="loadingStats"
      />
    </div>

    

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Recent Posts -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Post Terbaru</h2>
        </div>
        <div class="p-6">
          <div v-if="loadingPosts" class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
          </div>
          <div v-else-if="recentPosts.length === 0" class="text-center py-8 text-gray-500">
            <p>Belum ada post yang dibuat</p>
          </div>
          <div v-else class="space-y-4">
            <div v-for="post in recentPosts" :key="post.id" class="border-b border-gray-200 pb-4 last:border-b-0">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <h3 class="text-sm font-medium text-gray-900 truncate">{{ post.title }}</h3>
                  <p class="text-xs text-gray-500 mt-1">{{ formatDate(post.created_at) }}</p>
                  <span :class="getStatusClasses(post.status)" class="inline-block mt-2 px-2 py-1 text-xs font-medium rounded-full">
                    {{ getStatusText(post.status) }}
                  </span>
                </div>
                <router-link 
                  :to="{ name: 'editor.posts.edit', params: { id: post.id } }"
                  class="ml-4 text-blue-600 hover:text-blue-900 text-sm font-medium"
                >
                  Edit
                </router-link>
              </div>
            </div>
          </div>
          <router-link 
            v-if="recentPosts.length > 0"
            :to="{ name: 'editor.posts' }"
            class="block mt-4 text-blue-600 hover:text-blue-900 text-sm font-medium text-center"
          >
            Lihat Semua Post →
          </router-link>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Aktivitas Terbaru</h2>
        </div>
        <div class="p-6">
          <div v-if="loadingActivity" class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
          </div>
          <div v-else-if="recentActivity.length === 0" class="text-center py-8 text-gray-500">
            <p>Belum ada aktivitas</p>
          </div>
          <div v-else class="space-y-4">
            <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start space-x-3">
              <div :class="getActivityIconColor(activity.type)" class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center">
                <span class="text-white text-sm">{{ getActivityIcon(activity.type) }}</span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-900">
                  {{ activity.description }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                  {{ formatTime(activity.created_at) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <QuickActionCard
          title="Buat Post Baru"
          description="Buat konten post baru"
          icon="plus"
          color="blue"
          :to="{ name: 'editor.posts.create' }"
        />
        <QuickActionCard
          title="Kelola Post Saya"
          description="Lihat dan edit post yang sudah dibuat"
          icon="edit"
          color="green"
          :to="{ name: 'editor.posts' }"
        />
        <QuickActionCard
          title="Post Published"
          description="Lihat post yang sudah dipublikasi"
          icon="eye"
          color="purple"
          :to="{ name: 'editor.posts', query: { status: 'published' } }"
        />
        <QuickActionCard
          title="Post Pending"
          description="Lihat post dalam review"
          icon="clock"
          color="yellow"
          :to="{ name: 'editor.posts', query: { status: 'pending' } }"
        />
      </div>
    </div>

    <!-- Additional Resources -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
      <h2 class="text-lg font-medium text-blue-900 mb-2">Resources & Bantuan</h2>
      <p class="text-blue-700 text-sm mb-4">
        Butuh bantuan dalam membuat konten? Berikut beberapa resources yang dapat membantu:
      </p>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          Panduan Menulis
        </a>
        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          FAQ Editor
        </a>
        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          Support Tim
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useApi } from '../../composables/useApi'
import StatCard from '../Editor/StatCard.vue'
import QuickActionCard from '../Editor/QuickActionCard.vue'

export default {
  name: 'EditorDashboard',
  components: {
    StatCard,
    QuickActionCard
  },
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    const { makeRequest } = useApi()

    const stats = ref({})
    const recentPosts = ref([])
    const recentActivity = ref([])
    const loadingStats = ref(true)
    const loadingPosts = ref(true)
    const loadingActivity = ref(true)
    const error = ref('')

    const user = computed(() => authStore.user)

    const getStatusText = (status) => {
      const statusMap = {
        published: 'Published',
        pending: 'Pending Review',
        draft: 'Draft'
      }
      return statusMap[status] || status
    }

    const getStatusClasses = (status) => {
      const classes = {
        published: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        draft: 'bg-gray-100 text-gray-800'
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

    const getActivityIcon = (type) => {
      const icons = {
        post_created: '📝',
        post_updated: '✏️',
        post_published: '✅',
        post_pending: '⏳'
      }
      return icons[type] || '📋'
    }

    const getActivityIconColor = (type) => {
      const colors = {
        post_created: 'bg-blue-500',
        post_updated: 'bg-yellow-500',
        post_published: 'bg-green-500',
        post_pending: 'bg-orange-500'
      }
      return colors[type] || 'bg-gray-500'
    }

    const fetchDashboardData = async () => {
      try {
        // Fetch stats
        const statsResponse = await makeRequest('/api/editor/dashboard/stats')
        if (statsResponse.success) {
          stats.value = statsResponse.data
        }
        
        // Fetch recent posts
        const postsResponse = await makeRequest('/api/editor/dashboard/posts/recent')
        if (postsResponse.success) {
          recentPosts.value = postsResponse.data
        }
        
        // Fetch recent activity
        const activityResponse = await makeRequest('/api/editor/dashboard/activity/recent')
        if (activityResponse.success) {
          recentActivity.value = activityResponse.data
        } else {
          // Fallback activity data
          recentActivity.value = [
            {
              id: 1,
              type: 'post_created',
              description: 'Anda membuat post baru "Cara Menulis Konten yang Baik"',
              created_at: new Date().toISOString()
            },
            {
              id: 2,
              type: 'post_updated',
              description: 'Anda memperbarui post "Tips Editing Konten"',
              created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString()
            }
          ]
        }
        
      } catch (err) {
        console.error('Failed to fetch dashboard data:', err)
        error.value = err.message || 'Gagal memuat data dashboard'
        
        // Fallback data untuk demo
        stats.value = {
          total_posts: 8,
          published_posts: 3,
          pending_posts: 2,
          draft_posts: 3
        }
        
        recentPosts.value = [
          {
            id: 1,
            title: 'Cara Menulis Konten yang Menarik',
            status: 'published',
            created_at: new Date().toISOString()
          },
          {
            id: 2,
            title: 'Tips Editing untuk Pemula',
            status: 'pending',
            created_at: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString()
          },
          {
            id: 3,
            title: 'Panduan Formatting Konten',
            status: 'draft',
            created_at: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000).toISOString()
          }
        ]
      } finally {
        loadingStats.value = false
        loadingPosts.value = false
        loadingActivity.value = false
      }
    }

    onMounted(() => {
      fetchDashboardData()
    })

    return {
      user,
      stats,
      recentPosts,
      recentActivity,
      loadingStats,
      loadingPosts,
      loadingActivity,
      error,
      getStatusText,
      getStatusClasses,
      formatDate,
      formatTime,
      getActivityIcon,
      getActivityIconColor
    }
  }
}
</script>

<style scoped>
.editor-dashboard {
  min-height: calc(100vh - 200px);
}
</style>