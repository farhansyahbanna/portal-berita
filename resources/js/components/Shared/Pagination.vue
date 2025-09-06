<template>
  <div class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
    <div class="flex justify-between flex-1 sm:hidden">
      <button 
        @click="$emit('page-changed', currentPage - 1)"
        :disabled="currentPage === 1"
        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
        :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
      >
        Previous
      </button>
      <button 
        @click="$emit('page-changed', currentPage + 1)"
        :disabled="currentPage === lastPage"
        class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
        :class="{ 'opacity-50 cursor-not-allowed': currentPage === lastPage }"
      >
        Next
      </button>
    </div>
    
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing
          <span class="font-medium">{{ meta.from || 0 }}</span>
          to
          <span class="font-medium">{{ meta.to || 0 }}</span>
          of
          <span class="font-medium">{{ meta.total || 0 }}</span>
          results
        </p>
      </div>
      
      <div>
        <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <button 
            @click="$emit('page-changed', currentPage - 1)"
            :disabled="currentPage === 1"
            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50"
            :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
          >
            <span class="sr-only">Previous</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
          </button>
          
          <!-- Page numbers -->
          <button 
            v-for="page in pages" 
            :key="page"
            @click="$emit('page-changed', page)"
            class="relative inline-flex items-center px-4 py-2 text-sm font-medium border"
            :class="{
              'z-10 bg-blue-50 border-blue-500 text-blue-600': page === currentPage,
              'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': page !== currentPage
            }"
          >
            {{ page }}
          </button>
          
          <button 
            @click="$emit('page-changed', currentPage + 1)"
            :disabled="currentPage === lastPage"
            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50"
            :class="{ 'opacity-50 cursor-not-allowed': currentPage === lastPage }"
          >
            <span class="sr-only">Next</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'Pagination',
  props: {
    currentPage: {
      type: Number,
      default: 1
    },
    lastPage: {
      type: Number,
      default: 1
    },
    total: {
      type: Number,
      default: 0
    },
    perPage: {
      type: Number,
      default: 10
    }
  },
  setup(props) {
    const meta = computed(() => {
      const from = (props.currentPage - 1) * props.perPage + 1
      const to = Math.min(props.currentPage * props.perPage, props.total)
      return { from, to, total: props.total }
    })

    const pages = computed(() => {
      const pages = []
      const maxVisiblePages = 5
      let startPage = Math.max(1, props.currentPage - Math.floor(maxVisiblePages / 2))
      let endPage = Math.min(props.lastPage, startPage + maxVisiblePages - 1)
      
      // Adjust if we're at the beginning
      if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1)
      }
      
      for (let i = startPage; i <= endPage; i++) {
        pages.push(i)
      }
      
      return pages
    })

    return {
      meta,
      pages
    }
  }
}
</script>