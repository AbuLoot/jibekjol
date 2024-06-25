<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\User;
use App\Models\Region;
use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class Giving extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $lang;
    public $search;
    public $statusGiven;
    public $region;
    public $idClient;
    public $trackCode;
    public $text;

    protected $rules = [
        'trackCode' => 'required|string|min:10|max:20',
    ];

    public function mount()
    {
        if (! Gate::allows('giving', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
        $this->statusGiven = Status::where('slug', 'given')->orWhere('id', 9)->first();

        if (!session()->has('jjRegion')) {
            $region = auth()->user()->region()->first() ?? Region::where('slug', 'kazakhstan')->orWhere('id', 1)->first();
            session()->put('jjRegion', $region);
        }
    }

    public function btnToGive($trackCode)
    {
        $this->trackCode = $trackCode;
        $this->toGive();
        $this->search = null;
    }

    public function toGive()
    {
        $this->validate();

        $track = Track::where('code', $this->trackCode)->first();

        if (!$track) {
            $track = new Track;
            $track->user_id = session('givingToUser')->id ?? null;
            $track->code = $this->trackCode;
            $track->description = '';
            $track->lang = app()->getLocale();
            $track->status = 0;
            $track->text = $this->text;
            $track->save();
        }
        elseif ($track->status >= $this->statusGiven->id) {
            $this->addError('trackCode', 'Track '.$this->trackCode.' given');
            $this->trackCode = null;
            $this->text = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $this->statusGiven->id;
        $trackStatus->region_id = $this->region->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->user_id = session('givingToUser')->id ?? $track->user_id;
        $track->status = $this->statusGiven->id;
        $track->text = $this->text;
        $track->save();

        $this->trackCode = null;
        $this->text = null;
        $this->dispatchBrowserEvent('area-focus');
    }

    public function attachUser($id)
    {
        session()->put('givingToUser', User::findOrFail($id));
    }

    public function detachUser()
    {
        session()->forget('givingToUser');
        $this->idClient = 'J7799';
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

        $arrivedTracks = Track::where('status', $this->statusGiven->id)->orderByDesc('id')->paginate(50);

        $tracks = [];
        $users = [];

        if (strlen($this->search) >= 4) {
            $statusArrived = Status::where('slug', 'arrived')->orWhere('id', 8)->first();

            $tracks = Track::orderByDesc('id')
                ->whereIn('status', [$statusArrived->id, $this->statusGiven->id])
                ->where('code', 'like', '%'.$this->search.'%')
                ->paginate(10);
        }

        if (strlen($this->idClient) >= 9) {
            $users = User::orderBy('id', 'desc')
                ->where('id_client', 'like', '%'.$this->idClient.'%')
                ->get()
                ->take(10);
        }

        return view('livewire.storage.giving', [
                'tracks' => $tracks,
                'users' => $users,
                'arrivedTracks' => $arrivedTracks,
                'regions' => Region::descendantsAndSelf(1)->toTree(),
            ])->layout('livewire.storage.layout');
    }
}
