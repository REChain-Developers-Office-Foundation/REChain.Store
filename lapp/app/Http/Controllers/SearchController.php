<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Category;
use App\Models\News;
use App\Models\Page;
use App\Models\Platform;
use App\Models\Setting;
use App\Models\Topic;
use Illuminate\Support\Facades\View;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $site_settings = Setting::get();

        foreach ($site_settings as $setting) {
            $settings[$setting->name] = $setting->value;
        }

        View::share(['settings' => $settings]);
    }

    /** Index */
    public function index()
    {
        $searchquery = request()->post('q');

        $apps = Application::orderBy('id', 'desc')->where('title', 'like', "%$searchquery%")->limit(50)->get();

        $categories = Category::orderBy('id', 'desc')->where('title', 'like', "%$searchquery%")->limit(50)->get();

        $platforms = Platform::orderBy('id', 'desc')->where('title', 'like', "%$searchquery%")->limit(50)->get();

        $topics = Topic::orderBy('id', 'desc')->where('title', 'like', "%$searchquery%")->limit(50)->get();

        $news = News::orderBy('id', 'desc')->where('title', 'like', "%$searchquery%")->limit(50)->get();

        $pages = Page::orderBy('id', 'desc')->where('title', 'like', "%$searchquery%")->limit(50)->get();

        return View::make('adminlte::search.index')->with('apps', $apps)->with('categories', $categories)->with('platforms', $platforms)->with('topics', $topics)->with('news', $news)->with('pages', $pages);
    }

    /** Create */
    public function create()
    {
        //
    }

    /** Edit */
    public function edit($id)
    {
        //
    }

    /** Update */
    public function update(Request $request, $id)
    {
        //
    }

    /** Store */
    public function store(Request $request)
    {
        //
    }

    /** Destroy */
    public function destroy($id)
    {
        //
    }

}
