<template>
  <div class="flex items-center justify-between">
    <div class="flex-1 flex justify-between sm:hidden">
      <a v-if="pagination.prev_page_url" :href="pagination.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"> Previous </a>
      <a v-else class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed"> Previous </a>
      
      <a v-if="pagination.next_page_url" :href="pagination.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"> Next </a>
      <a v-else class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed"> Next </a>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing
          <span class="font-medium">{{ pagination.from }}</span>
          to
          <span class="font-medium">{{ pagination.to }}</span>
          of
          <span class="font-medium">{{ pagination.total }}</span>
          results
        </p>
      </div>
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <a v-for="(link, index) in pagination.links" :key="index" :href="link.url" :class="{
            'bg-blue-50 border-blue-500 text-blue-600 z-10': link.active,
            'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active && link.url,
            'text-gray-300 cursor-not-allowed': !link.url
          }" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium" v-html="link.label"></a>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    links: {
      type: Object,
      required: true
    }
  },
  computed: {
    pagination() {
      return this.links;
    }
  }
}
</script>