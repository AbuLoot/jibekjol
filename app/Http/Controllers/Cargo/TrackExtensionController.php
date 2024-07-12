<?php

namespace App\Http\Controllers\Cargo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use Rap2hpoutre\FastExcel\FastExcel;

use App\Models\Status;
use App\Models\Track;
use App\Models\TrackStatus;
use App\Mail\TrackOnTheBorder;
use App\Mail\TrackSorted;
use App\Mail\TrackArrived;

class TrackExtensionController extends Controller
{
    public $lang;

    public function __construct()
    {
        $this->lang = app()->getLocale();
    }

    public function uploadTracks(Request $request)
    {
        $this->validate($request, [
            'tracksDoc' => 'required|mimetypes:application/vnd.oasis.opendocument.spreadsheet,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel'
        ]);

        $docName = date('t-m-d H:i:s').'.'.$request->file('tracksDoc')->extension();

        $request->tracksDoc->storeAs('files', $docName);

        $trackCodes = (new FastExcel)->import('files/'.$docName, function($line) {
            return $line['Code'] ?? $line['code'];
        });

        if ($request->storageStage == 'reception') {
            $result = $this->toReceiveTracks($trackCodes);
        }
        elseif ($request->storageStage == 'sending') {
            $result = $this->toSendTracks($trackCodes);
        }
        elseif ($request->storageStage == 'on-the-border') {
            $result = $this->toOnTheBorderTracks($trackCodes);
        }
        elseif ($request->storageStage == 'on-route') {
            $result = $this->toOnRouteTracks($trackCodes);
        }
        elseif ($request->storageStage == 'sorting') {
            $result = $this->toSortTracks($trackCodes);
        }
        elseif ($request->storageStage == 'send-locally') {
            $result = $this->toSendLocallyTracks($trackCodes, $request->branchId);
        }
        elseif ($request->storageStage == 'arrival') {
            $result = $this->toArriveTracks($trackCodes);
        }
        elseif ($request->storageStage == 'giving') {
            $result = $this->toGiveTracks($trackCodes);
        }

        Storage::delete('files/'.$docName);

        return redirect()->back()->with(['result' => $result]);
    }

    public function exportTracks(Request $request)
    {
        $startDate = $request->startDate ?? date('Y-m-d');
        $endDate = $request->endDate ?? date('Y-m-d');

        $statusSentLocally = Status::select('id', 'slug')
            ->where('slug', 'sent-locally')
            ->orWhere('id', 7)
            ->first();

        $regionId = session()->get('jjRegion')->id;
        $regionName = ucfirst(session()->get('jjRegion')->slug);

        $sentLocallyTracks = Track::query()
            ->where('status', $statusSentLocally->id)
            ->whereHas('statuses', function($query) use ($regionId) {
                $query->where('region_id', $regionId);
            })
            ->where('updated_at', '>=', $startDate.' 00:00:01')
            ->where('updated_at', '<=', $endDate.' 23:59:59')
            ->get();

        $listTracks = [];

        $sentLocallyTracks->each(function ($item) use (&$listTracks) {
            $listTracks[] = [
                'Code' => $item->code,
                'Description' => $item->description,
                'Text' => $item->text,
            ];
        });

        $listTracks = collect($listTracks);

        $docName = 'Sent locally to '.$regionName.'. Start '.$startDate.' End '.$endDate;

        return (new FastExcel($listTracks))->download($docName.'.xlsx');
    }

    public function receptionTracks()
    {
        $fh = fopen('file-manager/tracks/reception-tracks.txt', 'r');

        $trackCodes = [];

        while ($line = fgets($fh)) {
            $trackCodes[] = trim($line);
        }

        fclose($fh);

        $this->toReceiveTracks($trackCodes);
    }

    public function arrivalTracks()
    {
        $fh = fopen('file-manager/tracks/arrival-tracks.txt', 'r');

        $trackCodes = [];

        while ($line = fgets($fh)) {
            // $item = strlen(trim($line)) > 9;
            $trackCodes[] = trim($line);
        }

        fclose($fh);

        $this->toArriveTracks($trackCodes);
    }

    public function toReceiveTracks($trackCodes)
    {
        $statusReceived = Status::where('slug', 'received')
            ->orWhere('id', 2)
            ->select('id', 'sort_id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->filter(function ($value) {
                return strlen($value) > 9;
            })->unique();

        $existentTracks = Track::where('status', '<', $statusReceived->id)->whereIn('code', $uniqueTrackCodes)            ->get();

