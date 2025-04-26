<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\User;
use App\Models\Region;
use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

use App\Mail\TrackArrived;
// use App\Jobs\SendMailNotification;

class Arrival extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $lang;
    public $mode = 'list';
    public $search;
    public $region;
    public $idClient;
    public $trackCode;
    public $trackCodes = [];
    public $statusArrived;
    public $allArrivedTracks = [];
    public $text;

    public function mount()
    {
        if (! Gate::allows('arrival', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
        $this->statusArrived = Status::where('slug', 'arrived')->orWhere('id', 8)->first();

        if (!session()->has('jjRegion')) {
            $region = auth()->user()->region()->first() ?? Region::where('slug', 'kazakhstan')->orWhere('id', 1)->first();
            session()->put('jjRegion', $region);
        }
    }

    public function getTracksIdByDate($dateFrom, $dateTo)
    {
        $arrivedTracks = $this->allArrivedTracks;

        $tracks = $arrivedTracks->when($dateTo, function ($arrivedTracks) use ($dateFrom, $dateTo) {

                // If tracks added today
                if ($dateTo == now()->format('Y-m-d H-i')) {
                    return $arrivedTracks->where('updated_at', '>', $dateFrom.' 00:00:00')->where('updated_at', '<=', now());
                }

                return $arrivedTracks->where('updated_at', '>', $dateFrom)->where('updated_at', '<', $dateTo);

            }, function ($arrivedTracks) use ($dateFrom) {

                return $arrivedTracks->where('updated_at', '<', $dateFrom);
            });

        return $tracks->pluck('id')->toArray();
    }

    public function openGroupByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $this->trackCodes = $this->allArrivedTracks->whereIn('id', $ids)->sortByDesc('id');

        $this->dispatchBrowserEvent('open-modal');
    }

    public function groupArrivedByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $tracks = $this->allArrivedTracks->whereIn('id', $ids);

        // Creating Track Status
        $tracksStatus = [];
        $tracksUsers = [];

        foreach($tracks as $track) {
            $tracksStatus[] = [
                'track_id' => $track->id,
                'status_id' => $this->statusArrived->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (isset($track->user->email) && !in_array($track->user->email, $tracksUsers) && $track->user->status === 1) {
                $tracksUsers[] = $track->user->email;
            }
        }

        TrackStatus::insert($tracksStatus);

        // Updating Track Status
        Track::whereIn('id', $ids)->update(['status' => $this->statusArrived->id]);

        // SendMailNotification::dispatch($tracksUsers);
        foreach($tracksUsers as $emailUser) {
            app()->setlocale($track->user->lang);
            // Mail::to($emailUser)->send(new SendMailNotification());
            Mail::to($emailUser)->send(new TrackOnTheBorder($track));
        }
    }

    public function btnToArrive($trackCode)
    {
        $this->trackCode = $trackCode;
        $this->toArrive();
        // $this->search = null;
    }

    public function toArrive()
    {
        $this->validate(['trackCode' => 'required|string|min:10|max:20']);

        $track = Track::where('code', $this->trackCode)->first();

        if (!$track) {
            $track = new Track;
            $track->user_id = session('arrivalToUser')->id ?? null;
            $track->code = $this->trackCode;
            $track->description = '';
            $track->lang = app()->getLocale();
            $track->status = 0;
            $track->text = $this->text;
            $track->save();
        }
        elseif ($track->status >= $this->statusArrived->id) {
            $this->addError('trackCode', 'Track '.$this->trackCode.' arrived');
            $this->trackCode = null;
            $this->text = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $this->statusArrived->id;
        $trackStatus->region_id = $this->region->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->user_id = session('arrivalToUser')->id ?? $track->user_id;
        $track->text = $this->text;
        $track->status = $this->statusArrived->id;
        $track->save();

        if (isset($track->user->email) && $track->user->status === 1) {
            app()->setLocale($track->user->lang);
            Mail::to($track->user->email)->send(new TrackArrived($track->user, [$track]));
        }

        $this->trackCode = null;
        $this->text = null;
        $this->dispatchBrowserEvent('area-focus');
    }

    public function attachUser($id)
    {
        session()->put('arrivalToUser', User::findOrFail($id));
    }

    public function detachUser()
    {
        session()->forget('arrivalToUser');
        $this->idClient = 'J7799';
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
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

        if ($this->mode == 'list') {
            $arrivedTracks = Track::where('status', $this->statusArrived->id)->orderByDesc('updated_at')->paginate(50);
        } else {
            $arrivedTracks = Track::where('status', $this->statusArrived->id)->orderByDesc('updated_at')->get();
            $this->allArrivedTracks = $arrivedTracks;
        }

        $tracks = [];
        $users = [];

        if (strlen($this->search) >= 4) {
            $statusSentLocally = Status::where('slug', 'sent-locally')->orWhere('id', 7)->first();

            $tracks = Track::orderByDesc('updated_at')
                ->whereIn('status', [$statusSentLocally->id, $this->statusArrived->id])
                ->where('code', 'like', '%'.$this->search.'%')
                ->paginate(10);
        }

        if (strlen($this->idClient) >= 9) {
            $users = User::orderBy('id', 'desc')
                ->where('id_client', 'like', '%'.$this->idClient.'%')
                ->get()
                ->take(10);
        }

        return view('livewire.storage.arrival', [
                'tracks' => $tracks,
                'users' => $users,
                'arrivedTracks' => $arrivedTracks,
                'regions' => Region::descendantsAndSelf(1)->toTree(),
            ])
            ->layout('livewire.storage.layout');
    }
}
