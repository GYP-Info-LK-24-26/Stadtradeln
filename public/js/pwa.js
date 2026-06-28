// Registriert den Service Worker für die PWA-Funktionalität.
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js').catch(function (err) {
            console.error('Service-Worker-Registrierung fehlgeschlagen:', err);
        });
    });
}
