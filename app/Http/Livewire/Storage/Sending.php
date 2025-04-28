<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\User;
use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;
use App\Notifications\TrackSent;

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

        // If tracks added up to two weeks
        $tracks = $tracksGroup->when($dateTo, function ($tracksGroup) use ($dateFrom, $dateTo) {

                // If tracks added today
                if ($dateFrom == now()->format('Y-m-d').' 00:00:00') {
                    return $tracksGroup->where('updated_at', '>', $dateFrom)->where('updated_at', '<', now());
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
        $tracksByLangUser = [];

        foreach ($tracks as $track) {

            $tracksStatus[] = [
                'track_id' => $track->id,
                'status_id' => $this->statusSent->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($track->user_id != NULL && $track->user->status === 1) {
                $tracksByLangUser[$track->user->lang][$track->user_id][] = $track;
            }
        }

        TrackStatus::insert($tracksStatus);

        // Updating Track Status
        Track::whereIn('id', $ids)->update(['status' => $this->statusSent->id]);

        // For Web Push Notification
        foreach($tracksByLangUser as $lang => $userTracks) {
            $usersId = array_keys($userTracks);
            $users = User::whereIn('id', $usersId)->get();
            app()->setLocale($lang);
            $message = __('app.parcel_group').Str::lcfirst(__('app.statuses.sent'));
            Notification::send($users, new TrackSent($message));
            // dd($users, $lang, $message, $userTracks);
        }

        // foreach ($tracksByUser as $userId => $tracks) {
            // if (is_numeric($userId)) {
                // Mail::to($tracks[0]->user->email)->send(new TrackSend($tracks[0]->user, $tracks));
            // }
        // }
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

        if ($track->user_id != NULL && $track->user->status === 1) {
            app()->setLocale($track->user->lang);
            $message = __('app.parcel_track', ['track_code' => $track->code]).Str::lcfirst(__('app.statuses.sent'));
            $track->user->notify(new TrackSent($message));
        }

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

        if ($track->user_id != NULL && $track->user->status === 1) {
            app()->setLocale($track->user->lang);
            $message = __('app.parcel_track', ['track_code' => $track->code]).Str::lcfirst(__('app.statuses.sent'));
            $track->user->notify((new TrackSent($message))->locale($track->user->lang));
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
