<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
// use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

use Rap2hpoutre\FastExcel\FastExcel;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;
use App\Notifications\TrackReceived;

class Reception extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $lang;
    public $search;
    public $statusReceived;
    public $trackCode;
    public $tracksDoc;

    protected $rules = [
        'trackCode' => 'required|string|min:10|max:20',
    ];

    public function mount()
    {
        if (auth()->user()->roles->first()->name == 'storekeeper-last') {
            return redirect(app()->getLocale().'/storage/arrival');
        }

        $this->lang = app()->getLocale();

        if (! Gate::allows('reception', auth()->user())) {
            abort(403);
        }

        $this->statusReceived = Status::select('id', 'sort_id', 'slug')
            ->where('slug', 'received')
            ->orWhere('id', 2)
            ->first();
    }

    public function toReceive()
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
        elseif ($track->status >= $this->statusReceived->id) {
            $this->addError('trackCode', 'Track '.$this->trackCode.' received');
            $this->trackCode = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $this->statusReceived->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->status = $this->statusReceived->id;
        $track->save();

        if ($track->user_id != NULL && $track->user->status === 1) {
            app()->setLocale($track->user->lang);
            $message = __('app.parcel_track', ['track_code' => $track->code]).Str::lcfirst(__('app.statuses.received'));
            $track->user->notify((new TrackReceived($message))->locale($track->user->lang));
            // Notification::send(auth()->user(), new TrackReceived($message));
        }

        $this->dispatchBrowserEvent('area-focus');
    }

    public function render()
    {
        $tracks = Track::orderByDesc('updated_at')
            ->where('status', $this->statusReceived->id)
            ->when((strlen($this->search) >= 4), function($query) {
                $query->where('code', 'like', '%'.$this->search.'%');
            })
            ->paginate(50);

        return view('livewire.storage.reception', ['tracks' => $tracks])
            ->layout('livewire.storage.layout');
    }
}
