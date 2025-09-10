import { ref, computed } from 'vue';
import { useStore } from 'vuex';
import { useApi } from './useApi';
import { usePagination } from './usePagination';

export function usePosts() {
    const store = useStore();
    const { makeRequest, loading, error } = useApi();
    const { currentPage, perPage, totalItems, setPage, setTotalItems } = usePagination();

    const posts = computed(() => store.state.posts.items);
    const currentPost = computed(() => store.state.posts.current);

    const fetchPosts = async (filters = {}) => {
        const params = {
            page: currentPage.value,
            per_page: perPage.value,
            ...filters
        };

        const response = await makeRequest('/api/posts', 'GET', null, { params });
        store.commit('posts/SET_POSTS', response.data);
        setTotalItems(response.meta.total);
        return response;
    };

    const fetchPost = async (id) => {
        const response = await makeRequest(`/api/posts/${id}`);
        store.commit('posts/SET_CURRENT_POST', response.data);
        return response;
    };

    const createPost = async (postData) => {
        const response = await makeRequest('/api/posts', 'POST', postData);
        store.commit('posts/ADD_POST', response.data);
        return response;
    };

    const updatePost = async (id, postData) => {
        const response = await makeRequest(`/api/posts/${id}`, 'PUT', postData);
        store.commit('posts/UPDATE_POST', response.data);
        return response;
    };

    const deletePost = async (id) => {
        await makeRequest(`/api/posts/${id}`, 'DELETE');
        store.commit('posts/REMOVE_POST', id);
    };

    return {
        posts,
        currentPost,
        loading,
        error,
        currentPage,
        perPage,
        totalItems,
        setPage,
        fetchPosts,
        fetchPost,
        createPost,
        updatePost,
        deletePost
    };
}