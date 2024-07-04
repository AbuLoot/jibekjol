
  @if (session('info'))role="alert">
    <div class="alert alert-info alert-dissmisble">
      {{ session('info') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session('warning'))
    <div class="alert alert-warning alert-dissmisble">
      {{ session('warning') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

	@if (session('status'))
	  <div class="alert alert-success alert-dissmisble">
	    {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	  </div>
	@endif

  @if (count($errors) > 0)
    <div class="alert alert-danger alert-dissmisble">
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
