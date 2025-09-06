import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';

export function useAuth() {
    const store = useStore();
    const router = useRouter();

    const user = computed(() => store.state.auth.user);
    const isAuthenticated = computed(() => store.getters['auth/isAuthenticated']);
    const isAdmin = computed(() => store.getters['auth/isAdmin']);
    const isEditor = computed(() => store.getters['auth/isEditor']);

    const login = async (credentials) => {
        await store.dispatch('auth/login', credentials);
    };

    const logout = async () => {
        await store.dispatch('auth/logout');
        router.push({ name: 'home' });
    };

    const register = async (userData) => {
        await store.dispatch('auth/register', userData);
    };

    const forgotPassword = async (email) => {
        await store.dispatch('auth/forgotPassword', email);
    };

    const resetPassword = async (resetData) => {
        await store.dispatch('auth/resetPassword', resetData);
    };

    return {
        user,
        isAuthenticated,
        isAdmin,
        isEditor,
        login,
        logout,
        register,
        forgotPassword,
        resetPassword
    };
}