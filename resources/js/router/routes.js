import { authGuard } from './guards/auth'
import { adminGuard } from './guards/admin'

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('../components/Posts/PostList.vue')
    },
    {
        path: '/posts',
        name: 'posts.index',
        component: () => import('../components/Posts/PostList.vue')
    },
    {
        path: '/posts/:id',
        name: 'posts.show',
        component: () => import('../components/Posts/PostDetail.vue'),
        props: true
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../components/Auth/LoginForm.vue'),
        meta: { guest: true }
    },
    // Admin Routes
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: () => import('../components/Admin/Dashboard/AdminDashboard.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
        beforeEnter: (to, from, next) => {
            authGuard(to, from, () => {
            adminGuard(to, from, next)
            })
        }
    },
    {
        path: '/admin/posts',
        name: 'admin.posts.index',
        component: () => import('../components/Admin/Posts/AdminPostList.vue'),
        meta: { requiresAuth: true, requiresEditor: true },
        beforeEnter: (to, from, next) => {
            authGuard(to, from, () => {
            adminGuard(to, from, next)
            })
        }
    },
    {
        path: '/admin/posts/create',
        name: 'admin.posts.create',
        component: () => import('../components/Admin/Posts/AdminPostForm.vue'),
        meta: { requiresAuth: true, requiresEditor: true },
         beforeEnter: (to, from, next) => {
            authGuard(to, from, () => {
            adminGuard(to, from, next)
            })
        }
    },
    {
        path: '/admin/posts/:id/edit',
        name: 'admin.posts.edit',
        component: () => import('../components/Admin/Posts/AdminPostForm.vue'),
        meta: { requiresAuth: true, requiresEditor: true },
        props: (route) => ({ 
            postId: route.params.id,
            isEdit: true 
        })
        
    },
    {
        path: '/admin/comments',
        name: 'admin.comments',
        component: () => import('../components/Admin/Comments/AdminCommentList.vue'),
        meta: { requiresAuth: true, requiresAdmin: true }
    },

    {
        path: '/admin/users',
        name: 'admin.users',
        component: () => import('../components/Admin/Users/AdminUserList.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },

    },
    // Editor Dashboard
    {
        path: '/editor/dashboard',
        name: 'editor.dashboard',
        component: () => import('../components/Editor/EditorDashboard.vue'),
        meta: { requiresAuth: true, requiresEditor: true }
    },
    {
        path: '/editor/posts',
        name: 'editor.posts',
        component: () => import('../components/Editor/EditorPostList.vue'),
        meta: { requiresAuth: true, requiresEditor: true }
    },
    {
        path: '/editor/posts/create',
        name: 'editor.posts.create',
        component: () => import('../components/Editor/EditorPostForm.vue'),
        meta: { requiresAuth: true, requiresEditor: true }
    },
    {
        path: '/editor/posts/:id/edit',
        name: 'editor.posts.edit',
        component: () => import('../components/Editor/EditorPostForm.vue'),
        meta: { requiresAuth: true, requiresEditor: true },
        props: true
    }
]

export default routes