@extends('layout')

@section('meta_title', $page->meta_title ?? $page->title)

@section('meta_description', $page->meta_description ?? $page->title)

@section('head')

@endsection

@section('content')

  <div class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="/img/storage/storage-01.jpg" class="d-block w-100-h-100 " alt="...">
        <div class="carousel-caption d-none-d-md-block">
          <div class="display-3 shadow-1 fw-bold">Вы работали с&nbsp;простой и&nbsp;быстрой логистикой от&nbsp;<span class="navbar-brand" style="color: #6610f2;">JibekJol</span>?</div>
          <hr>
          <h2 class="d-none-d-md-block fw-normal shadow-1">Отслеживание по трек коду</h2>
          <form action="/search-track" method="get" class="col-12 col-lg-8 offset-lg-2 mt-lg-0 mb-3 mb-lg-0 me-lg-2 py-2" role="search">
            <input type="search" name="code" class="form-control form-control-dark form-control-lg -text-bg-dark" placeholder="Введите трек код..." aria-label="Search" min="4" required>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Calc -->
  <div class="container mt-3">
    <form class="col-lg-6 mx-auto p-4 p-md-5 border rounded-3 bg-body-tertiary">
      <h3 class="mb-3">Калькулятор цены</h3>
      <div class="row">
        <div class="col-lg-3 col-6 mb-3">
          <label for="length" class="form-label">Длина</label>
          <input type="number" class="form-control" id="length" placeholder="0,0">
        </div>
        <div class="col-lg-3 col-6 mb-3">
          <label for="height" class="form-label">Высота</label>
          <input type="number" class="form-control" id="height" placeholder="0,0">
        </div>
        <div class="col-lg-3 col-6 mb-3">
          <label for="width" class="form-label">Ширина</label>
          <input type="number" class="form-control" id="width" placeholder="0,0">
        </div>
        <div class="col-lg-3 col-6 mb-3">
          <label for="weight" class="form-label">Вес</label>
          <input type="number" class="form-control" id="weight" placeholder="0,0">
        </div>
        <div class="col-lg-9 mb-3">
          <label for="weight" class="form-label">Способ доставки</label>

          <div class="list-group">
            <label class="list-group-item d-flex gap-2">
              <input class="form-check-input flex-shrink-0" type="radio" name="type_delivery" id="standart" value="1" checked="">
              <span>15-20 дней (Стандарт)
                <!-- <small class="d-block text-body-secondary">Стандартная доставка</small> -->
              </span>
            </label>
            <label class="list-group-item d-flex gap-2">
              <input class="form-check-input flex-shrink-0" type="radio" name="type_delivery" id="fast" value="2">
              <span>8-12 дней (Быстрая)
                <!-- <small class="d-block text-body-secondary">Быстрая доставка</small> -->
              </span>
            </label>
          </div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Посчитать</button>
    </form>
  </div>

  <!-- Interesting -->
  <div class="px-4 pt-5 my-5 text-center border-bottom">
    <div class="col-lg-6 mx-auto">
      <h2 class=" fw-bold text-body-emphasis">Вы будете впечатлены насколько удобно мониторить процесс доставки и как быстро доставляется груз при отсутствии форс-мажорных обстоятельств.</h2>
      <!-- <p class="lead mb-4">Quickly design and customize responsive mobile-first sites with Bootstrap, the world’s most popular front-end open source toolkit, featuring Sass variables and mixins, responsive grid system, extensive prebuilt components, and powerful JavaScript plugins.</p>
      <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mb-5">
        <button type="button" class="btn btn-primary btn-lg px-4 me-sm-3">Primary button</button>
        <button type="button" class="btn btn-outline-secondary btn-lg px-4">Secondary</button>
      </div> -->
    </div>
    <div class="overflow-hidden" style="">
      <div class="container px-3">
        <img src="/img/indira-tjokorda-OYtFO_heZ1Y-unsplash.jpg" class="img-fluid border rounded-3 shadow-lg mb-4" alt="Example image" width="700" height="" loading="lazy">
      </div>
    </div>
  </div>

  <!-- Desire -->
  <div class="container col-xxl-8 px-4 py-5">
    <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
        <h3 class="display-5 fw-bold text-body-emphasis lh-1 mb-3 text-center">Гарантия</h3>
      <div class="col-10 col-sm-8 col-lg-6">
        <img src="/img/jiawei-zhao-BsXeYX3efOI-unsplash.jpg" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" width="700" height="500" loading="lazy">
      </div>
      <div class="col-lg-6">
        <p class="lead">Логистика должна быть надежной и взаимовыгодной. Поэтому команда Jibekjol своевременно координирует процесс доставки, информирует вас о продвижении грузов и гарантирует возмещение испорченным или утерянным грузам.</p>
        <p class="lead">Важно учитывать! Если в процессе перевозки был испорчен хрупкий груз из за неправильной упаковки Ваших поставщиков, то мы не возмещаем средства груза. Так как для хрупких грузов необходима прочная упаковка. Мы же свою очередь обязуемся оперативно и безопасно доставить грузы.</p>
        <!-- <div class="d-grid gap-2 d-md-flex justify-content-md-start">
          <button type="button" class="btn btn-primary btn-lg px-4 me-md-2">Primary</button>
          <button type="button" class="btn btn-outline-secondary btn-lg px-4">Default</button>
        </div> -->
      </div>
    </div>
  </div>

  <!-- Advantages -->
  <div class="container px-4 py-5" id="featured-3">
    <hr>
    <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
      <div class="feature col">
        <span class="bi bi-stars text-primary display-3"></span>
        <h3 class="fs-2 text-body-emphasis">Надежная компания</h3>
        <p>Наша миссия, стать узнаваемой транснациональной компанией. Чтобы достичь этой цели, мы работаем по закону и даем гарантии на услуги. А специалистами Jibekjol являются логисты со стажем более 8 лет. </p>
      </div>
      <div class="feature col">
        <span class="bi bi-lightning text-primary display-3"></span>
        <h3 class="fs-2 text-body-emphasis">Быстрая доставка</h3>
        <p>Доставляем сборные и отдельные грузы всех габаритов в течении от 5-10 дней.</p>
      </div>
      <div class="feature col">
        <span class="bi bi-tags text-primary display-3"></span>
        <h3 class="fs-2 text-body-emphasis">Низкие цены</h3>
        <p>Мы работаем без посредников и имеем собственные склады, поэтому наш ценовой прайс ниже рыночной.</p>
      </div>
    </div>
  </div>

  <!-- Action -->
