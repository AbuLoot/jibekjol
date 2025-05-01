if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js')
    .then((reg) => {
      console.log('Service Worker installed', reg);
    })
    .catch((err) => {
      console.error('Error Service Worker', err);
    });
}