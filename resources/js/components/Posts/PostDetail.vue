<template>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
            <LoadingSpinner />
            <p class="text-gray-500 mt-4">Memuat artikel...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p>{{ error }}</p>
            </div>
            <router-link to="/posts" class="text-blue-600 hover:text-blue-800 font-medium">
                ← Kembali ke Daftar Berita
            </router-link>
        </div>

        <!-- Success State -->
        <div v-else-if="post" class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Post Header -->
            <div class="p-6 border-b border-gray-200">
                <nav class="flex items-center text-sm text-gray-500 mb-4">
                    <router-link to="/" class="hover:text-blue-600">Home</router-link>
                    <span class="mx-2">/</span>
                    <router-link to="/posts" class="hover:text-blue-600">Berita</router-link>
                    <span class="mx-2">/</span>
                    <span class="text-gray-900">{{ post.title }}</span>
                </nav>

                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ post.title }}</h1>
                
                <div class="flex items-center text-sm text-gray-600 space-x-4">
                    <span>Dipublikasikan: {{ formatDate(post.published_at) }}</span>
                    <span class="mx-2">•</span>
                    <span>Oleh: {{ post.user?.name || 'Admin' }}</span>
                    <span class="mx-2">•</span>
                    
                </div>
            </div>

            <!-- Post Content -->
            <div class="p-6">
                <div class="prose prose-lg max-w-none text-gray-800" v-html="post.content"></div>
            </div>

            <!-- Post Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <router-link to="/posts" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Daftar Berita
                    </router-link>

                    <div class="flex items-center space-x-4">
                        <button @click="sharePost" class="text-gray-500 hover:text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div v-if="post" class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Komentar</h2>

            <!-- Comment List -->
            <div v-if="post.comments && post.comments.length > 0" class="space-y-6 mb-8">
                <div v-for="comment in post.comments" :key="comment.id" class="comment-item bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-bold text-sm">
                                    {{ getInitials(comment.name) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">{{ comment.name }}</h4>
                                <span class="text-sm text-gray-500">{{ formatDate(comment.created_at, 'short') }}</span>
                            </div>
                            
                            <p class="text-gray-700 leading-relaxed">{{ comment.content }}</p>
                            
                            <div class="mt-2 flex items-center space-x-4">
                                <span class="text-xs text-gray-500">{{ comment.email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty Comments -->
            <div v-else class="text-center py-8 bg-gray-50 rounded-lg mb-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="text-gray-500">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
            </div>

            <!-- Add Comment Form -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tambah Komentar</h3>
                
                <form @submit.prevent="submitComment" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                            <input
                                id="name"
                                v-model="commentForm.name"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': commentErrors.name }"
                                placeholder="Masukkan nama Anda"
                            >
                            <p v-if="commentErrors.name" class="text-red-500 text-xs mt-1">{{ commentErrors.name }}</p>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input
                                id="email"
                                v-model="commentForm.email"
                                type="email"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': commentErrors.email }"
                                placeholder="Masukkan email Anda"
                            >
                            <p v-if="commentErrors.email" class="text-red-500 text-xs mt-1">{{ commentErrors.email }}</p>
                        </div>
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Komentar *</label>
                        <textarea
                            id="content"
                            v-model="commentForm.content"
                            required
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            :class="{ 'border-red-500': commentErrors.content }"
                            placeholder="Tulis komentar Anda di sini..."
                        ></textarea>
                        <p v-if="commentErrors.content" class="text-red-500 text-xs mt-1">{{ commentErrors.content }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">* Wajib diisi</p>
                        
                        <button
                            type="submit"
                            :disabled="commentLoading"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="commentLoading">Mengirim...</span>
                            <span v-else>Kirim Komentar</span>
                        </button>
                    </div>

                    <div v-if="commentSuccess" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        Komentar berhasil dikirim!
                    </div>

                    <div v-if="commentError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ commentError }}
                    </div>
                </form>
            </div>
        </div>

        <!-- Related Posts -->
        <div v-if="post && relatedPosts.length > 0" class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Berita Terkait</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <PostCard 
                    v-for="relatedPost in relatedPosts"
                    :key="relatedPost.id"
                    :post="relatedPost"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { formatDate, getInitials } from '../../utils/helpers'
import { usePostsStore } from '../../stores/posts'
import { useCommentsStore } from '../../stores/comments'
import PostCard from './PostCard.vue'
import LoadingSpinner from '../Shared/LoadingSpinner.vue'

export default {
    name: 'PostDetail',
    components: {
        PostCard,
        LoadingSpinner
    },
    props: {
        id: {
            type: [String, Number],
            required: true
        }
    },
    setup(props) {
        const route = useRoute()
        const router = useRouter()
        const postsStore = usePostsStore()
        const { currentPost, posts } = storeToRefs(postsStore)
        const commentsStore = useCommentsStore()

        
        const relatedPosts = ref([])
        const loading = ref(false)
        const error = ref('')

        // Comment form
        const commentForm = ref({
            name: '',
            email: '',
            content: ''
        })
        const commentErrors = ref({})
        const commentLoading = ref(false)
        const commentSuccess = ref(false)
        const commentError = ref('')

       const fetchPost = async () => {
            loading.value = true
            error.value = ''

            try {
                await postsStore.fetchPost(props.id) // ⬅️ isi postsStore.post
                await fetchRelatedPosts()
            } catch (err) {
                error.value = err.response?.data?.message || 'Gagal memuat artikel'
                console.error('Error fetching post:', err)
            } finally {
                loading.value = false
            }
        }

        const fetchRelatedPosts = async () => {
            try {
                await postsStore.fetchPosts({ per_page: 4, exclude: props.id })
                relatedPosts.value = posts.value.filter(p => p.id !== props.id) // ambil dari store
            } catch (err) {
                console.error('Error fetching related posts:', err)
            }
        }

        const submitComment = async () => {
            commentLoading.value = true
            commentErrors.value = {}
            commentSuccess.value = false
            commentError.value = ''

            try {
                await commentsStore.createComment({
                    post_id: props.id,
                    ...commentForm.value
                })

                commentSuccess.value = true
                commentForm.value = { name: '', email: '', content: '' }
                
                // Refresh post to get updated comments
                await fetchPost()
            } catch (err) {
                if (err.response?.status === 422) {
                    commentErrors.value = err.response.data.errors
                } else {
                    commentError.value = err.response?.data?.message || 'Gagal mengirim komentar'
                }
            } finally {
                commentLoading.value = false
            }
        }

        const sharePost = () => {
            if (navigator.share) {
                navigator.share({
                    title: post.value.title,
                    text: post.value.excerpt,
                    url: window.location.href
                })
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(window.location.href)
                alert('Link berita berhasil disalin!')
            }
        }

        onMounted(() => {
            fetchPost()
        })

        watch(() => props.id, (newId) => {
            if (newId) {
                fetchPost()
            }
        })

        return {
            post: currentPost,
            relatedPosts,
            loading,
            error,
            commentForm,
            commentErrors,
            commentLoading,
            commentSuccess,
            commentError,
            formatDate,
            getInitials,
            submitComment,
            sharePost
        }
    }
}
</script>

<style scoped>
.prose {
    line-height: 1.75;
}

.prose h2 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #1f2937;
}

.prose h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    color: #374151;
}

.prose p {
    margin-bottom: 1rem;
    color: #374151;
}

.prose ul {
    list-style-type: disc;
    list-style-position: inside;
    margin-bottom: 1rem;
}

.prose li {
    margin-bottom: 0.5rem;
}

.prose blockquote {
    border-left: 4px solid #3b82f6;
    padding-left: 1rem;
    font-style: italic;
    color: #6b7280;
    margin: 1rem 0;
}

.comment-item {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.comment-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>