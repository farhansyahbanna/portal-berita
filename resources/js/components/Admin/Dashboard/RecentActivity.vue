<template>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">Recent Activity</h2>
            <button 
                @click="showAllActivities = !showAllActivities"
                class="text-blue-600 hover:text-blue-500 text-sm font-medium"
            >
                {{ showAllActivities ? 'Show Less' : 'View All' }}
            </button>
        </div>
        
        <div v-if="loading" class="space-y-4">
            <div v-for="i in 3" :key="i" class="animate-pulse">
                <div class="flex space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-gray-300 rounded-full"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="h-4 bg-gray-300 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-300 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-else-if="activities.length === 0" class="text-center py-8">
            <div class="text-gray-400 mb-2">
                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-gray-500">No recent activity found</p>
        </div>
        
        <div v-else class="space-y-4">
            <div 
                v-for="activity in displayedActivities" 
                :key="activity.id" 
                class="flex space-x-3"
            >
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full flex items-center justify-center" 
                         :class="activityIcon(activity.type).bgColor">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  :d="activityIcon(activity.type).icon"></path>
                        </svg>
                    </div>
                </div>
                
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-900">
                        <span class="font-medium">{{ activity.user?.name || 'System' }}</span>
                        {{ activity.description }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ formatTime(activity.created_at) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '../../../composables/useApi'
import { format, formatDistanceToNow } from 'date-fns'

export default {
    name: 'RecentActivity',
    setup() {
        const activities = ref([])
        const loading = ref(true)
        const showAllActivities = ref(false)
        const { makeRequest } = useApi()

        const displayedActivities = computed(() => {
            return showAllActivities.value ? activities.value : activities.value.slice(0, 5)
        })

        const activityIcon = (type) => {
            const icons = {
                post_created: {
                    icon: 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                    bgColor: 'bg-green-500'
                },
                post_updated: {
                    icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                    bgColor: 'bg-blue-500'
                },
                post_published: {
                    icon: 'M5 13l4 4L19 7',
                    bgColor: 'bg-purple-500'
                },
                comment_added: {
                    icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                    bgColor: 'bg-yellow-500'
                },
                user_registered: {
                    icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                    bgColor: 'bg-indigo-500'
                }
            }
            return icons[type] || { icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', bgColor: 'bg-gray-500' }
        }

        const formatTime = (timestamp) => {
            try {
                const date = new Date(timestamp)
                // Show relative time if less than 24 hours, otherwise show date
                if (Date.now() - date.getTime() < 24 * 60 * 60 * 1000) {
                    return formatDistanceToNow(date, { addSuffix: true })
                } else {
                    return format(date, 'MMM dd, yyyy HH:mm')
                }
            } catch {
                return timestamp
            }
        }

        const fetchActivities = async () => {
            try {
                loading.value = true
                // Ganti dengan endpoint API yang sesuai
                const response = await makeRequest('/api/activities/recent')
                activities.value = response.data || response
            } catch (error) {
                console.error('Failed to fetch activities:', error)
                // Fallback data untuk demo
                activities.value = [
                    {
                        id: 1,
                        type: 'post_created',
                        description: 'created a new post',
                        user: { name: 'You' },
                        created_at: new Date().toISOString()
                    },
                    {
                        id: 2,
                        type: 'post_published',
                        description: 'published a post',
                        user: { name: 'Editor' },
                        created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString()
                    },
                    {
                        id: 3,
                        type: 'comment_added',
                        description: 'added a comment to your post',
                        user: { name: 'John Doe' },
                        created_at: new Date(Date.now() - 5 * 60 * 60 * 1000).toISOString()
                    }
                ]
            } finally {
                loading.value = false
            }
        }

        onMounted(() => {
            fetchActivities()
        })

        return {
            activities,
            loading,
            showAllActivities,
            displayedActivities,
            activityIcon,
            formatTime
        }
    }
}
</script>