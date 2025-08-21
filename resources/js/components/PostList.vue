<template>
  <div class="post-list grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Looping posts -->
    <div
      v-for="post in posts.data"
      :key="post.id"
      class="post-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col"
    >
      <!-- Gambar -->
      <div class="h-48 bg-gray-200 flex items-center justify-center">
        <img
          v-if="post.image"
          :src="post.image"
          alt="Thumbnail"
          class="object-cover w-full h-full"
        />
        <span v-else class="text-gray-400">No Image</span>
      </div>

      <!-- Tanggal publikasi -->
      <div class="text-center text-sm text-gray-500 mt-2">
        {{ formatDate(post.published_at) }} 
      </div>

      <!-- Konten -->
      <div class="p-5 flex flex-col flex-grow">
        <!-- Judul -->
        <h2 class="text-xl font-bold text-gray-900 mb-2 text-center">
          <a
            :href="`/posts/${post.id}`"
            class="hover:text-blue-600 transition-colors duration-200"
          >
            {{ post.title }}
          </a>
        </h2>

        <!-- Sekilas isi konten -->
        <p class="text-gray-600 text-sm mb-4 leading-relaxed line-clamp-3 text-center">
          {{ post.content }}
        </p>

        <!-- Tombol Selengkapnya -->
        
          <a
            :href="`/posts/${post.id}`"
            class="inline-block text-blue-400 text-sm font-medium px-4 py-2 rounded-lg  hover:underline transition-colors duration-200"
          >
            Selengkapnya
          </a>
        

        
      </div>
    </div>

    <!-- Jika tidak ada post -->
    <div v-if="posts.data.length === 0" class="col-span-full text-center py-12">
      <p class="text-gray-500 text-lg">Tidak ada berita yang ditemukan.</p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    posts: {
      type: Object,
      required: true,
    },
  },
  methods: {
    formatDate(date) {
      return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    },
  },
};
</script>
