import './bootstrap'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './components/App.vue'
import axios from 'axios'
import { useAuthStore } from './stores/auth'

// Set CSRF token for Axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Load user if token exists
const authStore = useAuthStore()
if (authStore.token) {
    authStore.fetchUser().catch(error => {
        console.error('Failed to fetch user:', error)
    })
}

app.mount('#app')