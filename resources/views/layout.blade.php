<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Namatilla">
  <title>@yield('meta_title', 'JibekJol')</title>
  <meta name="description" content="@yield('meta_description', 'JibekJol')">

  <!-- Bootstrap core CSS -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous"> -->

  <!-- Favicons -->
  <link rel="apple-touch-icon" href="apple-touch-icon.png" sizes="180x180">
  <link rel="icon" href="favicon-32x32.png" sizes="32x32" type="image/png">
  <link rel="icon" href="favicon-16x16.png" sizes="16x16" type="image/png">
  <link rel="mask-icon" href="safari-pinned-tab.svg" color="#7952b3">
  <link rel="icon" href="favicon.ico">
  <meta name="theme-color" content="#7952b3">

  <!-- Custom styles for this template -->
  <link href="/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/node_modules/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/css/offcanvas.css" rel="stylesheet">
  <link href="/css/custom-15.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Ysabeau:ital,wght@1,1000&display=swap" rel="stylesheet">

  @yield('head')

  @if($sections->firstWhere('slug', 'header-code'))
    {{ $sections->firstWhere('slug', 'header-code')->content }}
  @endif
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-brand-bg-brand-border bg-indigo bg-indigo-border" aria-label="Main navigation">
    <div class="container-xl">
      <a href="/{{ $lang }}/" class="navbar-brand text-white">
        JibekJol
      </a>
      <div class="dropdown me-auto">
        <button class="btn btn-outline-light dropdown-toggle text-uppercase" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          {{ $lang }}
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="/kz">Kazakh</a></li>
          <li><a class="dropdown-item" href="/ru">Russian</a></li>
          <li><a class="dropdown-item" href="/en">English</a></li>
        </ul>
      </div>

      <button class="navbar-toggler p-0 border-0" type="button" id="navbarSideCollapse" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav py-2 mx-auto-">
          <li class="nav-item">
            <a class="nav-link px-3" aria-current="page" href="/{{ $lang }}/"><i class="bi bi-house-fill text-white"></i></a>
          </li>
          @foreach($pages as $page)
            <li class="nav-item">
              <a class="nav-link px-3" aria-current="page" href="/{{ $lang }}/i/{{ $page->slug }}">{{ $page->title }}</a>
            </li>
          @endforeach
          @auth
            <li class="nav-item">
              <a class="nav-link px-3" aria-current="page" href="/{{ $lang }}/client">{{ __('app.my_tracks') }}</a>
            </li>
          @endauth
        </ul>
        <div class="ms-auto">
          @include('components.auth-dropdown')
        </div>
      </div>
    </div>
  </nav>

  <!-- Content -->
  <main>
    @yield('content')
  </main>

  <?php
    $contactsSection = $sections->firstWhere('slug', 'contacts');
    $contacts = unserialize($contactsSection->data);
  ?>

  <!-- Widget contact buttons -->
  <div class="d-block d-lg-none material-button-anim" id="widget-contacts">
    <ul class="list-inline" id="options">
      <li class="option">
        <button class="material-button option3 bg-whatsapp" type="button">
          <a href="whatsapp://send?phone={{ $contacts[1]['value'] }}" target="_blank">
            <span class="bi bi-whatsapp display-3"></span>
          </a>
        </button>
      </li>
      <li class="option">
        <button class="material-button option4 bg-ripple" type="button">
          <a href="tel:{{ $contacts[1]['value'] }}" target="_blank">
            <span class="bi bi-telephone display-3"></span>
          </a>
        </button>
      </li>
    </ul>
    <button class="material-button material-button-toggle btnBg" type="button">
      <span class="bi bi-person-circle display-3"></span>
      <span class="ripple btnBg"></span>
      <span class="ripple btnBg"></span>
      <span class="ripple btnBg"></span>
    </button>
  </div>

  <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
    <div class="container">
      <div class="row">
        
        <div class="col-md-4 d-flex align-items-center">
          <a href="/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1">
            <svg class="bi" width="30" height="24"><use xlink:href="#bootstrap"></use></svg>
          </a>
          <span class="mb-3 mb-md-0 text-muted">© {{ date('Y') }} JibekJol</span>
        </div>
        <div class="col-md-4">
          <span>广东新世之路国际物流有限公司</span>
        </div>
      </div>

      @if($sections->firstWhere('slug', 'soc-networks'))
        {!! $sections->firstWhere('slug', 'soc-networks')->content !!}
      @endif
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
  <script type="text/javascript">
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    // MATERIAL BUTTON TOGGLE
    const widgetContacts = document.getElementById('widget-contacts')
    const optionsContacts = document.querySelectorAll('.option')

    widgetContacts.addEventListener('click', () => {
      widgetContacts.classList.toggle('open')

      for (let i = 0; i < optionsContacts.length; i++) {
        optionsContacts[i].classList.toggle('scale-on');
      }
    })
  </script>
  <script src="/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
  <script src="/js/offcanvas.js"></script>

  @yield('scripts')

  @if($sections->firstWhere('slug', 'footer-code'))
    {{ $sections->firstWhere('slug', 'footer-code')->content }}
  @endif
</body>
</html>