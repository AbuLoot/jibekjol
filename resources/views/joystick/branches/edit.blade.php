@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/branches" class="btn btn-primary"><i class="material-icons md-18">arrow_back</i></a>
  </p>
  <div class="row">
    <div class="col-md-7">
      <div class="panel panel-default">
        <div class="panel-body">
          <form action="{{ route('branches.update', [$lang, $branch->id]) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_method" value="PUT">
            {!! csrf_field() !!}

            <div class="form-group">
              <label for="title">Название</label>
              <input type="text" class="form-control" id="title" name="title" minlength="2" maxlength="80" value="{{ (old('title')) ? old('title') : $branch->title }}" required>
            </div>
            <div class="form-group">
              <label for="company_id">Компании</label>
              <select id="company_id" name="company_id" class="form-control" required>
                <option value=""></option>
                <?php foreach ($companies as $company) : ?>
                  <option value="{{ $company->id }}" <?php if ($company->id == $branch->company_id) echo 'selected'; ?>>{{ $company->title }}</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="region_id">Регионы</label>
              <select id="region_id" name="region_id" class="form-control">
                <option value=""></option>
                <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $branch) { ?>
                  <?php foreach ($nodes as $node) : ?>
                    <option value="{{ $node->id }}" <?= ($node->id == $branch->region_id) ? 'selected' : ''; ?>>{{ PHP_EOL.$prefix.' '.$node->title }}</option>
                    <?php $traverse($node->children, $prefix.'___'); ?>s
                  <?php endforeach; ?>
                <?php }; ?>
                <?php $traverse($regions); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="user_id">Менеджеры</label>
              <select id="user_id" name="user_id" class="form-control" required>
                <option value=""></option>
                <?php foreach ($users as $user) : ?>
                  <option value="{{ $user->id }}" <?php if ($user->id == $branch->user_id) { echo 'selected'; } ?>>{{ $user->name.' '.$user->surname }}</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="address">Адрес</label>
              <input type="text" class="form-control" id="address" name="address" value="{{ (old('address')) ? old('address') : $branch->address }}">
            </div>
            <div class="form-group">
              <label for="phones">Номера телефонов</label>
              <input type="text" class="form-control" id="phones" name="phones" value="{{ (old('phones')) ? old('phones') : $branch->phones }}">
            </div>
            <div class="form-group">
              <label for="status">Статус:</label>
              <label>
                <input type="checkbox" id="status" name="status" @if ($branch->status == 1) checked @endif> Активен
              </label>
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

@section('head')
  <link href="/joystick/css/jasny-bootstrap.min.css" rel="stylesheet">
@endsection

@section('scripts')
  <script src="/joystick/js/jasny-bootstrap.js"></script>
@endsection

