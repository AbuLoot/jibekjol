<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('app.your_parcel').' '.__('app.statuses.arrived') }} - Jibekjol</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Ysabeau:ital,wght@1,1000&display=swap" rel="stylesheet">

  <style type="text/css">
    body {
      max-width: 650px;
      margin: 0 auto;
      font-family: "Open Sans", sans-serif;
    }
    .brand {
      font-family: 'Ysabeau', sans-serif;
      color: #6610f2;
      font-weight: bold;
      text-transform: uppercase;
      text-align: center;
    }
    .bg-arrived { background-color: #4dd4ac !important; padding: 5px; margin-bottom: 5px; }
  </style>
</head>
<body>

  <h1 class="brand">Jibekjol</h1>

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