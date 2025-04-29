<x-app-layout>

  <div class="row">
    <div class="col-lg-5 col-md-7 col-sm-9 mx-auto">

      @include('components.alerts')

      <div class="p-4 p-md-5 bg-light border rounded-3 bg-light">
        <h2 class="fw-bold mb-0">{{ __('app.my_profile') }}</h2>
        <br>

        <h5>{{ $user->name.' '.$user->lastname }}</h5>
        <p>{{ $user->email }}</p>
        <p></p>

        <table class="table">
          <tbody>
            <tr>
              <th>Tel</th>
              <td>{{ $user->tel }}</td>
            </tr>
            <tr>
              <th scope="col">{{ __('app.region') }}</th>
              <td scope="col">{{ $user->region->title }}</td>
            </tr>
            <tr>
              <th>{{ __('app.address') }}</th>
              <td>{{ $user->address }}</td>
            </tr>
            <tr>
              <th>ID client</th>
              <td>{{ $user->id_client }}</td>
            </tr>
            <tr>
              <th>{{ __('app.language') }}</th>
              <td>{{ $language->title }}</td>
            </tr>
            <tr>
              <th colspan="2">{{ __('app.webpush_notification') }}

                <?php $statusPush = \App\Models\PushSubscription::where('subscribable_id', auth()->user()->id)->first(); ?>
                <button type="button" class="btn btn-outline-primary @if(!$statusPush) {{ 'd-none' }} @endif " id="btn-push-unsubscribe" onclick="return confirm('{{ __('app.confirm_action') }}') || event.stopImmediatePropagation()"><i class="bi bi-bell-slash"></i> {{ __('app.unsubscribe_webpush') }}</button>
                <button type="button" class="btn btn-outline-primary @if($statusPush) {{ 'd-none' }} @endif " id="btn-push-subscribe" onclick="return confirm('{{ __('app.confirm_action') }}') || event.stopImmediatePropagation()"><i class="bi bi-bell"></i> {{ __('app.subscribe_webpush') }}</button>

              </th>
              <!-- <td>
                @if(\App\Models\PushSubscription::where('subscribable_id', auth()->user()->id)->first())
                {{ __('app.notification_status.1') }}
                @else
                {{ __('app.notification_status.2') }}
                @endif
              </td> -->
            </tr>
            <tr>
              <th>{{ __('app.mail_notification') }}</th>
              <td>{{ __('app.notification_status.'.$user->status) }}</td>
            </tr>
          </tbody>
        </table>

        <a href="/{{ $lang }}/profile/edit" class="btn btn-primary btn-lg">{{ __('app.edit') }}</a>

      </div>
      <script type="text/javascript">
        const btnSub = document.getElementById('btn-push-subscribe');
        const btnUnsub = document.getElementById('btn-push-unsubscribe');

        btnSub.addEventListener('click', function(res) {
          subscribeUserToPush();
          btnSub.disabled = true;
          btnUnsub.disabled = false;
        });

        btnUnsub.addEventListener('click', function(res) {
          unsubscribeUserFromPush();
          btnSub.disabled = false;
          btnUnsub.disabled = true;
        });

        // const registration = await navigator.serviceWorker.ready;

        // // Функция для обновления состояния кнопок
        // const updateButtonState = async () => {
        //   if (!enablePushBtn || !disablePushBtn) return;

        //   // Проверяем текущую подписку
        //   const subscription = await registration.pushManager.getSubscription();

        //   if (subscription) {
        //     // Пользователь подписан
        //     enablePushBtn.disabled = true;
        //     disablePushBtn.disabled = false;
        //   } else {
        //     // Пользователь не подписан
        //     enablePushBtn.disabled = false;
        //     disablePushBtn.disabled = true;
        //   }
        // };
      </script>
    </div>
  </div>

  @section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="/webpush.js"></script>
    <script type="text/javascript">
      
document.addEventListener('DOMContentLoaded', async () => {
  const enablePushBtn = document.getElementById('enable-push');
  const disablePushBtn = document.getElementById('disable-push');

  // Проверка поддержки Service Worker и Push API
  if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
    console.warn('Push notifications are not supported by this browser.');
    // Опционально: скрыть кнопки или показать сообщение об ошибке
    if (enablePushBtn) enablePushBtn.style.display = 'none';
    if (disablePushBtn) disablePushBtn.style.display = 'none';
    return;
  }

  // Ждем регистрации Service Worker
  const registration = await navigator.serviceWorker.ready;

  // Функция для обновления состояния кнопок
  const updateButtonState = async () => {
    if (!enablePushBtn || !disablePushBtn) return;

    // Проверяем текущую подписку
    const subscription = await registration.pushManager.getSubscription();

    if (subscription) {
      // Пользователь подписан
      enablePushBtn.disabled = true;
      disablePushBtn.disabled = false;
    } else {
      // Пользователь не подписан
      enablePushBtn.disabled = false;
      disablePushBtn.disabled = true;
    }
  };

  // Обработчик кнопки "Включить уведомления"
  if (enablePushBtn) {
    enablePushBtn.addEventListener('click', async () => {
      enablePushBtn.disabled = true;
      disablePushBtn.disabled = true;

      try {
        // Запрашиваем разрешение и подписываем пользователя
        const permission = await Notification.requestPermission();

        if (permission === 'granted') {

          subscribeUserToPush();

          console.log('Push subscribed and sent to server.');

        } else {
          console.warn('Permission for push notifications was denied.');
          alert('Не удалось включить уведомления. Разрешите их в настройках браузера.'); // Уведомление пользователю
        }
      } catch (error) {
        console.error('Failed to subscribe the user: ', error);
        alert('Произошла ошибка при подписке на уведомления.'); // Уведомление пользователю
      } finally {
        // Обновляем состояние кнопок после попытки
        updateButtonState();
      }
    });
  }


  // Обработчик кнопки "Выключить уведомления"
  if (disablePushBtn) {
    disablePushBtn.addEventListener('click', async () => {
      enablePushBtn.disabled = true;
      disablePushBtn.disabled = true;

      try {
        // Получаем текущую подписку
        const subscription = await registration.pushManager.getSubscription();

        if (subscription) {

          unsubscribeUserFromPush();

          console.log('Push unsubscribed and removed from server.');

        } else {
          console.log('User was not subscribed.');
        }

      } catch (error) {
        console.error('Failed to unsubscribe the user: ', error);
         alert('Произошла ошибка при отписке от уведомлений.'); // Уведомление пользователю
      } finally {
        // Обновляем состояние кнопок после попытки
        updateButtonState();
      }
    });
  }

  // Инициализация: обновляем состояние кнопок при загрузке страницы
  updateButtonState();
});

    </script>
  @endsection

</x-app-layout>