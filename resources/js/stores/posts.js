import { defineStore } from 'pinia'
import { api } from '../composables/useApi'

export const usePostsStore = defineStore('posts', {
    state: () => ({
        posts: [],
        currentPost: null,
        pagination: null,
        loading: false,
        error: null,
        filters: {
            search: '',
            category: '',
            status: '',
            page: 1
        }
    }),

    getters: {
        hasPosts: (state) => state.posts.length > 0,
        totalPosts: (state) => state.pagination?.total || 0
    },

    actions: {
        async fetchPosts(params = {}) {
            this.loading = true
            this.error = null
            
            try {
                const response = await api.get('/api/posts', { params })
                this.posts = response.data.data
                this.pagination = response.data.meta
                
                
                return response
            } catch (error) {
                this.error = error.response?.data?.message || 'Gagal memuat posts'
                throw error
            } finally {
                this.loading = false
            }
            
        },
        

        async fetchPost(id) {
            this.loading = true
            this.error = null
            
            try {
                const response = await api.get(`/api/posts/${id}`)
                this.currentPost = response.data
                return response
            } catch (error) {
                this.error = error.response?.data?.message || 'Gagal memuat post'
                throw error
            } finally {
                this.loading = false
            }
        },

        async createPost(postData) {
            this.loading = true
            this.error = null
            
            try {
                const response = await api.post('/api/posts', postData)
                this.posts.unshift(response.data)
                return response
            } catch (error) {
                this.error = error.response?.data?.message || 'Gagal membuat post'
                throw error
            } finally {
                this.loading = false
            }
        },

        async updatePost({ id, ...postData }) {
            this.loading = true
            this.error = null
            
            try {
                const response = await api.put(`/api/posts/${id}`, postData)
                const index = this.posts.findIndex(post => post.id === id)
                if (index !== -1) {
                    this.posts[index] = response.data
                }
                return response
            } catch (error) {
                this.error = error.response?.data?.message || 'Gagal mengupdate post'
                throw error
            } finally {
                this.loading = false
            }
        },

        async deletePost(id) {
            this.loading = true
            this.error = null
            
            try {
                await api.delete(`/api/posts/${id}`)
                this.posts = this.posts.filter(post => post.id !== id)
            } catch (error) {
                this.error = error.response?.data?.message || 'Gagal menghapus post'
                throw error
            } finally {
                this.loading = false
            }
        },

        setFilters(filters) {
            this.filters = { ...this.filters, ...filters }
        },

        clearFilters() {
            this.filters = {
                search: '',
                category: '',
                status: '',
                page: 1
            }
        }
    }
})