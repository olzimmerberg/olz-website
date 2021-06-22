self.addEventListener('install', (event) => {
    console.log('INSTALL', event);
});

self.addEventListener('activate', (event) => {
    console.log('ACTIVATE', event);
});
