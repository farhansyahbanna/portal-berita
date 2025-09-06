<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Berita Terbaru</h1>
            <p class="text-xl text-gray-600">Temukan berita dan artikel terbaru dari berbagai kategori</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
            <p class="text-gray-500 mt-4">Memuat artikel...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p>{{ error }}</p>
            </div>
            <button @click="fetchPosts" class="text-blue-600 hover:text-blue-800 font-medium">
                Coba Lagi
            </button>
        </div>

        <!-- Success State -->
        <div v-else>
            <!-- Search Input -->
            <div class="mb-6 max-w-md mx-auto">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari berita..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    @input="handleSearch"
                />
            </div>

            <!-- Posts Grid -->
            <div v-if="posts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <PostCard 
                    v-for="post in posts"
                    :key="post.id"
                    :post="post"
                />
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <div class="bg-gray-50 rounded-lg p-8">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">Belum ada artikel yang tersedia.</p>
                    <p class="text-gray-400 text-sm mt-2">Silakan kembali lagi nanti.</p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="pagination && pagination.last_page > 1" class="mt-8 flex justify-center">
                <div class="flex space-x-2">
                    <button
                        v-for="page in pagination.last_page"
                        :key="page"
                        @click="goToPage(page)"
                        :class="[
                            'px-3 py-1 rounded-md',
                            pagination.current_page === page
                                ? 'bg-blue-600 text-white'
                                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                        ]"
                    >
                        {{ page }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { usePostsStore } from '../../stores/posts'
import PostCard from './PostCard.vue'

export default {
    name: 'PostList',
    components: { PostCard },

    setup() {
        const postsStore = usePostsStore()
        const { posts, pagination } = storeToRefs(postsStore) // ⬅️ reactive binding

        const loading = ref(false)
        const error = ref(null)
        const searchQuery = ref('')
        const searchTimeout = ref(null)

        const fetchPosts = async (page = 1) => {
            loading.value = true
            error.value = null

            try {
                const params = { page, per_page: 9 }
                if (searchQuery.value) params.search = searchQuery.value

                await postsStore.fetchPosts(params)
                
            } catch (err) {
                
                error.value = err.response?.data?.message || 'Gagal memuat artikel'
            } finally {
                loading.value = false
            }
        }

        const handleSearch = () => {
            clearTimeout(searchTimeout.value)
            searchTimeout.value = setTimeout(() => {
                fetchPosts(1)
            }, 500)
        }

        const goToPage = (page) => {
            fetchPosts(page)
            window.scrollTo({ top: 0, behavior: 'smooth' })
        }

        onMounted(() => {
            fetchPosts()
        })

        return {
            posts, pagination, loading, error, searchQuery,
            fetchPosts, handleSearch, goToPage
        }
    }
}

</script>