<template>
  <div class="post-form">
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">
          {{ isEdit ? 'Edit Post' : 'Buat Post Baru' }}
        </h2>
      </div>
      
      <div class="p-6">
        <!-- Error Messages -->
        <div v-if="Object.keys(errors).length > 0" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <ul>
            <li v-for="(errorMessages, field) in errors" :key="field">
              <span v-for="(message, index) in errorMessages" :key="index">{{ message }}</span>
            </li>
          </ul>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          {{ successMessage }}
        </div>

        <!-- Global Error Message -->
        <div v-if="globalError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {{ globalError }}
        </div>

        <form @submit.prevent="submitForm">
          <!-- Form fields (sama seperti sebelumnya) -->
          <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Post *</label>
            <input
              type="text"
              id="title"
              v-model="form.title"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-500': errors.title }"
              placeholder="Masukkan judul post"
            />
          </div>

          <div class="mb-4">
            <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Konten *</label>
            <textarea
              id="content"
              v-model="form.content"
              required
              rows="10"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-500': errors.content }"
              placeholder="Tulis konten post di sini..."
            ></textarea>
          </div>

          <div class="mb-6">
            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Publikasi</label>
            <input
              type="datetime-local"
              id="published_at"
              v-model="form.published_at"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-500': errors.published_at }"
            />
            <p class="text-gray-500 text-sm mt-1">Kosongkan jika post masih draft</p>
          </div>

          <!-- Form Actions -->
          <div class="flex items-center justify-between">
            <a :href="cancelUrl" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
              Batal
            </a>
            
            <button
              type="submit"
              :disabled="loading"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading">Menyimpan...</span>
              <span v-else>{{ isEdit ? 'Update Post' : 'Buat Post' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    post: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      form: {
        title: '',
        content: '',
        published_at: ''
      },
      errors: {},
      loading: false,
      successMessage: '',
      globalError: ''
    };
  },
  computed: {
    isEdit() {
      return this.post !== null;
    },
    cancelUrl() {
      return '/admin/posts';
    }
  },
  mounted() {
    if (this.isEdit) {
      this.fillForm();
    }
  },
  methods: {
    fillForm() {
      this.form = {
        title: this.post.title,
        content: this.post.content,
        published_at: this.post.published_at ? this.formatDateTimeLocal(this.post.published_at) : ''
      };
    },
    
    formatDateTimeLocal(dateTime) {
      const date = new Date(dateTime);
      const localDateTime = new Date(date.getTime() - (date.getTimezoneOffset() * 60000));
      return localDateTime.toISOString().slice(0, 16);
    },
    
    async submitForm() {
      this.loading = true;
      this.errors = {};
      this.successMessage = '';
      this.globalError = '';
      
      try {
        let url, method;
        
        if (this.isEdit) {
          url = `/admin/posts/${this.post.id}`;
          method = 'put';
        } else {
          url = '/admin/posts';
          method = 'post';
        }

        const csrfToken = this.getCsrfToken();
        
        
        const response = await axios({
          method: method,
          url: url,
          data: this.form,
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        
        // Redirect ke halaman posts index setelah berhasil
        window.location.href = '/admin/posts?success=' + encodeURIComponent(
          this.isEdit ? 'Post berhasil diperbarui!' : 'Post berhasil dibuat!'
        );
        
      } catch (error) {
        console.error('Full error object:', error);
        
        // PERBAIKAN: Handle error dengan safe way
        if (error.response) {
          // Server responded with error status
          if (error.response.status === 422) {
            this.errors = error.response.data.errors || {};
            this.globalError = 'Terdapat kesalahan dalam input data';
          } else if (error.response.status === 500) {
            this.globalError = 'Terjadi kesalahan server. Silakan coba lagi.';
          } else {
            this.globalError = error.response.data.message || `Error: ${error.response.status}`;
          }
        } else if (error.request) {
          // Request was made but no response received
          this.globalError = 'Tidak ada response dari server. Periksa koneksi internet Anda.';
        } else {
          // Something else happened
          this.globalError = 'Terjadi kesalahan: ' + error.message;
        }
        
        // Show toast notification
        if (typeof this.$toast !== 'undefined') {
          this.$toast.error(this.globalError);
        }
        
      } finally {
        this.loading = false;
      }
    },
    
    getCsrfToken() {
      // Method 1: Try to get from meta tag
      const metaTag = document.querySelector('meta[name="csrf-token"]');
      if (metaTag) {
        return metaTag.getAttribute('content');
      }
      
      // Method 2: Try to get from Laravel global variable (if available)
      if (window.Laravel && window.Laravel.csrfToken) {
        return window.Laravel.csrfToken;
      }
      
      // Method 3: Try to get from cookie
      const cookieValue = this.getCookie('XSRF-TOKEN');
      if (cookieValue) {
        return decodeURIComponent(cookieValue);
      }
      
      // Method 4: Fallback - try to make a request to get the token
      console.warn('CSRF token not found, using fallback');
      return '';
    },
    
    getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(';').shift();
    },

    handleError(error) {
      if (error.response) {
        // Server responded with error status
        if (error.response.status === 422) {
          this.errors = error.response.data.errors || {};
          this.globalError = 'Terdapat kesalahan dalam input data';
        } else if (error.response.status === 500) {
          this.globalError = 'Terjadi kesalahan server. Silakan coba lagi.';
        } else {
          this.globalError = error.response.data.message || `Error: ${error.response.status}`;
        }
      } else if (error.request) {
        // Request was made but no response received
        this.globalError = 'Tidak ada response dari server. Periksa koneksi internet Anda.';
      } else {
        // Something else happened
        this.globalError = 'Terjadi kesalahan: ' + error.message;
      }
      
      // Show toast notification if available
      if (typeof this.$toast !== 'undefined') {
        this.$toast.error(this.globalError);
      }
    }
  }
};
</script>