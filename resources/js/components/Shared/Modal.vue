<template>
  <transition name="modal-fade">
    <!-- Overlay -->
    <div v-if="modelValue" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeOnOverlayClick ? close() : null"></div>
      
      <!-- Modal container -->
      <div class="relative z-50 transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all"
           :class="maxWidthClass">
        
        <!-- Modal content -->
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <!-- Icon (jika ada) -->
            <div v-if="icon" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" 
                 :class="iconBgColor">
              <component :is="icon" class="h-6 w-6" :class="iconColor" />
            </div>
            
            <!-- Content -->
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <!-- Header -->
              <div class="flex items-center justify-between">
                <h3 v-if="title" class="text-lg font-medium text-gray-900">
                  {{ title }}
                </h3>
                <button
                  v-if="showCloseButton"
                  @click="close"
                  class="text-gray-400 hover:text-gray-600 transition-colors ml-4"
                >
                  <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
              
              <!-- Description -->
              <div class="mt-2">
                <p v-if="description" class="text-sm text-gray-500">
                  {{ description }}
                </p>
                <slot name="content"></slot>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import { computed, watch, onMounted, onUnmounted } from 'vue'

export default {
  name: 'Modal',
  props: {
    modelValue: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: ''
    },
    description: {
      type: String,
      default: ''
    },
    icon: {
      type: [String, Object],
      default: null
    },
    iconBgColor: {
      type: String,
      default: 'bg-blue-100'
    },
    iconColor: {
      type: String,
      default: 'text-blue-600'
    },
    maxWidth: {
      type: String,
      default: 'lg',
      validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'].includes(value)
    },
    closeOnOverlayClick: {
      type: Boolean,
      default: true
    },
    showCloseButton: {
      type: Boolean,
      default: true
    },
    persistent: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:modelValue', 'close', 'confirm'],
  setup(props, { emit, slots }) {
    const maxWidthClass = computed(() => {
      const classes = {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '3xl': 'sm:max-w-3xl',
        '4xl': 'sm:max-w-4xl',
        '5xl': 'sm:max-w-5xl',
        '6xl': 'sm:max-w-6xl',
        '7xl': 'sm:max-w-7xl'
      }
      return classes[props.maxWidth] || classes.lg
    })

    const hasActions = computed(() => {
      return slots.actions || !props.persistent
    })

    const close = () => {
      if (!props.persistent) {
        emit('update:modelValue', false)
        emit('close')
      }
    }

    const confirm = () => {
      emit('confirm')
      if (!props.persistent) {
        emit('update:modelValue', false)
      }
    }

    const handleEscape = (event) => {
      if (event.keyCode === 27 && !props.persistent && props.modelValue) {
        close()
      }
    }

    watch(() => props.modelValue, (value) => {
      if (value) {
        document.addEventListener('keydown', handleEscape)
        document.body.style.overflow = 'hidden'
        document.body.style.paddingRight = '0px'
      } else {
        document.removeEventListener('keydown', handleEscape)
        document.body.style.overflow = 'auto'
        document.body.style.paddingRight = '0px'
      }
    })

    onMounted(() => {
      if (props.modelValue) {
        document.addEventListener('keydown', handleEscape)
        document.body.style.overflow = 'hidden'
      }
    })

    onUnmounted(() => {
      document.removeEventListener('keydown', handleEscape)
      document.body.style.overflow = 'auto'
      document.body.style.paddingRight = '0px'
    })

    return {
      maxWidthClass,
      hasActions,
      close,
      confirm
    }
  }
}
</script>

<style scoped>
.modal-overlay {
  z-index: 9999;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

/* Animation for modal content */
.modal-content-enter-active {
  transition: all 0.3s ease-out;
}

.modal-content-leave-active {
  transition: all 0.2s ease-in;
}

.modal-content-enter-from,
.modal-content-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-20px);
}
</style>