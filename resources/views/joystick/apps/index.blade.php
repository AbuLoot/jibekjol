@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Заявки</h2>

  @include('components.alerts')

  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td>Дата</td>
          <td>Имя</td>
          <td>Email</td>
          <td>Номер</td>
          <td>Текст</td>
          <td>Статус</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        @foreach ($apps as $app)
          <tr>
            <td>{{ $app->created_at }}</td>
            <td>{{ $app->name }}</td>
            <td>{{ $app->email }}</td>
            <td>{{ $app->phone }}</td>
            <td class="cell-mw">{{ $app->message }}</td>
            <td>{{ __('statuses.customer_apps.'.$app->status) }}</td>
            <td class="text-right text-nowrap">
              <a class="btn btn-link btn-xs" href="{{ route('apps.edit', [$lang, $app->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form method="POST" action="/{{ $lang }}/admin/apps/{{ $app->id }}" accept-charset="UTF-8" class="btn-delete">
                <input name="_method" type="hidden" value="DELETE">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  {{ $apps->links() }}

@endsection