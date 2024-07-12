<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('app.your_parcel').' '.__('app.statuses.arrived') }} - Jibekjol</title>

  <style type="text/css">
    body {
      max-width: 600px;
      margin: 0 auto;
      font-family: Arial, sans-serif;
    }
    .bg-arrived {
      background-color: #4dd4ac !important;
      padding: 5px;
      margin-bottom: 5px;
    }
  </style>
</head>
<body>
  <br>
  <div style="text-align: center;">
    <img src="/img/email-logo-0.png" alt="Jibekjol Company">
  </div>

  @if(count($tracks) > 1)
    <h2>
      {{ __('app.in_plural.dear_client', ['fullname' => $user->name.' '.$user->lastname]) }}
      {{ __('app.in_plural.arrived') }} ({{ $tracks[0]->regions->last()->title }}).
    </h2>
    <h3>{{ __('app.in_plural.info_tracks') }}:</h3>
  @else
    <h2>
      {{ __('app.dear_client', ['fullname' => $user->name.' '.$user->lastname]) }}
      {{ __('app.statuses.arrived') }} ({{ $tracks[0]->regions->last()->title }}).
    </h2>
    <h3>{{ __('app.info_track') }}:</h3>
  @endif

  @foreach($tracks as $track)
    <div class="bg-arrived">
      <div>{{ __('app.track_code') }}: <b>{{ $track->code }}</b></div>
      <div>{{ __('app.description') }}: <b>{{ $track->description }}</b></div>
    </div>
  @endforeach

  <h4><?php echo __('app.time').': '.date('Y-m-d G:i'); ?></h4>

  <p><a href="https://jibekjol.kz/">www.jibekjol.kz</a></p>
</body>
</html>