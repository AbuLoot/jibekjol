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
              <th>{{ __('app.notification') }}</th>
              <td>{{ __('app.notification_status.'.$user->status) }}</td>
            </tr>
          </tbody>
        </table>


        <button type="button" class="btn btn-info" id="enable-push">Enable Notification</button>

        <script>
          const btnEnablePush = document.getElementById('enable-push');

          btnEnablePush.addEventListener('click', function() {
            if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
              Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                  console.log('Notification permission granted.');
                  subscribeUserToPush();
                } else {
                  console.log('Notification permission denied.');
                }
              });
            } else if (Notification.permission === 'granted') {
               subscribeUserToPush();
            }
          });
        </script>


        <a href="/{{ $lang }}/profile/edit" class="btn btn-primary btn-lg">{{ __('app.edit') }}</a>

      </div>
    </div>
  </div>

</x-app-layout>