import './bootstrap';
import { createApp } from 'vue';

// Admin Components
import AdminDashboard from './admin/AdminDashboard.vue';
import PostListAdmin from './admin/PostListAdmin.vue';
import PostForm from './admin/PostForm.vue';
import Pagination from './components/Pagination.vue';

const app = createApp({});

// Register components
app.component('AdminDashboard', AdminDashboard);
app.component('PostListAdmin', PostListAdmin);
app.component('PostForm', PostForm);
app.component('Pagination', Pagination);

// Mount the app
const mountApp = () => {
    const appElement = document.getElementById('admin-app');
    if (appElement) {
        app.mount('#admin-app');
    }
};

// Mount when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountApp);
} else {
    mountApp();
}