<ul class="dropdown-menu dropdown-menu-items">
  @foreach($users as $user)
    <li>
      <a hx-get="/{{ $lang }}/admin/branches/pin-user/{{ $user->id }}"
        hx-trigger="click"
        hx-target="#parent-dropdown-users"
        hx-swap="outerHTML"><b>{{ 'ID: '.$user->id_client.'. '.$user->name.' '.$user->lastname }}</b>
      </a>
    </li>
  @endforeach
</ul>