import { useAuthStore } from '../../stores/auth'

/**
 * Guard untuk memastikan hanya user dengan role editor atau admin yang bisa mengakses
 */
export function editorGuard(to, from, next) {
    const authStore = useAuthStore()
    
    // Jika user tidak terautentikasi, redirect ke login
    if (!authStore.isAuthenticated) {
        next({ name: 'login', query: { redirect: to.fullPath } })
        return
    }
    
    // Jika user bukan admin atau editor, redirect ke unauthorized
    if (!authStore.isAdmin && !authStore.isEditor) {
        console.warn('Access denied: User is not an editor or admin')
        next({ name: 'unauthorized' })
        return
    }
    
    // Jika user adalah admin atau editor, izinkan akses
    next()
}
