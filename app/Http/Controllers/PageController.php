<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Page;
use App\Models\Post;
use App\Models\Section;

class PageController extends Controller
{
    public function index($lang)
    {
        $page = Page::where('slug', '/')->firstOrFail();
        $posts = Post::orderBy('sort_id')->where('lang', $lang)->where('status', 1)->get();
        $promo = Section::where('status', 1)
            ->whereIn('slug', ['promo', 'offer', 'second-offer', 'third-offer', 'fourth-offer', 'fifth-offer', 'faq'])
            ->where('lang', $lang)
            ->get();

        return view('index')->with(['page' => $page, 'posts' => $posts, 'promo' => $promo]);
    }

    public function page($lang, $slug)
    {
        $page = Page::where('slug', $slug)->where('lang', $lang)->firstOrFail();

        return view('page')->with('page', $page);
    }

    public function catalogs($lang)
    {
        $page = Page::where('slug', 'catalogs')->firstOrFail();

        $files = Storage::files('file-mananger/catalogs');

        return view('pages.catalogs')->with(['page' => $page, 'files' => $files]);
    }

    public function contacts()
    {
        $page = Page::where('slug', 'contacts')->firstOrFail();

        return view('pages.contacts')->with('page', $page);
    }
}
