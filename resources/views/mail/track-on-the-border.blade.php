<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ваша посылка на границе</title>
</head>
<body>

  <h2>Уважаемый {{ $userTrack->name.' '.$userTrack->lastname }}.</h2>
  <h3>Ваша посылка под трек-кодом: {{ $track->code }}, уже на границе Китая и Казахстана.</h3>
  <h4>Дата: <?php echo date('Y-m-d'); ?></h4><br>
  <h4>Время: <?php echo date('G:i'); ?></h4>

  <p><a href="https://jibekjol.kz/">www.jibekjol.kz</a></p>

</body>
</html>