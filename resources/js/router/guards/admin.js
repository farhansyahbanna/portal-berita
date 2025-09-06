import { useAuthStore } from '../../stores/auth'

export function adminGuard(to, from, next) {
    const authStore = useAuthStore()
    
    if (to.meta.requiresAdmin && !authStore.isAdmin) {
        next({ name: 'unauthorized' })
    } else {
        next()
    }
}