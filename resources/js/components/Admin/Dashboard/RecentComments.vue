<template>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Recent Comments</h2>
        
        <div v-if="comments.length === 0" class="text-gray-500 text-center py-4">
            No comments found
        </div>
        
        <ul v-else class="divide-y divide-gray-200">
            <li v-for="comment in comments" :key="comment.id" class="py-3">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-700">
                                {{ comment.name ? comment.name.charAt(0).toUpperCase() : 'U' }}
                            </span>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900">
                            {{ comment.name || 'Unknown' }}
                        </p>
                        <p class="text-sm text-gray-500 truncate">
                            {{ truncateText(comment.content, 50) }}
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="px-2 py-1 text-xs font-medium rounded-full" 
                              :class="statusBadgeClass(comment.status)">
                            {{ comment.status }}
                        </span>
                    </div>
                </div>
            </li>
        </ul>
        
        <router-link 
            to="/admin/comments" 
            class="block mt-4 text-blue-600 hover:text-blue-500 text-sm font-medium"
        >
            View all comments →
        </router-link>
    </div>
</template>

<script>
export default {
    name: 'RecentComments',
    props: {
        comments: {
            type: Array,
            default: () => []
        }
    },
    setup() {
        const truncateText = (text, length) => {
            if (!text) return ''
            return text.length > length ? text.substring(0, length) + '...' : text
        }

        const statusBadgeClass = (status) => {
            const classes = {
                approved: 'bg-green-100 text-green-800',
                pending: 'bg-yellow-100 text-yellow-800',
                rejected: 'bg-red-100 text-red-800',
                spam: 'bg-gray-100 text-gray-800'
            }
            return classes[status] || classes.pending
        }

        return {
            truncateText,
            statusBadgeClass
        }
    }
}
</script>