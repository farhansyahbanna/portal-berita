import { useAuthStore } from './auth'
import { usePostsStore } from './posts'
import { useCommentsStore } from './comments'
import { useUIStore } from './ui'

// Export individual stores
export {
    useAuthStore,
    usePostsStore,
    useCommentsStore,
    useUIStore
}

// Export store function for global usage
export const useStore = () => ({
    auth: useAuthStore(),
    posts: usePostsStore(),
    comments: useCommentsStore(),
    ui: useUIStore()
})