        $unreceivedTracks = $existentTracks->where('status', '<', $statusReceived->id);
        $unreceivedTracksStatus = [];

        $receivedTracks = $existentTracks->where('status', '>=', $statusReceived->id);

        $unreceivedTracks->each(function ($item, $key) use (&$unreceivedTracksStatus, $statusReceived) {
            $unreceivedTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusReceived->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Update Existent but Unreceived Tracks
        $this->insertStatusesAndUpdateTracks($unreceivedTracks, $unreceivedTracksStatus, $statusReceived);

        $allReceivedTracks = $receivedTracks->merge($unreceivedTracks);

        // Create Nonexistent Tracks
        $nonexistentTracks = collect($trackCodes)->diff($allReceivedTracks->pluck('code'));

        $this->createTracksAndStatuses($nonexistentTracks, $statusReceived->id);

        return [
            'totalTracksCount' => $trackCodes->count(),
            'receivedTracksCount' => $unreceivedTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $receivedTracks->count(),
        ];
    }

    public function toSendTracks($trackCodes)
    {
        $statusSent = Status::where('slug', 'sent')
            ->orWhere('id', 3)
            ->select('id', 'sort_id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        // Get existent tracks
        $existentTracks = Track::where('status', '<=', $statusSent->id)->whereIn('code', $uniqueTrackCodes)->get();

        $unsentTracks = $existentTracks->where('status', '<', $statusSent->id);
        $unsentTracksStatus = [];

        $sentTracks = $existentTracks->where('status', '>=', $statusSent->id);

        $region = session()->get('jjRegion');

        $unsentTracks->each(function ($item, $key) use (&$unsentTracksStatus, $statusSent, $region) {
            $unsentTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusSent->id,
                'region_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Update Unarrived Tracks
        $this->insertStatusesAndUpdateTracks($unsentTracks, $unsentTracksStatus, $statusSent);

        $allSentTracks = $sentTracks->merge($unsentTracks);

        $nonexistentTracks = collect($trackCodes)->diff($allSentTracks->pluck('code'));

        $this->createTracksAndStatuses($nonexistentTracks, $statusSent->id);

        return [
            'totalTracksCount' => $trackCodes->count(),
            'sentTracksCount' => $unsentTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $sentTracks->count(),
        ];
    }

    public function toOnTheBorderTracks($trackCodes)
    {
        $statusOnTheBorder = Status::where('slug', 'on-the-border')
            ->orWhere('id', 4)
            ->select('id', 'sort_id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        // Get existent tracks
        $existentTracks = Track::where('status', '<=', $statusOnTheBorder->id)->whereIn('code', $uniqueTrackCodes)->get();

        $notOnTheBorderTracks = $existentTracks->where('status', '<', $statusOnTheBorder->id);
        $notOnTheBorderTracksStatus = [];
        $notOnTheBorderTracksByUser = [];

        $onTheBorderTracks = $existentTracks->where('status', '>=', $statusOnTheBorder->id);

        $region = session()->get('jjRegion');

        $notOnTheBorderTracks->each(function ($item, $key) use (&$notOnTheBorderTracksStatus, &$notOnTheBorderTracksByUser, $statusOnTheBorder, $region) {
            $notOnTheBorderTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusOnTheBorder->id,
                'region_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $notOnTheBorderTracksByUser[$item->user_id][] = $item;
        });

        // Update Unarrived Tracks
        $this->insertStatusesAndUpdateTracks($notOnTheBorderTracks, $notOnTheBorderTracksStatus, $statusOnTheBorder);

        $allOnTheBorderTracks = $onTheBorderTracks->merge($notOnTheBorderTracks);

        $nonexistentTracks = collect($trackCodes)->diff($allOnTheBorderTracks->pluck('code'));

        $this->createTracksAndStatuses($nonexistentTracks, $statusOnTheBorder->id);

        foreach ($notOnTheBorderTracksByUser as $userId => $tracks) {
            if (is_numeric($userId)) {
                app()->setLocale($tracks[0]->user->lang);
                Mail::to($tracks[0]->user->email)->send(new TrackOnTheBorder($tracks[0]->user, $tracks));
            }
        }

        return [
            'totalTracksCount' => $trackCodes->count(),
            'onTheBorderTracksCount' => $notOnTheBorderTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $onTheBorderTracks->count(),
        ];
    }

    public function toOnRouteTracks($trackCodes)
    {
        $statusOnRoute = Status::where('slug', 'on-route')
            ->orWhere('id', 5)
            ->select('id', 'sort_id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        // Get existent tracks
        $existentTracks = Track::where('status', '<=', $statusOnRoute->id)->whereIn('code', $uniqueTrackCodes)->get();

        $notOnRouteTracks = $existentTracks->where('status', '<', $statusOnRoute->id);
        $notOnRouteTracksStatus = [];

        $onRouteTracks = $existentTracks->where('status', '>=', $statusOnRoute->id);

        $region = session()->get('jjRegion');

        $notOnRouteTracks->each(function ($item, $key) use (&$notOnRouteTracksStatus, $statusOnRoute, $region) {
            $notOnRouteTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusOnRoute->id,
                'region_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Update Unarrived Tracks
        $this->insertStatusesAndUpdateTracks($notOnRouteTracks, $notOnRouteTracksStatus, $statusOnRoute);

        $allOnRouteTracks = $onRouteTracks->merge($notOnRouteTracks);

        $nonexistentTracks = collect($trackCodes)->diff($allOnRouteTracks->pluck('code'));

        $this->createTracksAndStatuses($nonexistentTracks, $statusOnRoute->id);

        return [
            'totalTracksCount' => $trackCodes->count(),
            'onRouteTracksCount' => $notOnRouteTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $onRouteTracks->count(),
        ];
    }

    public function toSortTracks($trackCodes)
    {
        $statusSorted = Status::where('slug', 'sorted')
            ->orWhere('id', 6)
            ->select('id', 'sort_id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        // Get existent tracks
        $existentTracks = Track::where('status', '<=', $statusSorted->id)->whereIn('code', $uniqueTrackCodes)->get();

        $unsortedTracks = $existentTracks->where('status', '<', $statusSorted->id);
        $unsortedTracksStatus = [];
        $unsortedTracksByUser = [];

        $arrivedTracks = $existentTracks->where('status', '>=', $statusSorted->id);

        $region = session()->get('jjRegion');

        $unsortedTracks->each(function ($item, $key) use (&$unsortedTracksStatus, &$unsortedTracksByUser, $statusSorted, $region) {
            $unsortedTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusSorted->id,
                'region_id' => $region->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $unsortedTracksByUser[$item->user_id][] = $item;
        });

        // Update Unarrived Tracks
        $this->insertStatusesAndUpdateTracks($unsortedTracks, $unsortedTracksStatus, $statusSorted);

        $allArrivedTracks = $arrivedTracks->merge($unsortedTracks);

        $nonexistentTracks = collect($trackCodes)->diff($allArrivedTracks->pluck('code'));

        $this->createTracksAndStatuses($nonexistentTracks, $statusSorted->id, $region->id);

        foreach ($unsortedTracksByUser as $userId => $tracks) {
            if (is_numeric($userId)) {
                app()->setLocale($tracks[0]->user->lang);
                Mail::to($tracks[0]->user->email)->send(new TrackSorted($tracks[0]->user, $tracks));
            }
        }

        return [
            'totalTracksCount' => $trackCodes->count(),
            'sortedTracksCount' => $unsortedTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $arrivedTracks->count(),
        ];
    }

    public function toSendLocallyTracks($trackCodes, $branchId = null)
    {
        $statusSentLocally = Status::where('slug', 'sent-locally')
            ->orWhere('id', 7)
            ->select('id', 'sort_id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        // Get existent tracks
        $existentTracks = Track::where('status', '<=', $statusSentLocally->id)->whereIn('code', $uniqueTrackCodes)->get();

        $unsentTracks = $existentTracks->where('status', '<', $statusSentLocally->id);
        $unsentTracksStatus = [];

        $sentTracks = $existentTracks->where('status', '>=', $statusSentLocally->id);

        $region = session()->get('jjRegion');

        $unsentTracks->each(function ($item, $key) use (&$unsentTracksStatus, $statusSentLocally, $region, $branchId) {
            $unsentTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusSentLocally->id,
                'region_id' => $region->id,
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Update Unarrived Tracks
        $this->insertStatusesAndUpdateTracks($unsentTracks, $unsentTracksStatus, $statusSentLocally);

        $allSentTracks = $sentTracks->merge($unsentTracks);

        $nonexistentTracks = collect($trackCodes)->diff($allSentTracks->pluck('code'));

        $this->createTracksAndStatuses($nonexistentTracks, $statusSentLocally->id, $region->id, $branchId);

        return [
            'totalTracksCount' => $trackCodes->count(),
            'sentTracksCount' => $unsentTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $sentTracks->count(),
        ];
    }

    public function toArriveTracks($trackCodes)
    {
        $statusArrived = Status::where('slug', 'arrived')
            ->orWhere('id', 8)
            ->select('id', 'sort_id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        // Get existent tracks
        $existentTracks = Track::where('status', '<=', $statusArrived->id)->whereIn('code', $uniqueTrackCodes)->get();

        $unarrivedTracks = $existentTracks->where('status', '<', $statusArrived->id);
        $unarrivedTracksStatus = [];
        $unarrivedTracksByUser = [];

        $arrivedTracks = $existentTracks->where('status', '>=', $statusArrived->id);

        $region = session()->get('jjRegion');

        $unarrivedTracks->each(function ($item, $key) use (&$unarrivedTracksStatus, &$unarrivedTracksByUser, $statusArrived, $region) {
            $unarrivedTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusArrived->id,
                'region_id' => $region->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $unarrivedTracksByUser[$item->user_id][] = $item;
        });

        // Update Unarrived Tracks
        $this->insertStatusesAndUpdateTracks($unarrivedTracks, $unarrivedTracksStatus, $statusArrived);

        $allArrivedTracks = $arrivedTracks->merge($unarrivedTracks);

        $nonexistentTracks = collect($trackCodes)->diff($allArrivedTracks->pluck('code'));

        $this->createTracksAndStatuses($nonexistentTracks, $statusArrived->id, $region->id);

        foreach ($unarrivedTracksByUser as $userId => $tracks) {
            if (is_numeric($userId)) {
                app()->setLocale($tracks[0]->user->lang);
                Mail::to($tracks[0]->user->email)->send(new TrackArrived($tracks[0]->user, $tracks));
            }
        }

        return [
            'totalTracksCount' => $trackCodes->count(),
            'arrivedTracksCount' => $unarrivedTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $arrivedTracks->count(),
        ];
    }

    public function toGiveTracks($trackCodes)
    {
        $statusGiven = Status::where('slug', 'given')
            ->orWhere('id', 9)
            ->select('id', 'sort_id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        $existentTracks = Track::where('status', '<=', $statusGiven->id)->whereIn('code', $uniqueTrackCodes)->get();

        $ungivenTracks = $existentTracks->where('status', '<', $statusGiven->id);
        $ungivenTracksStatus = [];

        $givenTracks = $existentTracks->where('status', '>=', $statusGiven->id);

        $region = session()->get('jjRegion');

        $ungivenTracks->each(function ($item, $key) use (&$ungivenTracksStatus, $statusGiven, $region) {
            $ungivenTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusGiven->id,
                'region_id' => $region->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Update Ungiven Tracks
        $this->insertStatusesAndUpdateTracks($ungivenTracks, $ungivenTracksStatus, $statusGiven);

        $allGivenTracks = $givenTracks->merge($ungivenTracks);

        $nonexistentTracks = collect($trackCodes)->diff($allGivenTracks->pluck('code'));

        $this->createTracksAndStatuses($nonexistentTracks, $statusGiven->id, $region->id);

        return [
            'totalTracksCount' => $trackCodes->count(),
            'givenTracksCount' => $ungivenTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $givenTracks->count(),
        ];
    }

    // Insert tracks statuses and update tracks
    protected function insertStatusesAndUpdateTracks($unupdatedTracks, $uninsertedTracksStatus, $trackStatus)
    {
        if ($unupdatedTracks->count() >= 1) {

            try {
                TrackStatus::insert($uninsertedTracksStatus);

                Track::whereIn('id', $unupdatedTracks->pluck('id')->toArray())
                    ->update(['status' => $trackStatus->id]);

            } catch (\Exception $e) {
                echo 'Error: '.$e->getMessage();
            }
        }

        return true;
    }

    // Create new tracks and statuses
    protected function createTracksAndStatuses($nonexistentTracks, $statusId, $regionId = null, $branchId = null)
    {
        foreach($nonexistentTracks as $code) {

            $newTrack = new Track;
            $newTrack->user_id = null;
            $newTrack->lang = $this->lang;
            $newTrack->code = $code;
            $newTrack->description = '';
            $newTrack->status  = $statusId;
            $newTrack->save();

            $trackStatus = new TrackStatus();
            $trackStatus->track_id = $newTrack->id;
            $trackStatus->status_id = $statusId;
            $trackStatus->region_id = $regionId;
            $trackStatus->branch_id = $branchId;
            $trackStatus->created_at = now();
            $trackStatus->updated_at = now();
            $trackStatus->save();
        }

        return true;
    }
}
