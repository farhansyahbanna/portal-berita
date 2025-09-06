<template>
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
        <div class="aspect-w-16 aspect-h-9 bg-gray-100 flex items-center justify-center">
            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-2 hover:text-blue-600 transition-colors line-clamp-2">
                <router-link :to="`/posts/${post.id}`">{{ post.title }}</router-link>
            </h2>
            
            <p class="text-gray-500 text-xs mb-4">
                {{ formatDate(post.published_at || post.created_at) }}
            </p>
            
            <p class="text-gray-700 text-sm leading-relaxed line-clamp-3 mb-4">
                {{ generateExcerpt(post.content) }}
            </p>
            
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">
                    Oleh: {{ post.user?.name || 'Admin' }}
                </span>
                
                <router-link
                    :to="`/posts/${post.id}`"
                    class="text-blue-500 text-sm font-semibold hover:underline"
                >
                    Baca Selengkapnya →
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
import { generateExcerpt, formatDate } from '../../utils/helpers'

export default {
    name: 'PostCard',
    props: { post: Object },
    methods: {
        generateExcerpt(content) {
            return generateExcerpt(content, 100)
        },
        formatDate(dateString) {
            return formatDate(dateString, {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            })
        }
    }
}
</script>