@extends('layout')

@section('meta_title', 'Tracks page')

@section('meta_description', 'Tracks page')

@section('head')

@endsection

@section('content')

  <div class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="/img/storage/storage-01.jpg" class="d-block w-lg-100 h-100 " alt="...">
        <div class="carousel-caption d-none-d-md-block">
          <h2 class="d-none-d-md-block fw-normal shadow-1">{{ __('app.tracking_by_code') }}</h2>
          <form action="/{{ $lang }}/search-track" method="get" class="col-12 col-lg-8 offset-lg-2 mt-lg-0 mb-3 mb-lg-0 me-lg-2 py-2" role="search">
            <input type="search" name="code" class="form-control form-control-dark form-control-lg -text-bg-dark" placeholder="{{ __('app.enter_track_code') }}" aria-label="Search" min="4" required>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="container my-3 my-lg-5">

      <div class="col-12 col-sm-12">

        @foreach($tracks as $track)
          <div class="track-item mb-2">

            <?php
              $activeStatus = $track->statuses->last();

              $trackAndRegion = null;

              if (in_array($activeStatus->slug, ['sorted', 'arrived', 'sent-locally', 'given']) OR in_array($activeStatus->id, [4, 5, 6, 7])) {

                $trackAndRegion = $track->regions->last()->title ?? __('statuses.regions.title');
                $trackAndRegion = '('.$trackAndRegion.', Казахстан)';
              }
            ?>
            <div class="border {{ __('statuses.classes.'.$activeStatus->slug.'.card-color') }} rounded-top p-2" data-bs-toggle="collapse" href="#collapse{{ $track->id }}">
              <div class="row">
                <div class="col-12 col-lg-5">
                  <div><b>{{ __('app.track_code') }}:</b> {{ $track->code }}</div>
                  <div><b>{{ __('app.description') }}:</b> {{ Str::limit($track->description, 5) }}</div>
                </div>
                <div class="col-12 col-lg-4">
                  <div><b>{{ ucfirst($activeStatus->slug) }} {{ __('app.date') }}:</b> {{ $track->updated_at }}</div>
                  <div><b>{{ __('app.status') }}:</b> {{ __('app.statuses.'.$activeStatus->slug) }} {{ $trackAndRegion }}</div>
                </div>
                @if($track->user) 
                  <div class="col-12 col-lg-3">
                    <b>User:</b> {{ $track->user->name.' '.\Str::limit($track->user->lastname, 1, '.') }}<br>
                    <b>ID:</b> {{ \Str::limit($track->user->id_client, 8) }}<br>
                  </div>
                @endif
              </div>
            </div>

            <div class="collapse" id="collapse{{ $track->id }}">
              <div class="border border-top-0 rounded-bottom p-3">
                <section>
                  <ul class="timeline-with-icons">
                    @foreach($track->statuses()->orderByPivot('created_at', 'desc')->get() as $status)

                      @if($activeStatus->id == $status->id)
                        <li class="timeline-item mb-2">
                          <span class="timeline-icon bg-success"><i class="bi bi-check text-white"></i></span>
                          <p class="text-success mb-0">{{ __('app.statuses.'.$status->slug) }} {{ $trackAndRegion }}</p>
                          <p class="text-success mb-0">{{ $status->pivot->created_at }}</p>
                        </li>
                        @continue
                      @endif

                      <li class="timeline-item mb-2">
                        <span class="timeline-icon bg-secondary"><i class="bi bi-check text-white"></i></span>
                        <p class="text-body mb-0">{{ __('app.statuses.'.$status->slug) }}</p>
                        <p class="text-body mb-0">{{ $status->pivot->created_at }}</p>
                      </li>
                    @endforeach
                  </ul>
                  <p><b>{{ __('app.description') }}:</b> {{ Str::limit($track->description, 5) }}</p>
                </section>
              </div>
            </div>
          </div>
        @endforeach

      </div>
  </div>

@endsection