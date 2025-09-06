<template>
  <component :is="componentType" 
             :to="to" 
             :href="href"
             class="quick-action-card block p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200"
             :class="cardClasses">
    <div class="flex items-center">
      <div class="flex-shrink-0">
        <div class="h-12 w-12 rounded-lg flex items-center justify-center" :class="iconBgColor">
          <span class="text-xl">{{ actionIcon }}</span>
        </div>
      </div>
      <div class="ml-4">
        <h3 class="text-lg font-medium text-gray-900" :class="titleClasses">{{ title }}</h3>
        <p class="text-sm text-gray-500 mt-1">{{ description }}</p>
      </div>
    </div>
    <div class="mt-4 flex items-center text-sm" :class="linkClasses">
      <span>{{ linkText }}</span>
      <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
      </svg>
    </div>
  </component>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'QuickActionCard',
  props: {
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      required: true
    },
    icon: {
      type: String,
      default: 'plus'
    },
    color: {
      type: String,
      default: 'blue',
      validator: (value) => ['blue', 'green', 'purple', 'yellow', 'red'].includes(value)
    },
    to: {
      type: [String, Object],
      default: null
    },
    href: {
      type: String,
      default: null
    }
  },
  setup(props) {
    const componentType = computed(() => {
      if (props.to) return 'router-link'
      if (props.href) return 'a'
      return 'div'
    })

    const actionIcon = computed(() => {
      const icons = {
        plus: '➕',
        edit: '✏️',
        eye: '👁️',
        clock: '⏰',
        users: '👥',
        stats: '📊',
        document: '📄',
        search: '🔍'
      }
      return icons[props.icon] || icons.plus
    })

    const iconBgColor = computed(() => {
      const colors = {
        blue: 'bg-blue-100',
        green: 'bg-green-100',
        purple: 'bg-purple-100',
        yellow: 'bg-yellow-100',
        red: 'bg-red-100'
      }
      return colors[props.color] || colors.blue
    })

    const cardClasses = computed(() => {
      const classes = {
        blue: 'hover:border-blue-300',
        green: 'hover:border-green-300',
        purple: 'hover:border-purple-300',
        yellow: 'hover:border-yellow-300',
        red: 'hover:border-red-300'
      }
      return classes[props.color] || classes.blue
    })

    const titleClasses = computed(() => {
      const classes = {
        blue: 'text-blue-900',
        green: 'text-green-900',
        purple: 'text-purple-900',
        yellow: 'text-yellow-900',
        red: 'text-red-900'
      }
      return classes[props.color] || classes.blue
    })

    const linkClasses = computed(() => {
      const classes = {
        blue: 'text-blue-600 hover:text-blue-800',
        green: 'text-green-600 hover:text-green-800',
        purple: 'text-purple-600 hover:text-purple-800',
        yellow: 'text-yellow-600 hover:text-yellow-800',
        red: 'text-red-600 hover:text-red-800'
      }
      return classes[props.color] || classes.blue
    })

    const linkText = computed(() => {
      if (props.to || props.href) {
        return 'Akses Sekarang'
      }
      return 'Tidak tersedia'
    })

    return {
      componentType,
      actionIcon,
      iconBgColor,
      cardClasses,
      titleClasses,
      linkClasses,
      linkText
    }
  }
}
</script>

<style scoped>
.quick-action-card {
  transition: all 0.2s ease-in-out;
}

.quick-action-card:hover {
  transform: translateY(-2px);
}
</style>