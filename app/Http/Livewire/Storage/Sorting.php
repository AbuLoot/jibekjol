<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Region;
use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;
use App\Mail\TrackSorted;
use App\Notifications\TrackSorted as PushTrackSorted;

class Sorting extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $lang;
    public $search;
    public $region;
    public $trackCode;
    public $trackCodes = [];
    public $statusSorted = [];

    protected $rules = [
        'trackCode' => 'required|string|min:10|max:20',
    ];

    public function mount()
    {
        if (! Gate::allows('sorting', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
        $this->statusSorted = Status::select('id', 'slug')
            ->where('slug', 'sorted')
            ->orWhere('id', 6)
            ->first();

        if (!session()->has('jjRegion')) {
            $region = auth()->user()->region()->first() ?? Region::where('slug', 'kazakhstan')->orWhere('id', 1)->first();
            session()->put('jjRegion', $region);
        }

        $this->region = session()->get('jjRegion');
        $this->setRegionId = session()->get('jjRegion')->id;
    }

    public function btnToSort($trackCode)
    {
        $this->trackCode = $trackCode;
        $this->toSort();
        // $this->search = null;
    }

    public function toSort()
    {
        $this->validate();

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
        elseif ($track->status >= $this->statusSorted->id) {
        if (isset($track->user->email) && $track->user->status === 1) {
            app()->setlocale($track->user->lang);
            $message = __('app.parcel_track', ['track_code' => $track->code]).Str::lcfirst(__('app.statuses.sorted'));
            $track->user->notify(new PushTrackSorted($message));
        }
            $this->addError('trackCode', 'Track '.$this->trackCode.' sorted');
            $this->trackCode = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $this->statusSorted->id;
        $trackStatus->region_id = $this->region->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->status = $this->statusSorted->id;
        $track->save();

        if (isset($track->user->email) && $track->user->status === 1) {
            app()->setlocale($track->user->lang);
            $message = __('app.parcel_track', ['track_code' => $track->code]).Str::lcfirst(__('app.statuses.sorted'));
            $track->user->notify(new PushTrackSorted($message));
        }

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
    }

    public function render()
    {
        $this->region = session()->get('jjRegion');
        $this->setRegionId = session()->get('jjRegion')->id;

        $statusOnRoute = Status::where('slug', 'on-route')->orWhere('id', 6)->first();

        $sortableTracks = Track::whereIn('status', [$statusOnRoute->id, $this->statusSorted->id])->orderByDesc('updated_at')->paginate(50);

        $tracks = [];

        if (strlen($this->search) >= 4) {
            $tracks = Track::orderByDesc('updated_at')
                ->whereIn('status', [$statusOnRoute->id, $this->statusSorted->id])
                ->where('code', 'like', '%'.$this->search.'%')
                ->paginate(10);
        }

        return view('livewire.storage.sorting', [
                'tracks' => $tracks,
                'sortableTracks' => $sortableTracks,
                'regions' => Region::descendantsAndSelf(1)->toTree(),
            ])
            ->layout('livewire.storage.layout');
    }
}
