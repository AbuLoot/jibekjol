@guest
  <a href="/{{ $lang }}/login" class="btn btn-light btn-lg me-2">{{ __('app.login_btn') }}</a>
  <a href="/{{ $lang }}/register" class="btn btn-warning btn-lg">{{ __('app.register_btn') }}</a>
@else
  <div class="flex-shrink-0 dropdown ms-md-auto ps-3">
    <a href="#" class="d-block link-light text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="bi bi-person-circle fs-4 text-white"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end text-small shadow">
      <div class="text-muted px-3 py-1">{{ Auth::user()->name . ' ' . Auth::user()->lastname }}</div>
      <li><a class="dropdown-item py-2" href="/{{ $lang }}/profile"><i class="bi bi-person-circle"></i> {{ __('app.my_account') }}</a></li>
      <li><a class="dropdown-item py-2" href="/{{ $lang }}/client"><i class="bi bi-upc"></i> {{ __('app.my_tracks') }}</a></li>
      <li><a class="dropdown-item py-2" href="/{{ $lang }}/client/archive"><i class="bi bi-archive"></i> {{ __('app.my_archive') }}</a></li>
      <li><hr class="dropdown-divider"></li>
      <li>
        <form method="POST" action="/{{ $lang }}/logout">
          @csrf
          <a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('app.logout_btn') }}</a>
        </form>
      </li>
    </ul>
  </div>
@endguest