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
                <div class="btn-group">
                  <button type="button" class="btn btn-outline-primary @if(!$statusPush) {{ '-d-none' }} @endif " id="btn-push-unsubscribe" onclick="return confirm('{{ __('app.confirm_action') }}') || event.stopImmediatePropagation()" @if(!$statusPush) {{ 'disabled' }} @endif><i class="bi bi-bell-slash"></i> {{ __('app.unsubscribe_webpush') }}</button>
                  <button type="button" class="btn btn-outline-primary @if($statusPush) {{ '-d-none' }} @endif " id="btn-push-subscribe" onclick="return confirm('{{ __('app.confirm_action') }}') || event.stopImmediatePropagation()" @if($statusPush) {{ 'disabled' }} @endif><i class="bi bi-bell"></i> {{ __('app.subscribe_webpush') }}</button>
                </div>
              </th>
            </tr>
            <tr>
              <th>{{ __('app.mail_notification') }}</th>
              <td>{{ __('app.notification_status.'.$user->status) }}</td>
            </tr>
          </tbody>
        </table>

        <a href="/{{ $lang }}/profile/edit" class="btn btn-primary btn-lg">{{ __('app.edit') }}</a>

      </div>
    </div>
  </div>

  @section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
  @endsection

  @section('scripts')
    <script src="/webpush.js"></script>
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
      </script>
  @endsection

</x-app-layout>