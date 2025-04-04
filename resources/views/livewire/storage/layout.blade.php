<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="ismoon">
  <title>jibekjol</title>

  <link rel="canonical" href="">

  <!-- Bootstrap core CSS -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous"> -->

  <!-- Favicons -->
  <link rel="apple-touch-icon" href="/apple-touch-icon.png" sizes="180x180">
  <link rel="icon" href="/favicon-32x32.png" sizes="32x32" type="image/png">
  <link rel="icon" href="/favicon-16x16.png" sizes="16x16" type="image/png">
  <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#7952b3">
  <link rel="icon" href="/favicon.ico">
  <meta name="theme-color" content="#7952b3">

  <!-- Custom styles for this template -->
  <link href="/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/node_modules/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/css/offcanvas-1.css" rel="stylesheet">
  <link href="/css/custom-16.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Ysabeau:ital,wght@1,1000&display=swap" rel="stylesheet">

  @livewireStyles
</head>
<body class="bg-light pt-60 pt-lg-75">
  <?php $lang = app()->getLocale(); ?>
  <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark- bg-indigo bg-indigo-border" aria-label="Main navigation">
    <div class="container-xl">
      <a href="/{{ $lang }}/storage" class="navbar-brand p-0 me-1"><!-- JibekJol -->
        <img src="/img/jj-logo-white.png">
      </a>

      <button class="navbar-toggler p-0 border-0" type="button" id="navbarSideCollapse" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="navbar-collapse offcanvas-collapse" id="navbarSideCollapse">
        <ul class="navbar-nav py-2 mx-auto">
          <!-- <li class="nav-item">
            <a class="nav-link px-3" aria-current="page" href="/"><i class="bi bi-house-fill text-white"></i></a>
          </li> -->
          <li class="nav-item">
            <a class="nav-link px-3" href="/{{ $lang }}/storage/tracks">All tracks</a>
          </li>
          @canany(['reception', 'sending'], Auth::user())
            <li class="nav-item">
              <a class="nav-link px-3" href="/{{ $lang }}/storage/reception">Reception</a>
            </li>
            <li class="nav-item">
              <a class="nav-link px-3" href="/{{ $lang }}/storage/sending">Sending</a>
            </li>
          @endcanany
            <li class="nav-item">
              <a class="nav-link px-3" href="/{{ $lang }}/storage/on-the-border">Border</a>
            </li>
            <li class="nav-item">
              <a class="nav-link px-3" href="/{{ $lang }}/storage/on-route">On route</a>
            </li>
          @canany(['sorting', 'send-locally'], Auth::user())
            <li class="nav-item">
              <a class="nav-link px-3" href="/{{ $lang }}/storage/sorting">Sorting</a>
            </li>
            <li class="nav-item">
              <a class="nav-link px-3" href="/{{ $lang }}/storage/send-locally">Send locally</a>
            </li>
          @endcanany
          @canany(['arrival', 'giving'], Auth::user())
            <li class="nav-item">
              <a class="nav-link px-3" href="/{{ $lang }}/storage/arrival">Arrival</a>
            </li>
            <li class="nav-item">
              <a class="nav-link px-3" href="/{{ $lang }}/storage/giving">Giving</a>
            </li>
          @endcanany
        </ul>

        <div class="flex-shrink-0 dropdown ms-md-auto ps-3">
          <a href="#" class="d-block link-light text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle fs-4 text-white"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end text-small shadow">
            <div class="text-muted px-3 py-1">{{ Auth::user()->name . ' ' . Auth::user()->lastname }}</div>
            <li><a class="dropdown-item py-2" href="/{{ $lang }}"><i class="bi bi-house-fill"></i> Main</a></li>
            <li><a class="dropdown-item py-2" href="/{{ $lang }}/profile"><i class="bi bi-person-circle"></i> My profile</a></li>
            <li><a class="dropdown-item py-2" href="/{{ $lang }}/client"><i class="bi bi-upc"></i> My tracks</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="/{{ app()->getLocale() }}/logout">
                @csrf
                <a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
              </form>
            </li>
          </ul>
        </div>

      </div>
    </div>
  </nav>

  <main>
    {{ $slot }}
  </main>

  @livewireScripts
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
  <script type="text/javascript">
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
  </script>
  <script src="/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
  <script src="/js/offcanvas.js"></script>
    <script type="text/javascript">
    // Toast Script
    window.addEventListener('area-focus', event => {

      var areaEl = document.getElementById('trackCodeArea');
      areaEl.value = '';
      areaEl.focus();
    })
  </script>

  @yield('scripts')
</body>
</html>