@extends('layout')

@section('meta_title', $page->meta_title ?? $page->title)

@section('meta_description', $page->meta_description ?? $page->title)

@section('head')

@endsection

@section('content')

  <div class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="/img/storage/storage-01.jpg" class="d-block w-lg-100 h-100 " alt="...">
        <div class="carousel-caption d-none-d-md-block">
          <div class="display-3 shadow-1 fw-bold">{!! $promo->firstWhere('slug', 'offer')->content !!}</div>
          <hr>
          <h2 class="d-none-d-md-block fw-normal shadow-1">{{ __('app.tracking_by_code') }}</h2>
          <form action="/{{ $lang }}/search-track" method="get" class="col-12 col-lg-8 offset-lg-2 mt-lg-0 mb-3 mb-lg-0 me-lg-2 py-2" role="search">
            <input type="search" name="code" class="form-control form-control-dark form-control-lg -text-bg-dark" placeholder="{{ __('app.enter_track_code') }}" aria-label="Search" min="4" required>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Interesting -->
  <div class="container px-4 py-5 my-3 text-center border-bottom">
    <div class="col-lg-6 mx-auto mb-5">
      <h2 class=" fw-bold text-body-emphasis">{{ $promo->firstWhere('slug', 'second-offer')->content }}</h2>
    </div>
    <div class="row">
      <div class="col-lg-7 overflow-hidden">
        <div class="container px-3">
          <img src="/img/transportation.jpg" class="img-fluid border rounded-3 shadow-lg mb-4" alt="Example image" width="700" height="" loading="lazy">
        </div>
      </div>
      <div class="col-lg-5">
        <form method="POST" action="/{{ $lang }}/calculate" id="calc" class="col-lg-6-mx-auto p-4 p-md-5 border rounded-3 bg-body-tertiary">
          @csrf
          <h3 class="mb-3">{{ __('app.price_calculator') }}</h3>
          <div class="row">
            <div class="col-lg-3 col-6 mb-3">
              <label for="elLength" class="form-label">{{ __('app.length') }}</label>
              <input type="number" class="form-control" id="elLength" name="length" min="0" max="100" placeholder="0,0" value="{{ session('length') }}" step="any" required>
            </div>
            <div class="col-lg-3 col-6 mb-3">
              <label for="width" class="form-label">{{ __('app.width') }}</label>
              <input type="number" class="form-control" id="width" name="width" min="0" max="100" placeholder="0,0" value="{{ session('width') }}" step="any" required>
            </div>
            <div class="col-lg-3 col-6 mb-3">
              <label for="height" class="form-label">{{ __('app.height') }}</label>
              <input type="number" class="form-control" id="height" name="height" min="0" max="100" placeholder="0,0" value="{{ session('height') }}" step="any" required>
            </div>
            <div class="col-lg-3 col-6 mb-3">
              <label for="weight" class="form-label">{{ __('app.weight') }}</label>
              <input type="number" class="form-control" id="weight" name="weight" min="0" placeholder="0,0" value="{{ session('weight') }}" step="any" required>
            </div>
            <div class="col-lg-12 mb-3">
              <label class="form-label">{{ __('app.delivery_method') }}</label>
              <div class="list-group">
                <label class="list-group-item d-flex gap-2">
                  <input class="form-check-input flex-shrink-0" type="radio" name="type_delivery" id="standart" value="1" checked>
                  <span>{{ __('app.standard_days') }}</span>
                </label>
                <label class="list-group-item d-flex gap-2">
                  <input class="form-check-input flex-shrink-0" type="radio" name="type_delivery" id="express" value="2">
                  <span>{{ __('app.express_days') }}</span>
                </label>
                <label class="list-group-item d-flex gap-2">
                  <input class="form-check-input flex-shrink-0" type="radio" name="type_delivery" id="express-clothes" value="3">
                  <span>{{ __('app.express_days_clothes') }}</span>
                </label>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary">{{ __('app.count') }}</button>

          @if(session('price'))
            <?php

              $typesDelivery = [
                '1' => __('app.standard_days'),
                '2' => __('app.express_days'),
                '3' => __('app.express_days_clothes'),
              ];
            ?>
            <div id="text-hint">
              <hr>
              <div class="h3">{{ __('app.bulk_density') }}: <span id="density">{{ session('density') }}</span></div>
              <div class="h5">{{ __('app.delivery') }}: <span id="density">{{ $typesDelivery[session('typeDelivery')] }}</span></div>
              <div class="h3">{{ __('app.price') }}: $<span class="price">{{ session('price') }}</span></div>
              <div class="display-5">{{ __('app.total') }}: <span class="text-success fw-bold">${{ session('weight') * session('price') }}</span></div>
            </div>
          @endif
        </form>
      </div>
    </div>
  </div>

  <!-- Desire -->
  <div class="container col-xxl-8 px-4 py-5">
    <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
        <h3 class="display-5 fw-bold text-body-emphasis lh-1 mb-3 text-center">{{ $promo->firstWhere('slug', 'third-offer')->title }}</h3>
      <div class="col-10 col-sm-8 col-lg-6">
        <img src="/img/quarantee.jpg" class="d-block rounded-3 mx-lg-auto img-fluid" alt="JJ quarantee" width="700" height="500" loading="lazy">
      </div>
      <div class="col-lg-6">
        {!! $promo->firstWhere('slug', 'third-offer')->content !!}
      </div>
    </div>
  </div>

  <!-- Advantages -->
  <div class="container px-4 py-5" id="featured-3">
    <hr>
    <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
      {!! $promo->firstWhere('slug', 'fourth-offer')->content !!}
    </div>
  </div>

  <!-- Action -->
  <div class="container col-xl-10 col-xxl-8 px-4 py-5">
    <div class="row align-items-center g-lg-5 py-5">
      <div class="col-lg-7 text-center text-lg-start">
        <h3 class="display-5 fw-bold lh-1 text-body-emphasis mb-3">{{ $promo->firstWhere('slug', 'fifth-offer')->title }}</h3>
        {!! $promo->firstWhere('slug', 'fifth-offer')->content !!}
      </div>
      <div class="col-md-10 mx-auto col-lg-5">
        <form method="POST" action="/{{ $lang }}/send-app" id="app-form" class="p-4 p-md-5 border rounded-3 bg-body-tertiary">
          @csrf
          @include('components.alerts')
          <h3 class="mb-3">{{ __('app.app_form') }}</h3>
          <div class="form-floating mb-3">
            <input type="text" name="name" class="form-control" id="form-name" minlength="2" maxlength="40" autocomplete="off" placeholder="{{ __('app.name') }}" required>
            <label for="form-name">{{ __('app.name') }}</label>
          </div>
          <div class="form-floating mb-3">
            <input type="email" name="email" class="form-control" id="form-email" autocomplete="off" placeholder="{{ __('app.email') }}" required>
            <label for="form-email">{{ __('app.email') }}</label>
          </div>
          <div class="form-floating mb-3">
            <input type="tel" id="form-number" class="form-control" pattern="(\+?\d[- .]*){7,13}" name="phone" minlength="5" maxlength="20" placeholder="{{ __('app.phone') }}" required>
            <label for="form-number">{{ __('app.phone') }}</label>
          </div>
          <div class="form-floating mb-3">
            <textarea class="form-control" name="message" placeholder="Leave a comment here" id="message"></textarea>
            <label for="message">{{ __('app.text') }}</label>
          </div>
          <button class="w-100 btn btn-lg btn-primary" type="submit">{{ __('app.send') }}</button>
        </form>
      </div>
    </div>
  </div>

  <!-- News -->
  @if($posts->isNotEmpty())
    <div class="container my-3 my-lg-5">
      <div class="row gx-2 gy-2">
        @foreach($posts as $post)
          <div class="col">
            <div class="card shadow-sm">
              @if($post->image)
                <img src="/img/posts/{{ $post->image }}" class="card-img-top" alt="{{ $post->title }}">
              @endif

              <div class="card-body">
                <h5 class="card-title">{{ $post->title }}</h5>
                <p class="card-text">{!! Str::limit($post->content, 50) !!}</p>
                <a href="/i/news/{{ $post->slug }}" class="btn btn-link">More</a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <!-- FAQ -->
  <div class="container px-4 py-5">
    <h3 class="display-5 fw-bold text-body-emphasis text-center lh-1 mb-3">{{ $promo->firstWhere('slug', 'faq')->title }}</h3>
    <div class="accordion col-lg-6 mx-auto" id="accordionExample">
      <?php $answers = unserialize($promo->firstWhere('slug', 'faq')->data) ?? []; ?>
      @foreach($answers as $key => $answer)
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $key }}" aria-expanded="true" aria-controls="collapse{{ $key }}">{{ $answer['key'] }}</button>
          </h2>
          <div id="collapse{{ $key }}" class="accordion-collapse collapse @if($key == 0) show @endif" data-bs-parent="#accordionExample">
            <div class="accordion-body">{!! $answer['value'] !!}</div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

