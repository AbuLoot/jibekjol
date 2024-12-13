<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Track;

// use App\Http\Controllers\Cargo\UserInfo;

class VerifyUserController extends Controller
{
    public function view()
    {
        return view('auth.verify-user');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'tel' => ['required', 'string', 'max:15'],
            'region_id' => ['required', 'integer'],
            // 'id_client' => ['required', 'string', 'min:9', 'max:15'],
            // 'trackcode' => ['required', 'string', 'min:9', 'max:20'],
        ]);

        $user = User::query()
            ->where('email', $request->email)
            ->where('region_id', $request->region_id)
            ->where('tel', $request->tel)
            ->orWhere('id_client', $request->id_client)
            ->first();

        if (!$user OR ($request->no_trackcode == 'no-trackcode' AND Track::where('user_id', $user->id)->count() >= 1)) {
            return redirect()->back()->withInput()->with('warning', __('app.data_not_match'));
        }

        $trackCode = $request->trackcode;

        $existsTrack = Track::query()
            ->where('user_id', $user->id)
            ->when(!$request->no_trackcode, function($query) use ($trackCode) {
                $query->where('code', $trackCode);
            })
            ->first();

        if (!$existsTrack) {
            return redirect()->back()->withInput()->with('warning', __('app.tc_not_match'));
        }

        $request->session()->put('verifiedUser', $user->id);

        return redirect(app()->getLocale().'/change-password');
    }
}
