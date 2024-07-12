<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;
use App\Mail\TrackOnTheBorder;


class OnTheBorder extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $lang;
    public $search;
    public $mode = 'group';
    public $trackCode;
    public $trackCodes = [];
    public $statusOnTheBorder;
    public $allSentTracks = [];

    protected $rules = [
        'trackCode' => 'required|string|min:10|max:20',
    ];

    public function mount()
    {
        if (! Gate::allows('on-the-border', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
        $this->statusOnTheBorder = Status::where('slug', 'on-the-border')->orWhere('id', 4)->first();
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
        $tracksGroup = $this->allSentTracks;

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

        $this->trackCodes = $this->allSentTracks->whereIn('id', $ids)->sortByDesc('id');

        $this->dispatchBrowserEvent('open-modal');
    }

    public function markGroupByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $tracks = $this->allSentTracks->whereIn('id', $ids);

        // Creating Track Status
        $tracksStatus = [];
        $tracksByUser = [];

        foreach($tracks as $track) {
            $tracksStatus[] = [
                'track_id' => $track->id,
                'status_id' => $this->statusOnTheBorder->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // if (isset($track->user->email) && !in_array($track->user->email, $tracksUsers)) {
            //     $tracksUsers[] = $track->user->email;
            // }

            $tracksByUser[$track->user_id][] = $track;
        }

        TrackStatus::insert($tracksStatus);

        // Updating Track Status
        Track::whereIn('id', $ids)->update(['status' => $this->statusOnTheBorder->id]);

        foreach ($tracksByUser as $userId => $tracks) {
            if (is_numeric($userId)) {
                Mail::to($tracks[0]->user->email)->send(new TrackOnTheBorder($tracks[0]->user, $tracks));
            }
        }
    }

    public function btnToMark($trackCode)
    {
        $this->trackCode = $trackCode;
        $this->toMark();
        // $this->search = null;
    }

    public function toMark()
    {
        $this->validate();

        $statusOnTheBorder = Status::select('id', 'sort_id', 'slug')
            ->where('slug', 'on-the-border')
            ->orWhere('id', 4)
            ->first();

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
        elseif ($track->status >= $statusOnTheBorder->id) {
            $this->addError('trackCode', 'Track '.$this->trackCode.' was at the border');
            $this->trackCode = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $statusOnTheBorder->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->status = $statusOnTheBorder->id;
        $track->save();

        if (isset($track->user->email)) {
            app()->setLocale($track->user->lang);
            Mail::to($track->user->email)->send(new TrackOnTheBorder($track->user, [$track]));
        }

        $this->trackCode = null;
        $this->dispatchBrowserEvent('area-focus');
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function render()
    {
        $statusSent = Status::where('slug', 'sent')->orWhere('id', 3)->first();

        if ($this->mode == 'list') {
            $sentTracks = Track::whereIn('status', [$statusSent->id, $this->statusOnTheBorder->id])
                ->orderByDesc('updated_at')
                ->paginate(50);
        } else {
            $sentTracks = Track::where('status', $statusSent->id)->get();
            $this->allSentTracks = $sentTracks;
        }

        $tracks = [];

        if (strlen($this->search) >= 4) {
            $tracks = Track::orderByDesc('updated_at')
                ->whereIn('status', [$statusSent->id, $this->statusOnTheBorder->id])
                ->where('code', 'like', '%'.$this->search.'%')
                ->paginate(10);
        }

        return view('livewire.storage.on-the-border', [
                'tracks' => $tracks,
                'sentTracks' => $sentTracks,
            ])->layout('livewire.storage.layout');
    }
}
