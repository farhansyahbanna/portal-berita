<template>
  <div class="comment-list">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Komentar ({{ comments.length }})</h2>
    
    <div v-if="loading" class="text-center py-8">
      <p class="text-gray-500">Memuat komentar...</p>
    </div>
    
    <div v-else>
      <div v-if="comments.length === 0" class="text-center py-8 bg-gray-50 rounded-lg">
        <p class="text-gray-500 text-lg">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
      </div>
      
      <div v-else class="space-y-6">
        <div v-for="comment in comments" :key="comment.id" class="comment-item bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-blue-600 font-bold text-lg">{{ getInitials(comment.name) }}</span>
              </div>
            </div>
            
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-3">
                <div>
                  <h4 class="font-semibold text-gray-900 text-lg">{{ comment.name }}</h4>
                  <p class="text-sm text-gray-500">{{ comment.email }}</p>
                </div>
                <span class="text-sm text-gray-400">{{ formatDate(comment.created_at) }}</span>
              </div>
              
              <p class="text-gray-700 leading-relaxed text-base">{{ comment.content }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
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
      comments: [],
      loading: true,
      error: null
    }
  },
  mounted() {
    this.loadComments();
    
    // Listen for custom event to refresh comments
    this.$root.$on('refresh-comments', () => {
      this.loadComments();
    });
  },
  methods: {
    async loadComments() {
      this.loading = true;
      this.error = null;
      
      try {
        console.log('Loading comments for post ID:', this.postId);
        
        const response = await axios.get(`/posts/${this.postId}/comments`);
        console.log('Comments response:', response.data);
        
        this.comments = response.data;
        
      } catch (error) {
        console.error('Error loading comments:', error);
        this.error = 'Gagal memuat komentar';
        this.$toast.error('Gagal memuat komentar');
      } finally {
        this.loading = false;
      }
    },
    
    formatDate(date) {
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    
    getInitials(name) {
      return name.split(' ').map(word => word[0]).join('').toUpperCase().substring(0, 2);
    }
  }
}
</script>

<style scoped>
.comment-item {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.comment-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>