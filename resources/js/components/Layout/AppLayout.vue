<template>
    <div class="min-h-screen bg-gray-50 font-inter">
        <Navbar />
        <main>
            <slot />
        </main>
        <Footer />
    </div>
</template>

<script>
import Navbar from './Navbar.vue'
import Footer from './Footer.vue'

export default {
    components: {
        Navbar,
        Footer
    }
}
</script>