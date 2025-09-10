<template>
  <div class="stat-card bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
    <div class="flex items-center">
      <div class="flex-shrink-0">
        <div class="h-12 w-12 rounded-lg flex items-center justify-center" :class="iconBgColor">
          <span class="text-xl">{{ icon }}</span>
        </div>
      </div>
      <div class="ml-4">
        <dt class="text-sm font-medium text-gray-500 truncate">{{ title }}</dt>
        <dd class="text-2xl font-semibold text-gray-900">
          <template v-if="loading">...</template>
          <template v-else>{{ value }}</template>
        </dd>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'StatCard',
  props: {
    title: {
      type: String,
      required: true
    },
    value: {
      type: [Number, String],
      default: 0
    },
    icon: {
      type: String,
      default: '📊'
    },
    color: {
      type: String,
      default: 'blue',
      validator: (value) => ['blue', 'green', 'yellow', 'purple', 'red'].includes(value)
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {
    const iconBgColor = computed(() => {
      const colors = {
        blue: 'bg-blue-100',
        green: 'bg-green-100',
        yellow: 'bg-yellow-100',
        purple: 'bg-purple-100',
        red: 'bg-red-100'
      }
      return colors[props.color] || colors.blue
    })

    return {
      iconBgColor
    }
  }
}
</script>

<style scoped>
.stat-card {
  transition: transform 0.2s ease-in-out;
}

.stat-card:hover {
  transform: translateY(-2px);
}
</style>