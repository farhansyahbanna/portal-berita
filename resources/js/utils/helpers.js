export const formatDate = (dateString, options = {}) => {
    const defaultOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }
    
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', { ...defaultOptions, ...options })
}

export const truncateText = (text, length = 100) => {
    if (text.length <= length) return text
    return text.substring(0, length) + '...'
}

export const stripHtml = (html) => {
    const tmp = document.createElement('DIV')
    tmp.innerHTML = html
    return tmp.textContent || tmp.innerText || ''
}

export const generateExcerpt = (content, length = 150) => {
    const plainText = stripHtml(content)
    return truncateText(plainText, length)
}

export const debounce = (func, wait) => {
    let timeout
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout)
            func(...args)
        }
        clearTimeout(timeout)
        timeout = setTimeout(later, wait)
    }
}

export const getInitials = (name) => {
    if (!name) return '??'
    return name.split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2)
}

// ... existing code ...