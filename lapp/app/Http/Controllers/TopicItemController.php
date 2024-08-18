<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\TopicItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class TopicItemController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        // List of applications
        $apps = DB::table('applications')->orderBy('title', 'asc')->get();

        // Pass data to views
        View::share(['apps' => $apps]);
    }

    /** Index */
    public function index()
    {
        //
    }

    /** Show */
    public function show($id)
    {
        // Retrieve application details
        $app = Topic::find($id);

        // Return 404 page if application not found
        if ($app == null) {
            abort(404);
        }

        // Check if apps exist under this category
        $topic_items = TopicItem::where('list_id', "$id")->first();

        $item_list = $topic_items['app_list'];
        $item_list = explode(',', $item_list);
        $item_list = array_filter($item_list);

        $id = $app->id;

        // Return view
        return view('adminlte::topics.list', compact('item_list', 'id'));

    }

    /** Create */
    public function create()
    {
        // Return view
        return view('adminlte::categories.create');
    }

    /** Store */
    public function store(Request $request)
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

        $app_list = array();
        foreach ($request->except(array('_token', '_method')) as $key => $value) {
            $app_list[] = $value;
        }

        $app_list = implode(',', $app_list);
        $app_list = trim($app_list, ',');

        TopicItem::where('list_id', '=', $id)->update(['app_list' => $app_list]);

        // Clear cache
        Cache::flush();

        // Redirect to item edit page
        return back()->with('success', __('admin.content_updated'));

    }

    /** Destroy */
    public function destroy($id)
    {
        //
    }

}
