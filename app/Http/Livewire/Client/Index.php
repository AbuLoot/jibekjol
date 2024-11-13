<?php

namespace App\Http\Livewire\Client;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Track;
use App\Models\Status;
use App\Models\Region;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $lang;
    public $search;
    public Track $track;
    public $statusId = 0;

    protected $listeners = [
        'newData' => '$refresh',
    ];

    public function mount()
    {
        $this->lang = app()->getLocale();
    }

    public function editTrack($id)
    {
        $this->emit('editTrack', $id);
    }

    public function deleteTrack($id)
    {
        Track::destroy('id', $id);
    }

    public function archiveTrack($id)
    {
        Track::where('id', $id)->update(['state' => 0]);
    }

    public function render()
    {
        $statuses = Status::get();

        $tracksCount = Track::where('user_id', auth()->user()->id)
            ->when($this->statusId > 0, function($query) {
                $query->where('status', $this->statusId);
            })
            ->count();

        $tracks = Track::where('user_id', auth()->user()->id)
            ->where('state', 1)
            ->orderBy('id', 'desc')
            ->when($this->statusId > 0, function($query) {
                $query->where('status', $this->statusId);
            })
            ->when((strlen($this->search) >= 2), function($query) {
                $query->where('code', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%')
                    ->where('user_id', auth()->user()->id);
            })
            ->paginate(50);

        return view('livewire.client.index', [
                'tracks' => $tracks,
                'tracksCount' => $tracksCount,
                'statuses' => $statuses,
                'regions' => Region::get()->toTree(),
            ])
            ->layout('livewire.client.layout');
    }
}
