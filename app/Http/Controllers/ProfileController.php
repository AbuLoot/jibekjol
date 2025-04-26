<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

use DB;
use Auth;
use Hash;
use Str;

use App\Models\User;
use App\Models\Region;
use App\Models\Country;
use App\Models\Language;
use App\Http\Requests;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $language = Language::where('slug', $user->lang)->first();

        return view('account.profile', compact('user', 'language'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        $regions = Region::orderBy('sort_id')->get()->toTree();
        $languages = Language::orderBy('sort_id')->get();

        // $date = [];
        // list($date['year'], $date['month'], $date['day']) = explode('-', $user->profile->birthday);

        return view('account.profile-edit', compact('user', 'regions', 'languages'));
    }

    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'tel' => ['required', 'string', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'id_client' => ['required', 'string', 'min:9', 'max:15'],
            'region_id' => ['required', 'integer'],
            'address' => ['required', 'string'],
        ]);

        $user = Auth::user();

        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->tel = $request->tel;
        $user->id_client = $request->id_client;
        $user->region_id = $request->region_id;
        $user->address = $request->address;
        // $user->id_name = $request->id_name;
        $user->lang = $request->lang;
        $user->status = $request->status;
        $user->save();

        // $user->profile->birthday = $request->birthday;
        // $user->profile->about = $request->about;
        // $user->profile->sex = $request->sex;
        // $user->profile->save();

        return redirect(app()->getLocale().'/profile')->with('status', 'Запись обновлена!');
    }

    public function pushSubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = auth()->user();
        $user->updatePushSubscription($request->endpoint, $request->input('keys.p256dh'), $request->input('keys.auth'));

        return response()->json(['title' => true]);
    }

    public function pushUnsubscribe(Request $request)
    {
        $user = auth()->user();
        $endpoint = $request->input('endpoint');

        if (empty($endpoint)) {
            return response()->json(['message' => 'Endpoint is required'], 400);
        }

        try {
            $user->deletePushSubscription($endpoint);
            return response()->json(['message' => 'Successfully unsubscribed'], 200);
        } catch (\Exception $e) {
            // Логируйте ошибку для отладки
            \Log::error('Error unsubscribing user ' . $user->id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Failed to unsubscribe'], 500);
        }
    }

    public function passwordEdit($lang)
    {
        return view('account.change-password');
    }

    public function passwordUpdate(Request $request, $lang)
    {
        $this->validate($request, [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::user();

        if ($user->email != $request->email) {
            return redirect()->back()->with('danger', 'Email не совпадает!');
        }

        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        return redirect(app()->getLocale().'/profile')->with('status', 'Запись обновлена!');
    }
}
