import { ref, computed, watch } from 'vue';

export function usePagination(initialPage = 1, initialPerPage = 10) {
    const currentPage = ref(initialPage);
    const perPage = ref(initialPerPage);
    const totalItems = ref(0);
    const totalPages = computed(() => Math.ceil(totalItems.value / perPage.value));

    const offset = computed(() => (currentPage.value - 1) * perPage.value);

    const setPage = (page) => {
        if (page >= 1 && page <= totalPages.value) {
            currentPage.value = page;
        }
    };

    const nextPage = () => {
        if (currentPage.value < totalPages.value) {
            currentPage.value++;
        }
    };

    const prevPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--;
        }
    };

    const setPerPage = (value) => {
        perPage.value = value;
        currentPage.value = 1; // Reset to first page when changing items per page
    };

    const setTotalItems = (total) => {
        totalItems.value = total;
    };

    // Reset pagination when filters change
    watch([perPage], () => {
        currentPage.value = 1;
    });

    return {
        currentPage,
        perPage,
        totalItems,
        totalPages,
        offset,
        setPage,
        nextPage,
        prevPage,
        setPerPage,
        setTotalItems
    };
}