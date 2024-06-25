<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Rap2hpoutre\FastExcel\FastExcel;

use App\Models\Region;
use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;
use App\Models\Branch;

class SendLocally extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $lang;
    public $search;
    public $region;
    public $branch;
    public $trackCode;
    public $statusSentLocally;

    public function mount()
    {
        if (! Gate::allows('sending-locally', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
        $this->statusSentLocally = Status::select('id', 'sort_id', 'slug')
            ->where('slug', 'sent-locally')
            ->orWhere('id', 7)
            ->first();

        if (!session()->has('jjRegion')) {
            $region = auth()->user()->region()->first() ?? Region::where('slug', 'kazakhstan')->orWhere('id', 1)->first();
            session()->put('jjRegion', $region);
        }
    }

    public function btnToSendLocally($trackCode)
    {
        $this->trackCode = $trackCode;
        $this->toSendLocally();
    }

    public function toSendLocally()
    {
        $this->validate(['trackCode' => 'required|string|min:10|max:20']);

        $track = Track::where('code', $this->trackCode)->first();

        if (!$track) {
            $track = new Track;
            $track->user_id = null;
            $track->code = $this->trackCode;
            $track->description = '';
            $track->lang = app()->getLocale();
            $track->status = 0;
            $track->save();
        }
        elseif ($track->status >= $this->statusSentLocally->id) {
            $this->addError('trackCode', 'Track '.$this->trackCode.' sent locally');
            $this->trackCode = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $this->statusSentLocally->id;
        $trackStatus->region_id = $this->region->id;
        $trackStatus->branch_id = $this->branch->id ?? null;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->status = $this->statusSentLocally->id;
        $track->save();

        $this->trackCode = null;
        $this->dispatchBrowserEvent('area-focus');
    }

    public function setRegionId($id)
    {
        if (! Gate::allows('setting-regions', auth()->user())) {
            abort(403);
        }

        $region = Region::find($id);
        session()->put('jjRegion', $region);
        $this->branch = null;
    }

    public function setBranch($id)
    {
        $this->branch = Branch::find($id);
    }

    public function render()
    {
        $this->region = session()->get('jjRegion');
        $this->setRegionId = session()->get('jjRegion')->id;

        $statusSorted = Status::where('slug', 'sorted')->orWhere('id', 6)->first();

        $sentLocallyTracks = Track::whereIn('status', [$statusSorted->id, $this->statusSentLocally->id])
            ->orderByDesc('updated_at')
            ->paginate(50);

        $tracks = [];

        if (strlen($this->search) >= 4) {
            $tracks = Track::orderByDesc('updated_at')
                ->whereIn('status', [$statusSorted->id, $this->statusSentLocally->id])
                ->where('code', 'like', '%'.$this->search.'%')
                ->paginate(10);
        }

        return view('livewire.storage.send-locally', [
                'tracks' => $tracks,
                'sentLocallyTracks' => $sentLocallyTracks,
                'branches' => Branch::where('region_id', $this->setRegionId)->get(),
                'regions' => Region::descendantsAndSelf(1)->toTree(),
            ])
            ->layout('livewire.storage.layout');
    }
}
