<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class OnRoute extends Component
{
    public $lang;
    public $search;
    public $mode = 'group';
    public $statusOnRoute;
    public $trackCode;
    public $trackCodes = [];
    public $allOnTheBorderTracks = [];

    protected $rules = [
        'trackCode' => 'required|string|min:10|max:20',
    ];

    public function mount()
    {
        if (! Gate::allows('on-route', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
        $this->statusOnRoute = Status::where('slug', 'on-route')->orWhere('id', 5)->first();
    }

    public function getTrackCodesById($trackIds = [])
    {
        $trackIds = rtrim($trackIds, ']');
        $trackIds = ltrim($trackIds, '[');
        $ids = explode(',', $trackIds);

        $this->trackCodes = Track::whereIn('id', $ids)->get();
        $this->dispatchBrowserEvent('open-modal');
    }

    public function getTracksIdByDate($dateFrom, $dateTo)
    {
        $tracksGroup = $this->allOnTheBorderTracks;

        $tracks = $tracksGroup->when($dateTo, function ($tracksGroup) use ($dateFrom, $dateTo) {

                // If tracks added today
                if ($dateTo == now()->format('Y-m-d H-i')) {
                    return $tracksGroup->where('updated_at', '>', $dateFrom.' 23:59:59')->where('updated_at', '<=', now());
                }

                return $tracksGroup->where('updated_at', '>', $dateFrom)->where('updated_at', '<', $dateTo);

            }, function ($tracksGroup) use ($dateFrom) {

                return $tracksGroup->where('updated_at', '<', $dateFrom);
            });

        return $tracks->pluck('id')->toArray();
    }

    public function openGroupByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $this->trackCodes = $this->allOnTheBorderTracks->whereIn('id', $ids)->sortByDesc('id');

        $this->dispatchBrowserEvent('open-modal');
    }

    public function onRouteGroupByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $tracks = $this->allOnTheBorderTracks->whereIn('id', $ids);

        // Creating Track Status
        $tracksStatus = [];
        $statusOnRouteId = $this->statusOnRoute->id;

        $tracks->each(function ($track) use (&$tracksStatus, $statusOnRouteId) {
            $tracksStatus[] = [
                'track_id' => $track->id,
                'status_id' => $statusOnRouteId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        TrackStatus::insert($tracksStatus);

        // Updating Track Status
        Track::whereIn('id', $ids)->update(['status' => $this->statusOnRoute->id]);
    }

    public function btnOnRoute($trackCode)
    {
        $this->trackCode = $trackCode;
        $this->onRoute();
        $this->search = null;
    }

    public function onRoute()
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
        elseif ($track->status >= $this->statusOnRoute->id) {
            $this->addError('trackCode', 'Track '.$this->trackCode.' on route');
            $this->trackCode = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $this->statusOnRoute->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->status = $this->statusOnRoute->id;
        $track->save();

        $this->trackCode = null;
        $this->dispatchBrowserEvent('area-focus');
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function render()
    {
        $statusOnTheBorder = Status::where('slug', 'on-the-border')->orWhere('id', 4)->first();

        if ($this->mode == 'list') {
            $onTheBorderTracks = Track::whereIn('status', [$statusOnTheBorder->id, $this->statusOnRoute->id])
                ->orderByDesc('updated_at')
                ->paginate(50);
        } else {
            $onTheBorderTracks = Track::where('status', $statusOnTheBorder->id)->get();
            $this->allOnTheBorderTracks = $onTheBorderTracks;
        }

        $tracks = [];

        if (strlen($this->search) >= 4) {
            $tracks = Track::orderByDesc('updated_at')
                ->whereIn('status', [$statusOnTheBorder->id, $this->statusOnRoute->id])
                ->where('code', 'like', '%'.$this->search.'%')
                ->paginate(10);
        }

        return view('livewire.storage.on-route', [
                'tracks' => $tracks,
                'onTheBorderTracks' => $onTheBorderTracks,
            ])
            ->layout('livewire.storage.layout');
    }
}
