
export function registerServiceWorker(): void {
    console.log('registerServiceWorker...');
    if ('serviceWorker' in navigator) {
        console.log('Browser has serviceWorker...');
        navigator.serviceWorker.register('/pwa-service-worker.js', {scope: '/'})
            .then((registration) => {
                console.log('Registration successful, scope is:', registration.scope);
            })
            .catch((error) => {
                console.log('Service worker registration failed, error:', error);
            });
    }
}
