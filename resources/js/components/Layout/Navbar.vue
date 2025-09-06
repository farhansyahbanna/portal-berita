<template>
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <router-link to="/" class="text-xl font-bold text-blue-600">
                        Portal Berita
                    </router-link>
                </div>
                
                <div class="flex items-center space-x-4">
                    <router-link 
                        v-for="item in navigation"
                        :key="item.name"
                        :to="item.to"
                        class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
                    >
                        {{ item.name }}
                    </router-link>
                    
                    <template v-if="user">
                        <router-link 
                            v-if="user.isAdmin || user.isEditor"
                            :to="user.isAdmin ? '/admin/dashboard' : '/admin/editor-dashboard'"
                            class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
                        >
                            {{ user.isAdmin ? 'Admin Panel' : 'Editor Panel' }}
                        </router-link>
                        <button 
                            @click="logout"
                            class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
                        >
                            Logout
                        </button>
                    </template>
                    <template v-else>
                        <router-link 
                            to="/login"
                            class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
                        >
                            Login
                        </router-link>
                    </template>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import { useAuthStore } from '../../stores/auth'
import { storeToRefs } from 'pinia'

export default {
    name: 'Navbar',
    setup() {
        const authStore = useAuthStore()
        const { user, isAuthenticated, isAdmin, isEditor } = storeToRefs(authStore)
        
        const logout = async () => {
            try {
                await authStore.logout()
                window.location.href = '/login'
            } catch (error) {
                console.error('Logout error:', error)
            }
        }
        
        return {
            user,
            isAuthenticated,
            isAdmin,
            isEditor,
            logout
        }
    }
}
</script>