// stores/auth.js
import { defineStore } from 'pinia'
import { api } from '../plugins/api'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('auth_token') || null,
        isAuthenticated: !!localStorage.getItem('auth_token')
    }),

    getters: {
        isAdmin: (state) => state.user?.role === 'admin',
        isEditor: (state) => state.user?.role === 'editor'
    },

    actions: {
        async login(credentials) {
            try {
                
                const response = await api.post('/api/login', credentials)
                
                let token, userData;
                
                if (response.data.token) {
                    token = response.data.token;
                    userData = response.data.user;
                } else if (response.data.data && response.data.data.token) {
                    token = response.data.data.token;
                    userData = response.data.data.user;
                } else if (response.data.access_token) {
                    token = response.data.access_token;
                    userData = response.data.user;
                } else {
                   
                    throw new Error('Invalid response format from server');
                }
                
                
                this.token = token;
                this.user = userData;
                this.isAuthenticated = true;
                
                
                localStorage.setItem('auth_token', this.token);
                
                return response;
            } catch (error) {
                console.error('Login error in store:', error);
                
                k
                if (error.response) {
                    console.error('Response data:', error.response.data);
                    console.error('Response status:', error.response.status);
                    console.error('Response headers:', error.response.headers);
                    
                    
                    error.message = error.response.data.message || error.response.data.error || error.message;
                }
                
                this.logout();
                throw error;
            }
        },
        
        async logout() {
            
            localStorage.removeItem('auth_token');
            
            
            this.token = null;
            this.user = null;
            this.isAuthenticated = false;
        },
        
        async fetchUser() {
            if (!this.token) return;
            
            try {
                const response = await api.get('/api/user');
                this.user = response.data;
            } catch (error) {
                console.error('Fetch user error:', error);
                this.logout();
                throw error;
            }
        }
    }
});