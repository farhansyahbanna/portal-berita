import { defineStore } from 'pinia'

export const useUIStore = defineStore('ui', {
    state: () => ({
        sidebarOpen: false,
        theme: 'light',
        notifications: []
    }),

    actions: {
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen
        },
        
        addNotification(notification) {
            this.notifications.push(notification)
        },
        
        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id)
        }
    }
})