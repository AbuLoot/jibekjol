<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use App\Http\Controllers\Joystick\Controller;
use App\Models\App;

class AppController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', App::class);

    	$apps = App::orderBy('created_at', 'desc')->paginate(50);

        return view('joystick.apps.index', compact('apps'));
    }

    public function show($id)
    {
        $app = App::findOrFail($id);

        $this->authorize('view', $app);

        return view('joystick.apps.show', compact('app'));
    }

    public function edit($lang, $id)
    {
        $app = App::findOrFail($id);

        $this->authorize('update', $app);

        return view('joystick.apps.edit', compact('app'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'status' => 'required|integer',
        ]);

        $app = App::findOrFail($id);

        $this->authorize('update', $app);

        // $app->name = $request->name;
        // $app->email = $request->email;
        // $app->phone = $request->phone;
        // $app->message = $request->message;
        $app->status = $request->status;
        $app->save();

        return redirect($lang.'/admin/apps')->with('status', 'Запись обновлена.');
    }

    public function destroy($lang, $id)
    {
        $app = App::find($id);

        $this->authorize('delete', $app);

        $app->delete();

        return redirect($lang.'/admin/apps')->with('status', 'Запись удалена.');
    }
}
