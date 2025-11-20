// Global error handling for console errors
window.addEventListener('error', function(e) {
    // Suppress known browser extension errors
    if (e.message && (
        e.message.includes('runtime.lastError') ||
        e.message.includes('message channel closed') ||
        e.message.includes('listener indicated an asynchronous response')
    )) {
        e.preventDefault();
        return false;
    }
});

// Handle unhandled promise rejections
window.addEventListener('unhandledrejection', function(e) {
    // Suppress known browser extension errors
    if (e.reason && e.reason.message && (
        e.reason.message.includes('runtime.lastError') ||
        e.reason.message.includes('message channel closed') ||
        e.reason.message.includes('listener indicated an asynchronous response')
    )) {
        e.preventDefault();
        return false;
    }
});

// Livewire error handling
document.addEventListener('livewire:init', () => {
    // Handle Livewire errors gracefully
    Livewire.on('error', (error) => {
        console.warn('Livewire error handled:', error);
    });
});

// Alpine.js error handling
document.addEventListener('alpine:init', () => {
    // Global Alpine error handler
    Alpine.onError = (error) => {
        console.warn('Alpine.js error handled:', error);
        return false; // Prevent error from propagating
    };
});

// CSS preload optimization
document.addEventListener('DOMContentLoaded', function() {
    // Remove unused preload links to prevent warnings
    const preloadLinks = document.querySelectorAll('link[rel="preload"]');
    preloadLinks.forEach(link => {
        if (link.href.includes('app-') && link.href.includes('.css')) {
            // Check if CSS is actually used
            setTimeout(() => {
                if (!link.onload) {
                    link.remove();
                }
            }, 1000);
        }
    });
});

// Disable dark mode globally
document.addEventListener('DOMContentLoaded', function() {
    // Remove dark class from html element
    document.documentElement.classList.remove('dark');
    
    // Prevent dark mode from being applied
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                }
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
    
    // Override Flux UI appearance if it exists
    if (window.Alpine && window.Alpine.store) {
        Alpine.store('flux', {
            appearance: 'light'
        });
    }
});
