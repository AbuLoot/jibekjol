<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
  <img src="https://jibekjol.kz/img/logo-jibekjol.png" width="160" height="42" alt="Jibekjol Company" style="display:block; margin: 0 auto;">

  <h3>Құрметті Jolldas компаниясының клиенттері.</h3>

  <p>Сіздерге маңызды өзгеріс туралы хабар жетіземіз. Jolldas компаниясы Jibekjol компаниясымен бірікті. Барлық қызметтер Jibekjol Cargo брендімен ұсынылатын болады. Сіз Jibekjol Cargo сервисіне кіру үшін, өзіңіздің бар логин мен құпия сөзіңізді қолдана аласыз.</p>

  <p>Сенім білдіріп бізді таңдағаныңыз үшін рақмет! Сәттілік тілейміз!</p>

  <p><a href="https://jibekjol.kz/">www.jibekjol.kz</a></p>

  <h3>Уважаемые клиенты компании Jolldas.</h3>

  <p>Сообщаем вам о важном изменении. Компания Jolldas объединилась с компанией Jibekjol. Все наши услуги будут предоставляться под брендом – Jibekjol Cargo. Вы можете продолжать пользоваться существующим логином и паролем для доступа к сервису Jibekjol Cargo.</p>

  <p>Благодарим вас за доверие и желаем вам успехов!</p>

  <p><a href="https://jibekjol.kz/">www.jibekjol.kz</a></p>

  <h4><?php echo date('Y-m-d G:i'); ?></h4>
  <p><a href="{{ $link }}">{{ __('app.unsubscribe_link') }}</a></p>

</body>
</html>