<div>

  <div class="py-3 border-bottom mb-3">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">

      <h4 class="col-12 col-lg-4 mb-md-2 mb-lg-0">Search</h4>

      <form class="col-10 col-lg-4 mb-md-2 mb-lg-0 me-lg-auto">
        <input wire:model="search" type="search" class="form-control form-control-lg" placeholder="Enter track code..." aria-label="Search">
      </form>

      <div class="col-2 col-lg-4 mb-md-2 mb-lg-0 ms-lg-auto text-end">
        <div class="dropdown">
          <button class="btn btn-outline-primary btn-lg dropdown-toggle-" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-file-earmark-arrow-down me-sm-2"></i> <span class="d-none d-md-inline">Export</span>
          </button>
          <form method="POST" action="/{{ $lang }}/admin/export-tracks" class="dropdown-menu dropdown-menu-end p-4">
            @csrf
            <div class="mb-3">
              <label class="form-label">Start</label>
              <input type="date" name="startDate" class="form-control form-control-lg" value="" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd">
            </div>
            <div class="mb-3">
              <label class="form-label">End</label>
              <input type="date" name="endDate" class="form-control form-control-lg" value="" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd">
            </div>
            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary btn-lg"><i class="bi bi-file-earmark-arrow-down me-sm-2"></i> Export doc</button>
          </form>
        </div>
      </div>

    </div>
  </div>

  <div class="container">
    @foreach($tracks as $track)
      <div class="track-item mb-2">
        <?php
          $activeStatus = $track->statuses->last();
          $sortedRegion = $track->regions->last()->title ?? __('statuses.regions.title');
        ?>
        <div class="row">
          <div class="col-10 col-lg-10">
            <div class="border {{ __('statuses.classes.'.$activeStatus->slug.'.card-color') }} rounded-top p-2" data-bs-toggle="collapse" href="#collapse{{ $track->id }}">
              <div class="row">
                <div class="col-12 col-lg-5">
                  <div><b>Track code:</b> {{ $track->code }}</div>
                  <div><b>Description:</b> {{ Str::limit($track->description, 35) }}</div>
                </div>
                <div class="col-12 col-lg-4">
                  <div><b>{{ ucfirst($activeStatus->slug) }} date:</b> {{ $activeStatus->pivot->created_at }}</div>
                  <div><b>Status:</b> {{ __('app.statuses.'.$activeStatus->slug) }} {{ $sortedRegion }}</div>
                </div>
                @if($track->user) 
                  <div class="col-12 col-lg-3">
                    <b>User:</b> {{ $track->user->name.' '.$track->user->lastname }}<br>
                    <b>ID:</b> {{ $track->user->id_client }}
                  </div>
                @endif
              </div>
            </div>

            <div class="collapse" id="collapse{{ $track->id }}">
              <div class="border border-top-0 rounded-bottom p-3">
                <section>
                  <ul class="timeline-with-icons">
                    @foreach($track->statuses()->orderByPivot('created_at', 'desc')->get() as $status)

                      @if($activeStatus->id == $status->id)
                        <li class="timeline-item mb-2">
                          <span class="timeline-icon bg-success"><i class="bi bi-check text-white"></i></span>
                          <p class="text-success mb-0">{{ __('app.statuses.'.$status->slug) }}</p>
                          <p class="text-success mb-0">{{ $status->pivot->created_at }}</p>
                        </li>
                        @continue
                      @endif

                      <li class="timeline-item mb-2">
                        <span class="timeline-icon bg-secondary"><i class="bi bi-check text-white"></i></span>
                        <p class="text-body mb-0">{{ __('app.statuses.'.$status->slug) }}</p>
                        <p class="text-body mb-0">{{ $status->pivot->created_at }}</p>
                      </li>
                    @endforeach
                  </ul>
                  <p><b>Description:</b> {{ $track->description }}</p>
                </section>
              </div>
            </div>
          </div>
          <div class="col-2 col-lg-2 text-end">
            @if($track->status != $statusSentLocally->id)
              <div class="d-grid">
                <button wire:click="btnToSendLocally('{{ $track->code }}')" type="button" wire:loading.attr="disabled" class="btn btn-primary btn-lg-"><i class="bi bi-send-plus-fill"></i> <span class="d-none d-sm-inline">To send</span></button>
              </div>
            @endif
          </div>
        </div>
      </div>
    @endforeach

    <h3>Send locally</h3>

    <div class="row">
      <div class="col-12 col-sm-4 mb-2">
        <form wire:submit.prevent="toSendLocally">

          <div class="input-group @error('trackCode') has-validation @enderror mb-3">
            <div class="form-floating @error('trackCode') is-invalid @enderror">
              <input wire:model.defer="trackCode" type="text" class="form-control form-control-lg" placeholder="Add track-code" id="trackCodeArea">
              <label for="trackCodeArea">Enter track code</label>
            </div>
            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalUploadDoc"><i class="bi bi-file-earmark-arrow-up-fill"></i> </button>
            @error('trackCode')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="btn-group mb-3" role="group" aria-label="Button group with nested dropdown">
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                {{ $region->title }}
              </button>

              <ul class="dropdown-menu" style="max-height: 400px; overflow-y: auto; padding-bottom: 50px;">
                <?php $traverse = function ($nodes, $prefix = null) use (&$traverse) { ?>
                  <?php foreach ($nodes as $node) : ?>
                    <li><a wire:click="setRegionId('{{ $node->id }}')" class="dropdown-item" href="#">{{ PHP_EOL.$prefix.' '.$node->title }}</a></li>
                    <?php $traverse($node->children, $prefix.'___'); ?>
                  <?php endforeach; ?>
                <?php }; ?>
                <?php $traverse($regions); ?>
              </ul>
            </div>
            <button type="submit" id="toSendLocally" wire:loading.attr="disabled" class="btn btn-primary btn-lg"><i class="bi bi-send-plus-fill"></i> To send</button>
          </div>
        </form>

        @if($branches->count() > 0)
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              {{ $branch->title ?? 'Филиалы' }}
            </button>

            <ul class="dropdown-menu" style="max-height: 400px; overflow-y: auto; padding-bottom: 50px;">
              <?php foreach ($branches as $branchItem) : ?>
                <li><a wire:click="setBranch('{{ $branchItem->id }}')" class="dropdown-item" href="#">{{ $branchItem->title }}</a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        @endif
      </div>
      <div class="col-12 col-sm-8">

        @if(session('result'))
          <div class="alert alert-info">
            <h4>Total tracks count: {{ session('result')['totalTracksCount'] }}pcs</h4>
            <h4>Sent locally tracks count: {{ session('result')['sentTracksCount'] }}pcs</h4>
            <h4>Existent tracks count: {{ session('result')['existentTracksCount'] }}pcs</h4>
            <?php session()->forget('result'); ?>
            <div>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          </div>
        @endif

        @foreach($sentLocallyTracks as $track)
          <div class="track-item mb-2">
            <?php
              $activeStatus = $track->statuses->last();
              $sortedRegion = $track->regions->last()->title ?? __('statuses.regions.title');
            ?>
            <div class="border {{ __('statuses.classes.'.$activeStatus->slug.'.card-color') }} rounded-top p-2" data-bs-toggle="collapse" href="#collapse{{ $track->id }}">
              <div class="row">
                <div class="col-12 col-lg-6">
                  <div><b>Track code:</b> {{ $track->code }}</div>
                  <div><b>Description:</b> {{ Str::limit($track->description, 35) }}</div>
                </div>
                <div class="col-12 col-lg-6">
                  <div><b>{{ ucfirst($activeStatus->slug) }} date:</b> {{ $activeStatus->pivot->created_at }}</div>
                  <div>
                    <b>Status:</b> {{ __('app.statuses.'.$activeStatus->slug) }} {{ $sortedRegion }}
                    @if($track->branches->last())
                      <br>
                      <b>Branch:</b> {{ $track->branches->last()->title }}
                    @endif
                  </div>
                </div>
                @if($track->user) 
                  <div class="col-12 col-lg-12">
                    <b>User:</b> {{ $track->user->name.' '.$track->user->lastname }}<br>
                    <b>ID:</b> {{ $track->user->id_client }}
                  </div>
                @endif
              </div>
            </div>

            <div class="collapse" id="collapse{{ $track->id }}">
              <div class="border border-top-0 rounded-bottom p-3">
                <section>
                  <ul class="timeline-with-icons">
                    @foreach($track->statuses()->orderByPivot('created_at', 'desc')->get() as $status)

                      @if($activeStatus->id == $status->id)
                        <li class="timeline-item mb-2">
                          <span class="timeline-icon bg-success"><i class="bi bi-check text-white"></i></span>
                          <p class="text-success mb-0">{{ __('app.statuses.'.$status->slug) }} {{ $sortedRegion }}</p>
                          <p class="text-success mb-0">{{ $status->pivot->created_at }}</p>
                        </li>
                        @continue
                      @endif

                      <li class="timeline-item mb-2">
                        <span class="timeline-icon bg-secondary"><i class="bi bi-check text-white"></i></span>
                        <p class="text-body mb-0">{{ __('app.statuses.'.$status->slug) }}</p>
                        <p class="text-body mb-0">{{ $status->pivot->created_at }}</p>
                      </li>
                    @endforeach
                  </ul>
                  <p><b>Description:</b> {{ $track->description }}</p>
                </section>
              </div>
            </div>
          </div>
        @endforeach
        <br>
        <nav aria-label="Page navigation">
          {{ $sentLocallyTracks->links() }}
        </nav>
      </div>
    </div>
  </div>

  <br>

  <div class="modal fade" id="modalUploadDoc" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="/{{ $lang }}/admin/upload-tracks" method="post" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="storageStage" value="send-locally">
          <input type="hidden" name="branchId" value="{{ $branch->id ?? null }}">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="modalLabel">Uploading Track Codes</h1>
            <button type="button" id="closeUploadDoc" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label" for="tracksDocArea">Select document</label>
              <input type="file" name="tracksDoc" class="form-control form-control-lg @error('tracksDoc') is-invalid @enderror" placeholder="Add tracks doc" id="tracksDocArea" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.oasis.opendocument.spreadsheet,application/vnd.oasis.opendocument.spreadsheet,application/vnd.ms-excel">
              @error('tracksDoc')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" id="uploadDoc" class="btn btn-primary btn-lg"><i class="bi bi-file-earmark-arrow-up-fill"></i> Upload doc</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

@section('scripts')
  <script type="text/javascript">
    // Focus Script
    window.addEventListener('area-focus', event => {

      var areaEl = document.getElementById('trackCodeArea');
      areaEl.value = '';
      areaEl.focus();
    })
  </script>
@endsection