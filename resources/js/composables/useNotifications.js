import { ref } from 'vue';

export function useNotifications() {
    const notifications = ref([]);

    const showNotification = (message, type = 'info', duration = 5000) => {
        const id = Date.now().toString();
        const notification = {
            id,
            message,
            type,
            duration
        };

        notifications.value.push(notification);

        // Auto remove notification after duration
        if (duration > 0) {
            setTimeout(() => {
                removeNotification(id);
            }, duration);
        }

        return id;
    };

    const removeNotification = (id) => {
        notifications.value = notifications.value.filter(notification => notification.id !== id);
    };

    const clearAllNotifications = () => {
        notifications.value = [];
    };

    return {
        notifications,
        showNotification,
        removeNotification,
        clearAllNotifications
    };
}