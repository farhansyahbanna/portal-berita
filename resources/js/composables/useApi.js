import { ref } from 'vue';
import axios from 'axios'

// Create axios instance
const api = axios.create({
    baseURL: '/',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})

// Request interceptor
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token')
        if (token) {
            config.headers.Authorization = `Bearer ${token}`
        }
        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

// Response interceptor
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('auth_token')
            window.location.href = '/login'
        }
        return Promise.reject(error)
    }
)

// Composable function
export function useApi() {
    const data = ref(null);
    const loading = ref(false);
    const error = ref(null);

    const makeRequest = async (url, method = 'GET', payload = null, config = {}) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await api({
                method,
                url,
                data: payload,
                ...config
            });
            
            data.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || err.message || 'An error occurred';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        data,
        loading,
        error,
        makeRequest
    };
}

export { api }