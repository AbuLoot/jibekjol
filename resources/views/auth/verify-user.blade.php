<x-app-layout>
  <div class="row">
    <div class="col-lg-5 col-md-7 col-sm-9 mx-auto">

      <!-- Validation Errors -->
      <x-auth-validation-errors class="mb-4" :errors="$errors" />

      @include('components.alerts')

      <form method="POST" action="/{{ $lang }}/verify-user" class="p-4 p-md-5 bg-light border rounded-3 bg-light">
        @csrf

        <h2 class="fw-bold mb-0">{{ __('app.password_recovery') }}</h2>
        <br>

        <div class="form-floating mb-3">
          <input type="email" class="form-control rounded-3" name="email" id="email" placeholder="name@example.com" value="{{ old('email') }}" required>
          <label for="email">{{ __('app.email') }}</label>
        </div>
        <div class="form-floating mb-3">
          <select class="form-control" name="region_id" id="region_id" required>
            <option value="">{{ __('app.select_a_city') }}</option>
            <?php $traverse = function ($nodes, $prefix = null) use (&$traverse) { ?>
              <?php foreach ($nodes as $node) : ?>
                <option value="{{ $node->id }}">{{ PHP_EOL.$prefix.' '.$node->title }}</option>
                <?php $traverse($node->children, $prefix.'___'); ?>
              <?php endforeach; ?>
            <?php }; ?>
            <?php
              $regions = \App\Models\Region::orderBy('sort_id')->get()->toTree();
              $traverse($regions);
            ?>
          </select>
          <label for="region_id">{{ __('app.region') }}</label>
        </div>
        <div class="form-floating mb-3">
          <input type="tel" class="form-control rounded-3" name="tel" id="tel" placeholder="{{ __('app.phone') }}" value="{{ old('tel') }}" required>
          <label for="tel">{{ __('app.phone') }}</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" class="form-control rounded-3" name="id_client" id="id_client" placeholder="Адрес" value="{{ old('id_client') }}">
          <label for="id_client">ID client</label>
        </div>

        <label class="form-label" for="trackcode">{{ __('app.enter_used_track') }}</label>
        <div class="form-floating mb-3">
          <input type="text" class="form-control form-control-lg rounded-3" name="trackcode" id="trackcode" placeholder="{{ __('app.track_code') }}" value="{{ old('trackcode') }}" required>
          <label for="trackcode">{{ __('app.track_code') }}</label>
        </div>

        <button type="submit" class="w-100 mb-2 btn btn-lg rounded-3 btn-primary">{{ __('app.send') }}</button>
        <!-- <hr class="my-4"> -->
        <!-- <small class="text-muted">By clicking Sign up, you agree to the terms of use.</small> -->
      </form>
    </div>
  </div>
</x-app-layout>