@endsection

@section('scripts')
  @if (session('price'))
    <script>
      document.getElementById("calc").scrollIntoView({behavior: 'instant'});
    </script>
  @endif
  @if (count($errors) > 0 || session('status'))
    <script>
      document.getElementById("app-form").scrollIntoView({behavior: 'instant'});
    </script>
  @endif

  <script>
    function calculate() {
      const form = document.getElementById('calc')

      let token = form.elements['_token'].value
      let lengthEl = form.elements['elLength'].value
      let width = form.elements['width'].value
      let height = form.elements['height'].value
      let weight = form.elements['weight'].value
      let typeDelivery = form.elements['type_delivery'].value

      let domain = '{{ url("/".$lang) }}/calculate'
      let uri = '?_token='+token+'&length='+lengthEl+'&width='+width+'&height='+height+'&weight='+weight+'&type_delivery='+typeDelivery;

      // Ajax Request
      const xmlHttp = new XMLHttpRequest();

      xmlHttp.open('GET', domain+uri, true)
      xmlHttp.send()
      xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          var divHint = document.getElementById('text-hint')

          // document.getElementById('density').innerHTML = 
          // document.getElementById('price').innerHTML = 

          divHint.classList.remove("d-none");
          // density.innerHTML = this.responseText.density
          // price.innerHTML = this.responseText.price

          console.log(this.responseText);
        }
      }

      console.log(domain+uri, lengthEl, width, height, weight, typeDelivery)
    }
  </script>
@endsection