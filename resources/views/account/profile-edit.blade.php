<x-app-layout>

  <div class="row">
    <div class="col-lg-5 col-md-7 col-sm-9 mx-auto">

      @include('components.alerts')

      <form action="/{{ $lang }}/profile" method="post" class="p-4 p-md-5 bg-light border rounded-3 bg-light">
        <input type="hidden" name="_method" value="PUT">
        {!! csrf_field() !!}
        <h2 class="fw-bold mb-0">{{ __('app.my_profile') }}</h2>
        <br>

        <div class="row">
          <div class="col">
            <div class="form-floating mb-3">
              <input type="text" name="name" class="form-control rounded-3" id="name" value="{{ $user->name }}" placeholder="{{ __('app.name') }}" required autofocus>
              <label for="name">{{ __('app.name') }}</label>
            </div>
          </div>
          <div class="col">
            <div class="form-floating mb-3">
              <input type="text" name="lastname" class="form-control rounded-3" id="lastname" value="{{ $user->lastname }}" placeholder="{{ __('app.surname') }}" required>
              <label for="lastname">{{ __('app.surname') }}</label>
            </div>
          </div>
        </div>
        <div class="form-floating mb-3">
          <input type="tel" class="form-control rounded-3" name="tel" id="tel" value="{{ $user->tel }}" placeholder="{{ __('app.phone') }}" required>
          <label for="tel">{{ __('app.phone') }}</label>
        </div>
        <div class="form-floating mb-3">
          <input type="email" class="form-control rounded-3" name="email" id="email" value="{{ $user->email }}" placeholder="name@example.com" required>
          <label for="email">{{ __('app.email') }}</label>
        </div>
        <div class="form-floating mb-3">
          <select id="region_id" name="region_id" class="form-control">
            <option value=""></option>
            <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $user) { ?>
              <?php foreach ($nodes as $node) : ?>
                <option value="{{ $node->id }}" <?= ($node->id == $user->region_id) ? 'selected' : ''; ?>>{{ PHP_EOL.$prefix.' '.$node->title }}</option>
                <?php $traverse($node->children, $prefix.'___'); ?>
              <?php endforeach; ?>
            <?php }; ?>
            <?php $traverse($regions); ?>
          </select>
          <label for="region_id">{{ __('app.region') }}</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" class="form-control rounded-3" name="address" id="address" value="{{ $user->address }}" placeholder="{{ __('app.address') }}" required>
          <label for="address">{{ __('app.address') }}</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" class="form-control rounded-3" name="id_client" id="id_client" value="{{ $user->id_client }}" placeholder="ID account: J7799...">
          <label for="id_client">ID account</label>
        </div>
        <div class="form-floating mb-3">
          <select id="lang" name="lang" class="form-control">
            <?php foreach ($languages as $language) : ?>
              <option value="{{ $language->slug }}" <?= ($language->slug == $user->lang) ? 'selected' : ''; ?>>{{ $language->title }}</option>
            <?php endforeach; ?>
          </select>
          <label for="lang">{{ __('app.language') }}</label>
        </div>
        <!-- <div class="form-floating mb-3">
          <input type="text" class="form-control rounded-3" name="id_name" id="id_name" value="{{ $user->id_name }}" placeholder="ID name Taobao, Alibaba...">
          <label for="id_name">ID name</label>
        </div> -->

        <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">{{ __('app.save') }}</button><br>
        <a href="/{{ $lang }}/profile/password/edit" class="w-100 mb-2 btn btn-lg rounded-3 btn-link">{{ __('app.change_password') }}</a>
      </form>
    </div>
  </div>

</x-app-layout>