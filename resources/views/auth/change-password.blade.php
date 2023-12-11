<x-app-layout>

  <div class="row">
    <div class="col-lg-5 col-md-7 col-sm-9 mx-auto">

      @include('components.alerts')

      <form action="/{{ $lang }}/change-password" method="post" class="p-4 p-md-5 bg-light border rounded-3 bg-light">
        @csrf

        <h2 class="fw-bold mb-0">{{ __('app.changing_password') }}</h2>
        <br>

        <div class="form-floating mb-3">
          <input type="password" class="form-control rounded-3" name="password" id="password" placeholder="{{ __('app.new_password') }}" required>
          <label for="password">{{ __('app.new_password') }}</label>
        </div>
        <div class="form-floating mb-3">
          <input type="password" class="form-control rounded-3" name="password_confirmation" id="repeatPassword" placeholder="{{ __('app.re-enter_password') }}" required>
          <label for="repeatPassword">{{ __('app.re-enter_password') }}</label>
        </div>
        <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">{{ __('app.save') }}</button>
      </form>
    </div>
  </div>

</x-app-layout>