<div class="container col-xl-10 col-xxl-8 px-4 py-5">
    <div class="row align-items-center g-lg-5 py-5">
      <div class="col-lg-7 text-center text-lg-start">
        <h3 class="display-4 fw-bold lh-1 text-body-emphasis mb-3">Приглашаем всех сотрудничать вместе с нами:</h3>
        <p class="col-lg-10 fs-4">
        Физических лиц.
        Компании.
        Специалистов по тендерам.
        Продавцов и предпринимателей.

        Свяжитесь с нами, любым удобным способом.
        </p>
      </div>
      <div class="col-md-10 mx-auto col-lg-5">
        <form class="p-4 p-md-5 border rounded-3 bg-body-tertiary">
          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
            <label for="floatingInput">Email address</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
            <label for="floatingPassword">Password</label>
          </div>
          <div class="checkbox mb-3">
            <label>
              <input type="checkbox" value="remember-me"> Remember me
            </label>
          </div>
          <button class="w-100 btn btn-lg btn-primary" type="submit">Sign up</button>
          <hr class="my-4">
          <small class="text-body-secondary">By clicking Sign up, you agree to the terms of use.</small>
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
                <a href="/i/news/{{ $post->slug }}" class="btn btn-link">Дальше</a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <!-- FAQ -->
  <div class="container px-4 py-5">
    <h3 class="display-5 fw-bold text-body-emphasis lh-1 mb-3">Ответы на часто задаваемые вопросы</h3>
    <div class="accordion" id="accordionExample">
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            Accordion Item #1
          </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
          <div class="accordion-body">
            <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
            Accordion Item #2
          </button>
        </h2>
        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
          <div class="accordion-body">
            <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
            Accordion Item #3
          </button>
        </h2>
        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
          <div class="accordion-body">
            <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- START THE FEATURETTES -->
  <div class="container">
    <br>
    @if(!empty($promo))
      {!! $promo->content !!}
    @endif

  </div>

@endsection

@section('scripts')

@endsection