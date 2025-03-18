@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/apps" class="btn btn-primary"><i class="material-icons md-18">arrow_back</i></a>
  </p>
  <div class="row">
    <div class="col-md-7">
      <div class="panel panel-default">
        <div class="panel-body">
          <form action="{{ route('apps.update', [$lang, $app->id]) }}" method="post">
            <input type="hidden" name="_method" value="PUT">
            {!! csrf_field() !!}

            <div class="form-group">
              <label for="date">Дата</label>
              <input type="text" class="form-control" id="date" name="date" value="{{ $app->created_at }}" readonly>
            </div>
            <div class="form-group">
              <label for="name">Имя</label>
              <input type="text" class="form-control" id="name" name="name" maxlength="80" value="{{ $app->name }}" disabled>
            </div>
            <div class="form-group">
              <label for="email">Email:</label>
              <input type="email" class="form-control" name="email" id="email" minlength="8" maxlength="60" value="{{ $app->email }}" disabled>
            </div>
            <div class="form-group">
              <label>Номер телефона</label>
              <input type="tel" pattern="(\+?\d[- .]*){7,13}" class="form-control" name="phone" placeholder="Номер телефона*" value="{{ $app->phone }}" disabled>
            </div>
            <div class="form-group">
              <label for="message">Сообщение</label>
              <textarea class="form-control" id="message" name="message" rows="5" disabled>{{ $app->message }}</textarea>
            </div>
            <div class="form-group">
              <label for="status">Статус</label>
              <select id="status" name="status" class="form-control" required>
                <option value="1" <?php if ($app->status == 1) { echo 'selected'; } ?>>{{ __('statuses.customer_apps.1') }}</option>
                <option value="2" <?php if ($app->status == 2) { echo 'selected'; } ?>>{{ __('statuses.customer_apps.2') }}</option>
              </select>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-success"><i class="material-icons">save</i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
