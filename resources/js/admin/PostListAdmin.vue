<template>
  <div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
      <h2 class="text-lg font-medium text-gray-900">Daftar Post</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="post in posts.data" :key="post.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ post.title }}</div>
            </td>
    <td class="px-6 py-4 whitespace-nowrap">
              <span :class="{
                'bg-green-100 text-green-800': post.published_at,
                'bg-yellow-100 text-yellow-800': !post.published_at
              }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ post.published_at ? 'Published' : 'Draft' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(post.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <a :href="`/admin/posts/${post.id}/edit`" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
              <button @click="deletePost(post)" class="text-red-600 hover:text-red-900">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
      <Pagination :links="posts.links" />
    </div>
  </div>
</template>

<script>
import Pagination from '../components/Pagination.vue';

export default {
  components: {
    Pagination
  },
  props: {
    posts: {
      type: Object,
      required: true
    }
  },
  methods: {
    formatDate(date) {
      return new Date(date).toLocaleDateString('id-ID');
    },
    async deletePost(post) {
      if (confirm('Apakah Anda yakin ingin menghapus post ini?')) {
        try {
          await axios.delete(`/admin/posts/${post.id}`);
          this.$emit('post-deleted');
          this.$toast.success('Post berhasil dihapus');
        } catch (error) {
          this.$toast.error('Gagal menghapus post');
        }
      }
    }
  }
}
</script>