<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class Sending extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $lang;
    public $search;
    public $mode = 'group';
    public $statusSent;
    public $trackCode;
    public $trackCodes = [];
    public $allReceivedTracks = [];

    protected $rules = [
        'trackCode' => 'required|string|min:10|max:20',
    ];

    public function mount()
    {
        if (! Gate::allows('sending', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
        $this->statusSent = Status::where('slug', 'sent')->orWhere('id', 3)->first();
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
        $tracksGroup = $this->allReceivedTracks;

        $tracks = $tracksGroup->when($dateTo, function ($tracksGroup) use ($dateFrom, $dateTo) {

                // If tracks added today
                if ($dateTo == now()->format('Y-m-d H-i')) {
                    return $tracksGroup->where('updated_at', '>', $dateFrom.' 00:00:00')->where('updated_at', '<=', now());
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

        $this->trackCodes = $this->allReceivedTracks->whereIn('id', $ids)->sortByDesc('id');

        $this->dispatchBrowserEvent('open-modal');
    }

    public function sendGroupByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $tracks = $this->allReceivedTracks->whereIn('id', $ids);

        // Creating Track Status
        $tracksStatus = [];
        $statusSentId = $this->statusSent->id;

        $tracks->each(function ($track) use (&$tracksStatus, $statusSentId) {
            $tracksStatus[] = [
                'track_id' => $track->id,
                'status_id' => $statusSentId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        TrackStatus::insert($tracksStatus);

        // Updating Track Status
        Track::whereIn('id', $ids)->update(['status' => $this->statusSent->id]);
    }

    public function btnToSend($trackCode)
    {
        $this->trackCode = $trackCode;
        $this->toSend();
        // $this->search = null;
    }

    public function toSend()
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
        elseif ($track->status >= $this->statusSent->id) {
            $this->addError('trackCode', 'Track '.$this->trackCode.' sent');
            $this->trackCode = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $this->statusSent->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->status = $this->statusSent->id;
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
        $statusReceived = Status::where('slug', 'received')->where('id', 2)->first();

        if ($this->mode == 'list') {
            $sentTracks = Track::where('status', $this->statusSent->id)->orderByDesc('updated_at')->paginate(50);
        }
        else {
            $sentTracks = Track::where('status', $statusReceived->id)->get();
            $this->allReceivedTracks = $sentTracks;
        }

        $tracks = [];

        if (strlen($this->search) >= 4) {
            $tracks = Track::orderByDesc('updated_at')
                ->whereIn('status', [$statusReceived->id, $this->statusSent->id])
                ->where('code', 'like', '%'.$this->search.'%')
                ->paginate(10);
        }

        return view('livewire.storage.sending', [
                'tracks' => $tracks,
                'sentTracks' => $sentTracks,
            ])
            ->layout('livewire.storage.layout');
    }
}
