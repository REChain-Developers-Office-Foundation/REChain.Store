<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageManagerStatic as Image;
use Redirect;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        // List of categories
        $rows = Category::where('type', '1')->orderBy('sort', 'ASC')->paginate(15);

        // List of icons
        $icons = DB::table('fa_icons')->where('id', '<=', '1396')->orderBy('title', 'ASC')->get();

        // Site settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $settings[$setting->name] = $setting->value;
            $setting_name = $setting->name;
            $this->$setting_name = $setting->value;
        }

        $this->category_base = $settings['category_base'];

        // Schema.org Application Categories
        $application_categories = array(
            '1' => "GameApplication",
            '2' => 'SocialNetworkingApplication',
            '3' => 'TravelApplication',
            "4" => 'ShoppingApplication',
            "5" => 'SportsApplication',
            "6" => 'LifestyleApplication',
            "7" => 'BusinessApplication',
            "8" => 'DesignApplication',
            "9" => 'DeveloperApplication',
            "10" => 'DriverApplication',
            "11" => 'EducationalApplication',
            "12" => 'HealthApplication',
            "13" => 'FinanceApplication',
            "14" => 'SecurityApplication',
            "15" => 'BrowserApplication',
            "16" => 'CommunicationApplication',
            "17" => 'DesktopEnhancementApplication',
            "18" => 'EntertainmentApplication',
            "19" => 'MultimediaApplication',
            "20" => 'HomeApplication',
            "21" => 'UtilitiesApplication',
            "22" => 'ReferenceApplication',
        );

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
        View::share(['rows' => $rows, 'icons' => $icons, 'settings' => $settings, 'application_categories' => $application_categories]);
    }

    /** Index */
    public function index(Request $request)
    {
        // Sort apps
        if ($request->has('sort')) {
            $posts = Category::where('type', '1')->orderBy('sort', 'ASC')->get();

            $id = $request->input('id');
            $sorting = $request->input('sort');

            // Update sort order
            foreach ($posts as $item) {
                Category::where('id', '=', $id)->update(array(
                    'sort' => $sorting,
                ));
            }

            // Clear cache
            Cache::flush();

            return \Response::json('success', 200);
        }

        // Return view
        return view('adminlte::categories.index');
    }

    /** Sort */
    public function sort(Request $request)
    {
        // List of pages
        $rows = Category::where('type', '1')->orderBy('sort', 'ASC')->get();

        // Return view
        return view('adminlte::categories.sort')->with('rows', $rows);
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
        // Check if slug exists
        $slug_check = Category::where('type', '1')->where('slug', $request->get('slug'))->first();

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

        $category = new Category;
        $category->slug = $request->get('slug');
        $category->title = $request->get('title');
        $category->custom_title = $request->get('custom_title');
        $category->custom_description = $request->get('custom_description');
        $category->custom_h1 = $request->get('custom_h1');
        $category->fa_icon = $request->get('fa_icon');
        $category->home_page = $request->get('home_page') ? 1 : 0;
        $category->navbar = $request->get('navbar') ? 1 : 0;
        $category->footer = $request->get('footer') ? 1 : 0;
        $category->right_column = $request->get('right_column') ? 1 : 0;
        $category->type = 1;

        // Retrieve last item in sort order and add +1
        $category->sort = Category::where('type', '1')->max('sort') + 1;

        // Check if the picture has been uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $category->image = image_upload($image, '200', '200', '', $this->image_quality, $this->save_as_webp, 4);
        }

        $category->save();

        if ($request->get('slug') == null) {
            $category->slug = null;
            $category->update([
                'title' => $category->title,
            ]);
        }

        // Clear cache
        Cache::flush();

        // Redirect back
        return redirect()->route('categories.edit', $category->id)->with('success', __('admin.content_added'));
    }

    /** Edit */
    public function edit(Request $request, $id)
    {
        // Delete Translation
        if ($request->has('delete')) {

            $row = DB::table('category_translations')->where('cat_id', $id)->where('lang_id', $request->get('lang'))->first();

            // Return 404 page if translation not found
            if ($row == null) {
                abort(404);
            }

            DB::table('category_translations')->where('cat_id', $id)->where('lang_id', $request->get('lang'))->delete();

            // Clear cache
            Cache::flush();

            return redirect()->back()->with('success', __('admin.content_deleted'));
        }
        
         // Delete Image
        if ($request->has('remove_image')) {

        // Retrieve category details
            $app = Category::find($id);

            // Return 404 page if category not found
            if ($app == null) {
                abort(404);
            }
            
        if (!empty($app->image)) {
            image_delete($app->image, 4);
        }
        
        $app->image = null;

        $app->save();

            // Clear cache
            Cache::flush();

            return redirect()->back()->with('success', __('admin.content_deleted'));
        }

        // Retrieve details
        $row = Category::where('type', '1')->find($id);

        // Return 404 page if category not found
        if ($row == null) {
            abort(404);
        }

        $languages = DB::table('translations')->where('id', '!=', $this->language_id)->orderBy('sort', 'ASC')->get();

        $category_translations = DB::table('category_translations')->where('cat_id', $id)->get();

        $title = [];

        foreach ($category_translations as $translation) {
            $title[$translation->lang_id] = $translation->title;
        }

        // Return view
        return view('adminlte::categories.edit', compact('row', 'id', 'languages', 'title'));
    }

    /** Update */
    public function update(Request $request, $id)
    {
        // Check if slug exists
        $slug_check = Category::where('type', '1')->where('slug', $request->get('slug'))->where('id', '!=', $id)->first();

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
            translation_check($request->get('titles'), 'title', 'cat_id', 'category_translations', $id);
        }

        // Retrieve details
        $category = Category::find($id);

        $category->slug = $request->get('slug');
        $category->title = $request->get('title');
        $category->custom_title = $request->get('custom_title');
        $category->custom_description = $request->get('custom_description');
        $category->custom_h1 = $request->get('custom_h1');
        $category->fa_icon = $request->get('fa_icon');
        $category->home_page = $request->get('home_page') ? 1 : 0;
        $category->navbar = $request->get('navbar') ? 1 : 0;
        $category->footer = $request->get('footer') ? 1 : 0;
        $category->right_column = $request->get('right_column') ? 1 : 0;

        // Check if the picture has been changed
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $category->image = image_upload($image, '200', '200', $category->image, $this->image_quality, $this->save_as_webp, 4);
        }

        if ($request->get('slug') == null) {
            $category->slug = null;
            $category->update(['title' => $category->title]);
        }

        $category->save();
        $category->update(['title' => $category->title]);

        // Clear cache
        Cache::flush();

        // Redirect back
        return redirect()->route('categories.edit', $category->id)->with('success', __('admin.content_updated'));
    }

    /** Destroy */
    public function destroy($id)
    {
        // Retrieve details
        $row = Category::find($id);

        // Check if there are apps under the category
        $items = count($row->applications->pluck('id'));

        if ($items != '0') {
            return Redirect::back()->withErrors(__('admin.category_platform_delete_error'));
        }

        $row->delete();

        DB::table('category_translations')->where('cat_id', $id)->delete();

        // Clear cache
        Cache::flush();

        // Redirect back
        return redirect()->back()->with('success', __('admin.content_deleted'));
    }

}
