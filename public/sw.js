self.addEventListener('push', event => {

  const data = event.data.json();
  const title = data.notification.title || 'Notification';
  const options = {
    body: data.notification.body || '',
    icon: data.notification.icon || '/icons/favicon-96x96.png',
    url: data.notification.url || '/en/client',
  };

  event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', event => {

  event.notification.close();
  if (event.action && clients.openWindow) {
    event.waitUntil(clients.openWindow(event.action));
  } else if (event.notification.click_action && clients.openWindow) {
    event.waitUntil(clients.openWindow(event.notification.click_action));
  }
});
