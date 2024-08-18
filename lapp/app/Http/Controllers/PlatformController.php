<?php
namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageManagerStatic as Image;
use Redirect;

class PlatformController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        // List of platforms
        $rows = Platform::orderBy('sort', 'ASC')->paginate(15);

        // List of icons
        $icons = DB::table('fa_icons')->where('id', '<=', '1396')->orderBy('title', 'ASC')->get();

        // Site settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $settings[$setting->name] = $setting->value;
            $setting_name = $setting->name;
            $this->$setting_name = $setting->value;
        }

        $this->platform_base = $settings['platform_base'];

        // Languages
        $this->languages = DB::table('translations')->OrderBy('sort', 'ASC')->get();

        foreach ($this->languages as $language) {
            $language_title[$language->code] = $language->id;
            if ($settings['site_language'] == $language->code) {
                $this->language_id = $language_title[$settings['site_language']];
            }
        }

        foreach ($site_settings as $setting) {
            if ($setting->language == $this->language_id) {
                $settings[$setting->name] = $setting->value;
            }
        }

        // Pass data to views
        View::share(['rows' => $rows, 'icons' => $icons, 'settings' => $settings]);
    }

    /** Index */
    public function index(Request $request)
    {
        // Sort apps
        if ($request->has('sort')) {
            $posts = Platform::orderBy('sort', 'ASC')->get();

            $id = $request->input('id');
            $sorting = $request->input('sort');

            // Update sort order
            foreach ($posts as $item) {
                Platform::where('id', '=', $id)->update(array(
                    'sort' => $sorting,
                ));
            }

            // Clear cache
            Cache::flush();

            return \Response::json('success', 200);
        }

        // Return view
        return view('adminlte::platforms.index');
    }

    /** Sort */
    public function sort(Request $request)
    {
        // List of pages
        $rows = Platform::orderBy('sort', 'ASC')->get();

        // Return view
        return view('adminlte::platforms.sort')->with('rows', $rows);
    }

    /** Create */
    public function create()
    {
        // Return view
        return view('adminlte::platforms.create');
    }

    /** Store */
    public function store(Request $request)
    {
        // Check if slug exists
        $slug_check = Platform::where('slug', $request->get('slug'))->first();

        // Return error message if slug is in use
        if ($slug_check != null) {
            return Redirect::back()->withInput()->withErrors(__('admin.slug_in_use'));
        }

        $this->validate($request, [
            'title' => 'required|max:255',
            'slug' => 'nullable|max:255',
            'custom_title' => 'nullable|max:255',
            'custom_description' => 'nullable|max:255',
            'custom_h1' => 'nullable|max:255',
        ]);

        $platform = new Platform;
        $platform->slug = $request->get('slug');
        $platform->title = $request->get('title');
        $platform->custom_title = $request->get('custom_title');
        $platform->custom_description = $request->get('custom_description');
        $platform->custom_h1 = $request->get('custom_h1');
        $platform->fa_icon = $request->get('fa_icon');
        $platform->footer = $request->get('footer') ? 1 : 0;
        $platform->navbar = $request->get('navbar') ? 1 : 0;
        $platform->right_column = $request->get('right_column') ? 1 : 0;

        // Retrieve last item in sort order and add +1
        $platform->sort = Platform::max('sort') + 1;

        // Check if the picture has been uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $platform->image = image_upload($image, '200', '200', '', $this->image_quality, $this->save_as_webp, 5);
        }

        $platform->save();

        if ($request->get('slug') == null) {
            $platform->slug = null;
            $platform->update([
                'title' => $platform->title,
            ]);
        }

        // Clear cache
        Cache::flush();

        // Redirect back
        return redirect()->route('platforms.edit', $platform->id)->with('success', __('admin.content_added'));
    }

    /** Edit */
    public function edit(Request $request, $id)
    {
        // Delete Translation
        if ($request->has('delete')) {

            $row = DB::table('platform_translations')->where('platform_id', $id)->where('lang_id', $request->get('lang'))->first();

            // Return 404 page if translation not found
            if ($row == null) {
                abort(404);
            }

            DB::table('platform_translations')->where('platform_id', $id)->where('lang_id', $request->get('lang'))->delete();

            // Clear cache
            Cache::flush();

            return redirect()->back()->with('success', __('admin.content_deleted'));
        }
        
         // Delete Image
        if ($request->has('remove_image')) {

        // Retrieve platform details
            $app = Platform::find($id);

            // Return 404 page if platform not found
            if ($app == null) {
                abort(404);
            }
            
        if (!empty($app->image)) {
            image_delete($app->image, 5);
        }
        
        $app->image = null;

        $app->save();

            // Clear cache
            Cache::flush();

            return redirect()->back()->with('success', __('admin.content_deleted'));
        }
        
        // Retrieve details
        $row = Platform::find($id);

        // Return 404 page if platform not found
        if ($row == null) {
            abort(404);
        }

        $languages = DB::table('translations')->where('id', '!=', $this->language_id)->orderBy('sort', 'ASC')->get();

        $platform_translations = DB::table('platform_translations')->where('platform_id', $id)->get();

        $title = [];

        foreach ($platform_translations as $translation) {
            $title[$translation->lang_id] = $translation->title;
        }

        // Return view
        return view('adminlte::platforms.edit', compact('row', 'id', 'languages', 'title'));
    }

    /** Update */
    public function update(Request $request, $id)
    {
        // Check if slug exists
        $slug_check = Platform::where('slug', $request->get('slug'))->where('id', '!=', $id)->first();

        // Return error message if slug is in use
        if ($slug_check != null) {
            return Redirect::back()->withInput()->withErrors(__('admin.slug_in_use'));
        }

        $this->validate($request, [
            'title' => 'required|max:255',
            'slug' => 'nullable|max:255',
            'custom_title' => 'nullable|max:255',
            'custom_description' => 'nullable|max:255',
            'custom_h1' => 'nullable|max:255',
        ]);

        if ($request->get('titles') != null) {
            // Check if title translation added
            translation_check($request->get('titles'), 'title', 'platform_id', 'platform_translations', $id);
        }

        // Retrieve details
        $platform = Platform::find($id);

        $platform->slug = $request->get('slug');
        $platform->title = $request->get('title');
        $platform->custom_title = $request->get('custom_title');
        $platform->custom_description = $request->get('custom_description');
        $platform->custom_h1 = $request->get('custom_h1');
        $platform->fa_icon = $request->get('fa_icon');
        $platform->footer = $request->get('footer') ? 1 : 0;
        $platform->navbar = $request->get('navbar') ? 1 : 0;
        $platform->right_column = $request->get('right_column') ? 1 : 0;

        // Check if the picture has been changed
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $platform->image = image_upload($image, '200', '200', $platform->image, $this->image_quality, $this->save_as_webp, 5);
        }

        if ($request->get('slug') == null) {
            $platform->slug = null;
            $platform->update(['title' => $platform->title]);
        }

        $platform->save();
        $platform->update(['title' => $platform->title]);

        // Clear cache
        Cache::flush();

        // Redirect back
        return redirect()->route('platforms.edit', $platform->id)->with('success', __('admin.content_updated'));
    }

    /** Destroy */
    public function destroy($id)
    {
        // Retrieve details
        $row = Platform::find($id);

        // Check if there are apps under the platform
        $items = count($row->applications->pluck('id'));

        if ($items != '0') {
            return Redirect::back()->withErrors(__('admin.category_platform_delete_error'));
        }

        $row->delete();

        // Clear cache
        Cache::flush();

        // Redirect back
        return redirect()->back()->with('success', __('admin.content_deleted'));
    }

}
