// resources/js/app.js или отдельный скрипт

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/service-worker.js')
    .then(function(registration) {
      console.log('Service Worker зарегистрирован с областью видимости:', registration.scope);
    }).catch(function(error) {
      console.log('Ошибка регистрации Service Worker:', error);
    });
}

// Функция для запроса разрешения и подписки
function subscribeUser() {
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.ready.then(function(registration) {
      const applicationServerKey = urlBase64ToUint8Array("{{ config('webpush.vapid.public_key') }}"); // Получите публичный VAPID ключ из конфигурации Laravel

      const options = {
        userVisibleOnly: true,
        applicationServerKey: applicationServerKey
      };

      registration.pushManager.subscribe(options)
        .then(function(subscription) {
          console.log('Пользователь подписан:', subscription);

          // Отправьте данные подписки на ваш backend
          sendSubscriptionToBackend(subscription);

        })
        .catch(function(error) {
          console.error('Ошибка подписки:', error);
        });
    });
  }
}

// Функция для отписки пользователя
function unsubscribeUser() {
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.ready.then(function(registration) {
      registration.pushManager.getSubscription().then(function(subscription) {
        if (subscription) {
          subscription.unsubscribe().then(function() {
            console.log('Пользователь отписан.');

            // Отправьте запрос на удаление подписки на ваш backend
            deleteSubscriptionFromBackend(subscription);

          }).catch(function(error) {
            console.error('Ошибка отписки:', error);
          });
        }
      });
    });
  }
}

// Вспомогательная функция для преобразования VAPID ключа
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


// Функции для отправки/удаления подписки на backend (используйте Ajax, Fetch API или Axios)
function sendSubscriptionToBackend(subscription) {
  // Пример с использованием Fetch API
  fetch('/subscriptions', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Не забудьте добавить CSRF токен
    },
    body: JSON.stringify(subscription)
  })
  .then(response => response.json())
  .then(data => {
    console.log('Подписка успешно сохранена на backend:', data);
  })
  .catch(error => {
    console.error('Ошибка при сохранении подписки на backend:', error);
  });
}

function deleteSubscriptionFromBackend(subscription) {
   // Пример с использованием Fetch API
   fetch('/subscriptions/delete', {
    method: 'POST', // Или DELETE, в зависимости от вашего роута
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Не забудьте добавить CSRF токен
    },
    body: JSON.stringify({ endpoint: subscription.endpoint })
  })
  .then(response => response.json())
  .then(data => {
    console.log('Подписка успешно удалена на backend:', data);
  })
  .catch(error => {
    console.error('Ошибка при удалении подписки на backend:', error);
  });
}


// Пример использования: Кнопки для подписки/отписки
document.getElementById('subscribe-button').addEventListener('click', subscribeUser);
document.getElementById('unsubscribe-button').addEventListener('click', unsubscribeUser);