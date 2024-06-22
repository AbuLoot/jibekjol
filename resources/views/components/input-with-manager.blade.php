<div class="input-group">
  <input type="text" class="form-control" id="user_id" name="user_id" value="{{ $user->name . ' ' . $user->lastname }}" disabled>
  <div class="input-group-btn">
    <a class="btn btn-default"
      hx-get="/{{ $lang }}/admin/branches/unpin-user"
      hx-trigger="click"
      hx-target="#input-with-manager"
      hx-swap="outerHTML"><span class="material-icons md-18" data-toggle="tooltip" data-placement="bottom" title="Открепить пользователя">close</span>
    </a>
  </div>
</div>