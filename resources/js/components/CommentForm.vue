<template>
  <div class="comment-form bg-white rounded-lg p-6">
    <h3 class="text-xl font-semibold text-gray-900 mb-4">Tambah Komentar</h3>
    
    <form @submit.prevent="submitComment" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
          <input
            type="text"
            id="name"
            v-model="form.name"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Masukkan nama Anda"
          >
        </div>
        
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            type="email"
            id="email"
            v-model="form.email"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Masukkan email Anda"
          >
        </div>
      </div>
      
      <div>
        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Komentar</label>
        <textarea
          id="content"
          v-model="form.content"
          required
          rows="4"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          placeholder="Tulis komentar Anda di sini..."
        ></textarea>
      </div>
      
      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
      >
        <span v-if="loading">Mengirim...</span>
        <span v-else>Kirim Komentar</span>
      </button>
    </form>
  </div>
</template>

<script>
export default {
  props: {
    postId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      form: {
        name: '',
        email: '',
        content: ''
      },
      loading: false
    }
  },
  methods: {
    async submitComment() {
      this.loading = true;
      
      try {
        const response = await axios.post('/comments', {
          ...this.form,
          post_id: this.postId
        });
        
        // Reset form
        this.form.name = '';
        this.form.email = '';
        this.form.content = '';
        
        // Emit event to refresh comments
        this.$root.$emit('refresh-comments');
        
        this.$toast.success('Komentar berhasil dikirim!');
      } catch (error) {
        console.error('Error submitting comment:', error);
        this.$toast.error('Gagal mengirim komentar. Silakan coba lagi.');
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>