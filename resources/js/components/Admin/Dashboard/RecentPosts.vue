<template>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Recent Posts</h2>
        
        <div v-if="posts.length === 0" class="text-gray-500 text-center py-4">
            No posts found
        </div>
        
        <ul v-else class="divide-y divide-gray-200">
            <li v-for="post in posts" :key="post.id" class="py-3">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ post.title }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ formatDate(post.created_at) }}
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <span class="px-2 py-1 text-xs font-medium rounded-full" 
                              :class="statusBadgeClass(post.status)">
                            {{ post.status }}
                        </span>
                    </div>
                </div>
            </li>
        </ul>
        
        <router-link 
            to="/admin/posts" 
            class="block mt-4 text-blue-600 hover:text-blue-500 text-sm font-medium"
        >
            View all posts →
        </router-link>
    </div>
</template>

<script>
import { format } from 'date-fns'

export default {
    name: 'RecentPosts',
    props: {
        posts: {
            type: Array,
            default: () => []
        }
    },
    setup() {
        const formatDate = (dateString) => {
            try {
                return format(new Date(dateString), 'MMM dd, yyyy')
            } catch {
                return dateString
            }
        }

        const statusBadgeClass = (status) => {
            const classes = {
                published: 'bg-green-100 text-green-800',
                draft: 'bg-gray-100 text-gray-800',
                pending: 'bg-yellow-100 text-yellow-800',
                archived: 'bg-red-100 text-red-800'
            }
            return classes[status] || classes.draft
        }

        return {
            formatDate,
            statusBadgeClass
        }
    }
}
</script>