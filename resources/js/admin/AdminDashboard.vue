<template>
    <div class="admin-dashboard">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Total Posts</h3>
                <p class="text-3xl font-bold text-blue-600">{{ stats.total_posts }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Total Comments</h3>
                <p class="text-3xl font-bold text-green-600">{{ stats.total_comments }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Published Posts</h3>
                <p class="text-3xl font-bold text-purple-600">{{ stats.published_posts }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Pending Posts</h3>
                <p class="text-3xl font-bold text-yellow-600">{{ stats.pending_posts }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Posts</h3>
                <div class="space-y-3">
                    <div v-for="post in recent_posts" :key="post.id" class="border-b border-gray-100 pb-3 last:border-b-0">
                        <h4 class="font-medium text-gray-900">{{ post.title }}</h4>
                        <p class="text-sm text-gray-600">By {{ post.user.name }} • {{ formatDate(post.created_at) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Comments</h3>
                <div class="space-y-3">
                    <div v-for="comment in recent_comments" :key="comment.id" class="border-b border-gray-100 pb-3 last:border-b-0">
                        <p class="text-gray-900">{{ comment.content }}</p>
                        <p class="text-sm text-gray-600">By {{ comment.name }} • {{ formatDate(comment.created_at) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        stats: {
            type: Object,
            required: true
        },
        recent_posts: {
            type: Array,
            required: true
        },
        recent_comments: {
            type: Array,
            required: true
        }
    },
    methods: {
        formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
    }
}
</script>