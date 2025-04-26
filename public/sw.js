self.addEventListener('push', function(event) {
    const data = event.data.json();
    const options = {
        body: data.body,
        icon: data.icon,
        url: data.url,
        // badge: data.badge, // Необязательно: путь к значку уведомления
        // Другие опции уведомления: https://developer.mozilla.org/en-US/docs/Web/API/Notification/Notification
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    // Перенаправление пользователя при клике на уведомление
    event.waitUntil(
        clients.openWindow(event.notification.data.url || '/')
    );
});