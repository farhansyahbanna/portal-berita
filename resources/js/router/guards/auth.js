import { useAuthStore } from '../../stores/auth'

export function authGuard(to, from, next) {
    const authStore = useAuthStore()
    
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'login' })
    } else {
        next()
    }
}
