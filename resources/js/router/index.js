import { createRouter, createWebHistory } from 'vue-router'
import routes from './routes'
import { useAuthStore } from '../stores/auth'
import { authGuard,  } from './guards/auth'
import { adminGuard } from './guards/admin'
import { editorGuard } from './guards/editor'


const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return { top: 0 }
        }
    }
})

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()
    
    // Load user if token exists but user data is not loaded
    if (authStore.token && !authStore.user) {
        await authStore.fetchUser()
    }

    if (to.meta.requiresAuth) {
        return authGuard(to, from, next)
    }

    if (to.meta.requiresAdmin) {
        return adminGuard(to, from, next)
    }

    if (to.meta.requiresEditor) {
        return editorGuard(to, from, next)
    }



    next()
})

export default router