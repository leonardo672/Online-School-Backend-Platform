// resources/js/modules/certificate/utils.js

export function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}

export function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';

    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas ${
                type === 'success'
                    ? 'fa-check-circle'
                    : type === 'error'
                    ? 'fa-times-circle'
                    : 'fa-info-circle'
            } me-2"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}