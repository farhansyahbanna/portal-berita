<template>
    <AppLayout>
        <router-view />
    </AppLayout>
</template>

<script>
import AppLayout from './Layout/AppLayout.vue'

export default {
    components: {
        AppLayout
    }
}
</script>