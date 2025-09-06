import { defineStore } from 'pinia'
import { api } from '../composables/useApi'

export const useCommentsStore = defineStore('comments', {
    state: () => ({
        comments: [],
        loading: false,
        error: null
    }),

    actions: {
        async createComment(commentData) {
            this.loading = true
            this.error = null
            
            try {
                const response = await api.post(`/api/posts/${commentData.post_id}/comments`, commentData)
                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Gagal mengirim komentar'
                throw error
            } finally {
                this.loading = false
            }
        }
    }
})