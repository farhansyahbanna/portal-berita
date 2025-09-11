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

            <!-- Comment List Component -->
            <CommentList 
                :comments="post.comments" 
                :loading="loading"
                :postId="parseInt(id)"
                @refresh-comments="fetchPost"
            />

            <!-- Comment Form Component -->
            <CommentForm 
                :post-id="post.id"
                @comment-submitted="handleCommentSubmitted"
            />
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
import { formatDate } from '../../utils/helpers'
import { usePostsStore } from '../../stores/posts'
import PostCard from './PostCard.vue'
import LoadingSpinner from '../Shared/LoadingSpinner.vue'
import CommentList from '../Comments/CommentList.vue'
import CommentForm from '../Comments/CommentForm.vue'

export default {
    name: 'PostDetail',
    components: {
        PostCard,
        LoadingSpinner,
        CommentList,
        CommentForm
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
        
        const relatedPosts = ref([])
        const loading = ref(false)
        const error = ref('')

        const fetchPost = async () => {
            loading.value = true
            error.value = ''

            try {
                await postsStore.fetchPost(props.id)
                await fetchRelatedPosts()
            } catch (err) {
                error.value = err.response?.data?.message || 'Gagal memuat artikel'
                console.error('Error fetching post:', err)
            } finally {
                loading.value = false
            }
        }

        const fetchComments = async () => {
            try {
                loadingComments.value = true
                await postStore.fetchPostComments(props.id)
                comments.value = postStore.currentPostComments
            } catch (err) {
                console.error('Gagal memuat komentar:', err)
            } finally {
                loadingComments.value = false
            }
        }

        const fetchRelatedPosts = async () => {
            try {
                await postsStore.fetchPosts({ per_page: 4, exclude: props.id })
                relatedPosts.value = posts.value.filter(p => p.id !== props.id)
            } catch (err) {
                console.error('Error fetching related posts:', err)
            }
        }

        const handleCommentSubmitted = () => {
            // Refresh post to get updated comments
            fetchPost()
        }

        const sharePost = () => {
            if (navigator.share) {
                navigator.share({
                    title: currentPost.value.title,
                    text: currentPost.value.excerpt,
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

         watch(() => props.id, async (newId) => {
            if (newId) {
                await fetchPost()
                await fetchComments()
            }
        })

        return {
            post: currentPost,
            relatedPosts,
            loading,
            error,
            formatDate,
            handleCommentSubmitted,
            sharePost,
            fetchComments
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
</style>