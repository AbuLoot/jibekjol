
function subscribeUserToPush() {

  return navigator.serviceWorker.ready
    .then(function(registration) {
      const subscribeOptions = {
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array('BK17IUI2vdE1B47M8qH4uIUePUuqRgAL44hv4jX8Hq8ogvW5NtWIV1eKZh3aGX7ca13DVnFt5ZiojCE95XCyowY')
      };
      return registration.pushManager.subscribe(subscribeOptions);
    })
    .then(function(newSubscription) {
      console.log('Received PushSubscription:', JSON.stringify(newSubscription));
      sendSubscriptionToServer(newSubscription);
      return newSubscription;
    });
}

function urlBase64ToUint8Array(base64String) {

  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
    .replace(/\-/g, '+')
    .replace(/_/g, '/');

  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

function sendSubscriptionToServer(subscription) {

  fetch('/en/push-subscribe', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify(subscription)
    // body: JSON.stringify({
    //   endpoint: subscription.endpoint,
    //   keys: {
    //     p256dh: btoa(String.fromCharCode.apply(null, new Uint8Array(newSubscription.getKey('p256dh')))),
    //     auth: btoa(String.fromCharCode.apply(null, new Uint8Array(newSubscription.getKey('auth'))))
    //   }
    // })
  })
  .then(response => response.json())
  .then(data => console.log('Subscription saved:', data))
  .catch(error => console.error('Error saving subscription', error));
}

function sendUnsubscriptionToServer(subscription) {

  fetch('/en/push-unsubscribe', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
      endpoint: subscription.endpoint
    })
  })
  .then(response => response.json())
  .catch(error => console.error('Error unsubscription', error));
}

if ('serviceWorker' in navigator) {

  navigator.serviceWorker.register('/sw.js').then(function(registration) {

    console.log('Service Worker registered with scope:', registration.scope);

    if (Notification.permission === 'granted') {
      console.log('Notification permission already granted.');
      subscribeUserToPush();
    }
    else if (Notification.permission !== 'denied') {
      // Запросите разрешение у пользователя при определенном действии (например, клик по кнопке)
      // или при загрузке страницы, если это приемлемо для вашего UX.
      Notification.requestPermission().then(function(permission) {
        if (permission === 'granted') {
          console.log('Notification permission granted.');
          subscribeUserToPush();
        }
        else {
          console.log('Notification permission denied.');
        }
      });
    }
  })
  .catch(function(error) {
    console.error('Service Worker registration failed:', error);
  });
}