<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Joystick\Controller;
use App\Models\Company;
use App\Models\Region;
use App\Models\Branch;
use App\Models\User;

class BranchController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Branch::class);

        $branches = Branch::paginate(50);

        return view('joystick.branches.index', compact('branches'));
    }

    public function create($lang)
    {
        $this->authorize('create', Branch::class);

        $companies = Company::get();
        $regions = Region::orderBy('sort_id')->get()->toTree();
        $users = User::whereHas('roles', function(Builder $query) {
                $query->whereIn('id', [1, 2, 3, 4, 5]);
            })->get();

        return view('joystick.branches.create', compact('regions', 'companies', 'users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Branch::class);

        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:branches',
        ]);

        $branch = new Branch;
        $branch->company_id = $request->company_id;
        $branch->region_id = ($request->region_id > 0) ? $request->region_id : 0;
        $branch->user_id = $request->user_id;        
        $branch->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $branch->title = $request->title;
        $branch->address = $request->address;
        $branch->phones = $request->phones;
        $branch->status = ($request->status == 'on') ? 1 : 0;
        $branch->save();

        return redirect($request->lang.'/admin/branches')->with('status', 'Запись добавлена.');
    }

    public function edit($lang, $id)
    {
        $branch = Branch::findOrFail($id);
        $companies = Company::get();
        $regions = Region::orderBy('sort_id')->get()->toTree();
        $users = User::whereHas('roles', function(Builder $query) {
                $query->whereIn('id', [1, 2, 3, 4, 5]);
            })->get();

        $this->authorize('update', $branch);

        return view('joystick.branches.edit', compact('branch', 'companies', 'regions', 'users'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $branch = Branch::findOrFail($id);

        $this->authorize('update', $branch);

        $branch->company_id = $request->company_id;
        $branch->region_id = ($request->region_id > 0) ? $request->region_id : 0;
        $branch->user_id = $request->user_id;        
        $branch->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $branch->title = $request->title;
        $branch->address = $request->address;
        $branch->phones = $request->phones;
        $branch->status = ($request->status == 'on') ? 1 : 0;
        $branch->save();

        return redirect($lang.'/admin/branches')->with('status', 'Запись обновлена.');
    }

    public function destroy($lang, $id)
    {
        $branch = Branch::find($id);

        $this->authorize('delete', $branch);

        $branch->delete();

        return redirect($lang.'/admin/branches')->with('status', 'Запись удалена.');
    }

    public function searchUsers(Request $request, $lang)
    {
        $text = trim(strip_tags($request->text));

        $users = User::query()
            ->when(strlen($text) >= 2, function($query) use ($text) {
                $query->where('name', 'like', $text.'%')
                    ->orWhere('lastname', 'like', $text.'%')
                    ->orWhere('email', 'like', $text.'%')
                    ->orWhere('tel', 'like', '%'.$text.'%')
                    ->orWhere('id_client', 'like', '%'.$text.'%')
                    ->take(15);
            }, function($query) {
                $query->take(0);
            })
            ->get();

        if ($users->count() > 0) {
            return view('components.dropdown-managers', compact('users'));
        }
    }

    public function pinUser($lang, $userId)
    {
        $user = User::findOrFail($userId);

        return view('components.input-with-manager', compact('user'));
    }
}