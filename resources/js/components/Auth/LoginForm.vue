<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Login
                </h2>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="handleSubmit">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                            placeholder="Email address"
                            :class="{ 'border-red-500': errors.email }"
                        >
                        <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
                    </div>
                    
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                            placeholder="Password"
                            :class="{ 'border-red-500': errors.password }"
                        >
                        <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password }}</p>
                    </div>
                </div>

                <div v-if="authError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ authError }}
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                    >
                        <span v-if="loading">Loading...</span>
                        <span v-else>Sign in</span>
                    </button>
                </div>

                <div class="text-center">
                    <router-link to="/forgot-password" class="text-blue-600 hover:text-blue-500 text-sm">
                        Lupa password?
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useForm } from '../../composables/useForm'

export default {
    name: 'LoginForm',
    setup() {
        const router = useRouter()
        const authStore = useAuthStore()
        const authError = ref('')
        const loading = ref(false)

        const { form, errors, setError, clearErrors } = useForm({
            email: '',
            password: ''
        })

        // Cek jika user sudah login
        onMounted(() => {
            if (authStore.isAuthenticated && authStore.user) {
                redirectBasedOnRole();
            }
        })

        const redirectBasedOnRole = () => {
            if (!authStore.user) return;
            
            
            
            switch(authStore.user.role) {
                case 'admin':
                    router.push('/admin/dashboard').then(() => {
                        console.log('Navigation success');
                    }).catch(err => {
                        console.error('Navigation error:', err);
                    });

                    break;
                case 'editor':
                    router.push('/editor/dashboard');
                    break;
                default:
                    router.push('/');
            }
        }

        const handleSubmit = async () => {
            clearErrors()
            authError.value = ''
            loading.value = true

            try {
                await authStore.login(form);
                redirectBasedOnRole();
            } catch (error) {
                console.error('Login error:', error);
                
                if (error.response?.status === 422) {
                    const validationErrors = error.response.data.errors;
                    Object.keys(validationErrors).forEach(field => {
                        setError(field, validationErrors[field][0]);
                    });
                } else if (error.response?.data?.message) {
                    authError.value = error.response.data.message;
                } else if (error.message) {
                    authError.value = error.message;
                } else {
                    authError.value = 'Login failed. Please check your credentials and try again.';
                }
            } finally {
                loading.value = false;
            }
        }

        return {
            form,
            errors,
            authError,
            loading,
            handleSubmit
        }
    }
}
</script>