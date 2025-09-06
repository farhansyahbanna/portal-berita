<template>
    <div class="bg-white rounded-lg shadow-md p-6" :class="cardBorderColor">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <span class="text-2xl">{{ icon }}</span>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        {{ title }}
                    </dt>
                    <dd class="text-3xl font-semibold text-gray-900">
                        {{ value }}
                    </dd>
                </dl>
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
        }
    },
    setup(props) {
        const cardBorderColor = computed(() => {
            const colors = {
                blue: 'border-l-4 border-blue-400',
                green: 'border-l-4 border-green-400',
                yellow: 'border-l-4 border-yellow-400',
                purple: 'border-l-4 border-purple-400',
                red: 'border-l-4 border-red-400'
            }
            return colors[props.color] || colors.blue
        })

        return {
            cardBorderColor
        }
    }
}
</script>