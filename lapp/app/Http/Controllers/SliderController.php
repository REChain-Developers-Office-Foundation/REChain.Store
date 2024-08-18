<?php
namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageManagerStatic as Image;

class SliderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        // List of sliders
        $rows = Slider::orderBy('id', 'DESC')->paginate(10);

        // List of applications
        $apps = Application::orderBy('title', 'ASC')->get()->pluck('title', 'id');

        // Site Settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $setting_name = $setting->name;
            $this->$setting_name = $setting->value;
            $settings[$setting->name] = $setting->value;
        }

        // Languages
        $this->languages = DB::table('translations')->OrderBy('sort', 'ASC')->get();

        foreach ($this->languages as $language) {
            $language_title[$language->code] = $language->id;
            if ($settings['site_language'] == $language->code) {
                $this->language_id = $language_title[$settings['site_language']];
            }
        }

        // Pass data to views
        View::share(['rows' => $rows, 'apps' => $apps, 'settings' => $settings]);
    }

    /** Index */
    public function index(Request $request)
    {
        // Delete Demo Data
        if ($request->has('status')) {
            $status = request()->query('status');
            $id = request()->query('id');

            $slider = Slider::find($id);

            if ($status === '0' or $status === '1') {
                $slider->update(['active' => $status]);
            }

            // Clear cache
            Cache::flush();

            // Redirect back
            return redirect()->route('sliders.index')->with('success', __('admin.content_updated'));
        }

        // Return view
        return view('adminlte::sliders.index');
    }

    /** Create */
    public function create()
    {
        // Return view
        return view('adminlte::sliders.create');
    }

    /** Store */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'link' => 'required',
            'image' => 'required',
        ]);

        $slider = new Slider;

        // Check if the image has been uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $slider->image = image_upload($image, '850', '410', '', $this->image_quality, $this->save_as_webp, 3);
        }

        $slider->title = $request->get('title');
        $slider->link = $request->get('link');

        if ($request->get('active') == null) {
            $slider->active = '0';
        } else {
            $slider->active = '1';
        }

        $slider->save();

        // Clear cache
        Cache::flush();

        // Redirect to slider edit page
        return redirect()->route('sliders.edit', $slider->id)->with('success', __('admin.content_added'));
    }

    /** Edit */
    public function edit(Request $request, $id)
    {
        // Delete Translation
        if ($request->has('delete')) {

            $row = DB::table('slider_translations')->where('slider_id', $id)->where('lang_id', $request->get('lang'))->first();

            // Return 404 page if translation not found
            if ($row == null) {
                abort(404);
            }

            DB::table('slider_translations')->where('slider_id', $id)->where('lang_id', $request->get('lang'))->delete();

            // Clear cache
            Cache::flush();

            return redirect()->back()->with('success', __('admin.content_deleted'));
        }
        
        // Retrieve slider details
        $row = Slider::find($id);

        // Return 404 page if slider not found
        if ($row == null) {
            abort(404);
        }
        
        $languages = DB::table('translations')->where('id', '!=', $this->language_id)->orderBy('sort', 'ASC')->get();
        
        $slider_translations = DB::table('slider_translations')->where('slider_id', $id)->get();

        $title = [];

        foreach ($slider_translations as $translation) {
            $title[$translation->lang_id] = $translation->title;
        }

        // Return view
        return view('adminlte::sliders.edit', compact('row', 'id', 'languages', 'title'));
    }

    /** Update */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'link' => 'required',
        ]);

        if ($request->get('titles') != null) {
            // Check if title translation added
            translation_check($request->get('titles'), 'title', 'slider_id', 'slider_translations', $id);
        }

        // Retrieve slider details
        $slider = Slider::find($id);

        $slider->title = $request->get('title');
        $slider->link = $request->get('link');

        if ($request->get('active') == null) {
            $slider->active = '0';
        } else {
            $slider->active = '1';
        }

        // Check if the picture has been changed
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $slider->image = image_upload($image, '850', '410', $slider->image, $this->image_quality, $this->save_as_webp, 3);
        }

        $slider->save();

        // Clear cache
        Cache::flush();

        // Redirect to slider edit page
        return redirect()->route('sliders.edit', $slider->id)->with('success', __('admin.content_updated'));
    }

    /** Destroy */
    public function destroy($id)
    {
        // Retrieve slider details
        $slider = Slider::find($id);

        if (!empty($slider->image)) {
            image_delete($slider->image, 3);
        }

        $slider->delete();
        
        DB::table('slider_translations')->where('slider_id', $id)->delete();

        // Clear cache
        Cache::flush();

        // Redirect to list of sliders
        return redirect()->route('sliders.index')->with('success', __('admin.content_deleted'));
    }

}
