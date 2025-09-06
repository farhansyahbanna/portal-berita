<template>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Admin Dashboard</h1>
        
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        </div>
        
        <!-- Error State -->
        <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ error }}
        </div>
        
        <!-- Content -->
        <div v-else>
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <StatCard 
                    title="Total Posts" 
                    :value="stats?.total_posts || 0" 
                    icon="📝"
                    color="blue"
                />
                <StatCard 
                    title="Published Posts" 
                    :value="stats?.published_posts || 0" 
                    icon="✅"
                    color="green"
                />
                <StatCard 
                    title="Pending Posts" 
                    :value="stats?.pending_posts || 0" 
                    icon="⏳"
                    color="yellow"
                />
                <StatCard 
                    title="Total Comments" 
                    :value="stats?.total_comments || 0" 
                    icon="💬"
                    color="purple"
                />
            </div>
            
            <!-- Recent Posts & Comments -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <RecentPosts :posts="recentPosts || []" />
                <RecentComments :comments="recentComments || []" />
            </div>
            
            <!-- Quick Actions -->
            <QuickActions class="mt-8" />
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useApi } from '../../../composables/useApi'
import StatCard from '../Dashboard/StatCard.vue'
import RecentPosts from '../Dashboard/RecentPosts.vue'
import RecentComments from '../Dashboard/RecentComments.vue'
import QuickActions from '../Dashboard/QuickActions.vue'

export default {
    name: 'AdminDashboard',
    components: {
        StatCard,
        RecentPosts,
        RecentComments,
        QuickActions
    },
    setup() {
        const stats = ref(null)
        const recentPosts = ref([])
        const recentComments = ref([])
        const loading = ref(true)
        const error = ref('')
        
        const { makeRequest } = useApi()

        const fetchDashboardData = async () => {
            try {
                loading.value = true
                error.value = ''
                
                // Fetch stats
                const statsResponse = await makeRequest('/api/admin/dashboard/stats')
                stats.value = statsResponse.data || statsResponse
                
                // Fetch recent posts
                const postsResponse = await makeRequest('/api/admin/posts/recent')
                recentPosts.value = postsResponse.data || postsResponse
                
                // Fetch recent comments
                const commentsResponse = await makeRequest('/api/admin/comments/recent')
                recentComments.value = commentsResponse.data || commentsResponse
                
            } catch (err) {
                console.error('Failed to fetch dashboard data:', err)
                error.value = err.message || 'Failed to load dashboard data'
            } finally {
                loading.value = false
            }
        }

        onMounted(() => {
            fetchDashboardData()
        })

        return {
            stats,
            recentPosts,
            recentComments,
            loading,
            error
        }
    }
}
</script>