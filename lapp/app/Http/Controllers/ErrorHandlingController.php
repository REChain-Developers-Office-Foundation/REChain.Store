<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ErrorHandlingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Site Settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $setting_name = $setting->name;
            $this->settings[$setting->name] = $setting->value;
            $settings[$setting->name] = $setting->value;
        }

        $this->error_pages = array(401, 403, 404, 419, 429, 500, 503);

        // Pass data to views
        View::share(['settings' => $settings]);
    }

    /** Index */
    public function index(Request $request)
    {
        // Return view
        return view('adminlte::error_handling.index')->with('error_pages', $this->error_pages);
    }

    /** Create */
    public function create()
    {
        // Return view
        return view('adminlte::pages.create');
    }

    /** Store */
    public function store(Request $request)
    {
        //
    }

    /** Edit */
    public function edit($id)
    {
if(!in_array($id, $this->error_pages))
{
            abort(404);
}

    // Check if file exists
    $path = resource_path('views/errors/'.$id.'.blade.php');
    
    if (File::exists($path)) {
        $code = File::get($path);
    }

        // Return view
        return view('adminlte::error_handling.edit', compact('code', 'id'));
    }

    /** Update */
    public function update(Request $request, $id)
    {
        if(!in_array($id, $this->error_pages))
{
            abort(404);
}

        $this->validate($request, [
            'code' => 'required',
        ]);
        
        $path = resource_path('views/errors/'.$id.'.blade.php');
        
        File::put($path, $request->get('code'));

        // Redirect back
        return redirect()->route('error_handling.edit', $id)->with('success', __('admin.content_updated'));
    }

    /** Destroy */
    public function destroy($id)
    {
        //
    }

}
