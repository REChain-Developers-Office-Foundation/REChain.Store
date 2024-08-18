<?php
namespace App\Http\Controllers;

use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Redirect;

class VersionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Site Settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $setting_name = $setting->name;
            $this->settings[$setting->name] = $setting->value;
        }

        // Pass data to views
        View::share(['settings' => $this->settings]);
    }

    /** Show */
    public function show($id, Request $request)
    {
        // List of versions
        $rows = Version::where('app_id', $id)->orderBy('id', 'DESC')->get();

        // Return view
        return view('adminlte::versions.index')->with('rows', $rows)->with('id', $id);
    }

    /** Index */
    public function index()
    {
        //
    }

    /** Create */
    public function create(Request $request)
    {
        if ($request->input('app') == null) {
            abort(404);
        }

        // Return view
        return view('adminlte::versions.create');
    }

    /** Store */
    public function store(Request $request)
    {
        $this->validate($request, [
            'version' => 'nullable|max:255',
            'file_size' => 'nullable|max:255',
            'counter' => 'required|numeric|max:2147483647',
            'url' => 'nullable|max:2083',
        ]);

        $row = new Version;
        $row->app_id = $request->get('app_id');
        $row->version = $request->get('version');
        $row->file_size = $request->get('file_size');
        $row->counter = $request->get('counter');

        // Check if the file has been uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $row->url = file_upload($file);
        } else {
            $row->url = $request->get('url');
        }

        $row->save();
        
        // Update app update time
        DB::update("update applications set updated_at = NOW() WHERE id = $row->app_id");

        // Clear cache
        Cache::flush();

        // Redirect back
        return redirect()->route('versions.edit', $row->id)->with('success', __('admin.content_added'));
    }

    /** Edit */
    public function edit($id)
    {
        // Retrieve details
        $row = Version::find($id);

        // Return 404 page if page not found
        if ($row == null) {
            abort(404);
        }

        // Return view
        return view('adminlte::versions.edit', compact('row', 'id'));
    }

    /** Update */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'version' => 'nullable|max:255',
            'file_size' => 'nullable|max:255',
            'counter' => 'required|numeric|max:2147483647',
            'url' => 'nullable|max:2083',
        ]);

        // Retrieve details
        $row = Version::find($id);
        $row->version = $request->get('version');
        $row->file_size = $request->get('file_size');
        $row->counter = $request->get('counter');

        // Check if the file has been uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $row->url = file_upload($file);
        } else {
            $row->url = $request->get('url');
        }

        $row->save();
        
        // Update app update time
        DB::update("update applications set updated_at = NOW() WHERE id = $row->app_id");

        // Clear cache
        Cache::flush();

        // Redirect back
        return redirect()->route('versions.edit', $row->id)->with('success', __('admin.content_updated'));
    }

    /** Destroy */
    public function destroy($id)
    {
        // Retrieve details
        $row = Version::find($id);

        $row->delete();

        // Clear cache
        Cache::flush();

        // Redirect back
        return redirect()->back()->with('success', __('admin.content_deleted'));
    }

}
