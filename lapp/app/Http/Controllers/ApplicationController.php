<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Category;
use App\Models\Platform;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageManagerStatic as Image;
use Redirect;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // List of categories
        $categories = Category::orderBy('title', 'ASC')->where('type', '1')->get()->pluck('title', 'id');

        // List of Platforms
        $platforms = Platform::orderBy('title', 'ASC')->get()->pluck('title', 'id');

        // Site Settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $settings[$setting->name] = $setting->value;
            $setting_name = $setting->name;
            $this->$setting_name = $setting->value;
        }

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
        View::share(['platforms' => $platforms, 'categories' => $categories, 'settings' => $settings]);
    }

    /** Index */
    public function index(Request $request)
    {
        // Delete Demo Data
        if ($request->has('delete_demo_data')) {

            DB::table('applications')->where('demo', '1')->delete();

            // Clear cache
            Cache::flush();

            return redirect()->route('apps.index')->with('success', __('admin.demo_data_deleted'));
        }

        // List of latest applications
        $rows = Application::orderBy('id', 'desc')->paginate(15);

        // Return view
        return view('adminlte::apps.index', compact('rows'));
    }

    /** Create */
    public function create()
    {
        // Return view
        return view('adminlte::apps.create');
    }

    /** Store */
    public function store(Request $request)
    {
        $app = new Application;
        $ver = new Version;

        $this->validate($request, [
            'title' => 'required|max:255',
            'slug' => 'nullable|max:255',
            'description' => 'required|max:755',
            'custom_title' => 'nullable|max:255',
            'custom_description' => 'nullable|max:255',
            'custom_h1' => 'nullable|max:255',
            'page_views' => 'required|numeric|max:2147483647',
            'up_votes' => 'required|numeric|max:2147483647',
            'down_votes' => 'required|numeric|max:2147483647',
            'counter' => 'required|numeric|max:2147483647',
            'buy_url' => 'nullable|url',
            'type' => 'required',
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['required', 'integer', 'exists:categories,id'],
            'platforms' => ['required', 'array', 'min:1'],
            'platforms.*' => ['required', 'integer', 'exists:platforms,id'],
        ]);

        $slug_check = Application::where('slug', $request->get('slug'))->first();

        if ($slug_check != null) {
            return Redirect::back()->withInput()->withErrors(__('admin.slug_in_use'));
        }

        // Check if the file has been uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ver->url = file_upload($file);
            $request->merge([
                'url' => $ver->url,
            ]);
        }

        // Check if the image has been uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $app->image = image_upload($image, '200', '200', '', $this->image_quality, $this->save_as_webp);
        }

        $app->slug = $request->get('slug');
        $app->title = $request->get('title');
        $app->description = $request->get('description');
        $app->custom_title = $request->get('custom_title');
        $app->custom_description = $request->get('custom_description');
        $app->custom_h1 = $request->get('custom_h1');
        $app->page_views = $request->get('page_views');
        $app->up_votes = $request->get('up_votes');
        $app->down_votes = $request->get('down_votes');
        $app->buy_url = $request->get('buy_url');
        $app->license = $request->get('license');
        $app->developer = $request->get('developer');
        $app->details = $request->get('details');
        $app->type = $request->get('type');
        $app->screenshots = '';
        $app->featured = $request->get('featured') ? 1 : 0;
        $app->pinned = $request->get('pinned') ? 1 : 0;
        $app->editors_choice = $request->get('editors_choice') ? 1 : 0;
        $app->must_have = $request->get('must_have') ? 1 : 0;
        $app->package_name = $request->get('package_name');

        if ($request->get('submission') != null) {
            $submission_id = $request->get('app_id');
            DB::table('submissions')->delete($submission_id);

            // Check if the picture has been uploaded
            if ($request->hasFile('different_image')) {
                $image = $request->file('different_image');
                $app->image = image_upload($image, '200', '200', '', $this->image_quality, $this->save_as_webp);
                image_delete($request->get('image'), 7);

            } else {
                $image = $request->get('image');
                if ($image != null) {
                $app->image = image_upload(file_get_contents(s3_switch($image, 7)), '200', '200', '', $this->image_quality, $this->save_as_webp);
                image_delete($image, 7);
                }
            }

        }

        if ($request->get('app_store') != null) {

            $max_screenshots = $this->screenshot_count;

            $screenshots = $request->get('screenshots');

            if ($max_screenshots != '0') {

                $explode_screenshots = explode(",", $screenshots);
                $total_screenshots = count($explode_screenshots);

                if ($total_screenshots >= $max_screenshots) {
                    $total_screenshots = $max_screenshots;
                }

                $ss_array = array();

                $driver = env('FILESYSTEM_DRIVER');

                for ($x = 0; $x <= $total_screenshots - 1; $x++) {

                    if ($this->save_as_webp == '1') {
                        $new_name = time() . rand(1111111, 9999999) . '.webp';
                        $format = 'webp';
                    } else {
                        $new_name = time() . '.' . $image->getClientOriginalExtension();
                        $format = $image->getClientOriginalExtension();
                    }

                    $location = public_path('screenshots/' . $new_name);

                    if ($driver == 's3') {
                        $imageFile = \Image::make($explode_screenshots[$x])->heighten('400')->stream($format, $this->image_quality);
                        Storage::disk('s3')->put('screenshots/' . $new_name, $imageFile, 'public');
                    } else {
                        Image::make($explode_screenshots[$x])->heighten(400)->save($location);
                    }

                    array_push($ss_array, $new_name);
                }

                $screenshots_list = implode(',', $ss_array);

            } else {
                $screenshots_list = '';
            }

            // Check if the picture has been uploaded
            if ($request->hasFile('different_image')) {
                $image = $request->file('different_image');
                $app->image = image_upload($image, '200', '200', '', $this->image_quality, $this->save_as_webp);

            } else {
                $image = $request->get('image');
                $app->image = image_upload(file_get_contents($image), '200', '200', '', $this->image_quality, $this->save_as_webp);
            }

            $app->screenshots = $screenshots_list;

        }

        $app->save();

        $app->categories()->sync((array) $request->input('categories'));
        $app->platforms()->sync((array) $request->input('platforms'));

        $tags = explode(",", $request->get('tags'));
        $app->tag($tags);

        if ($request->get('slug') == null) {
            $app->slug = null;
            $app->update(['title' => $app->title]);
        }

        $ver->app_id = $app->id;
        if (!$request->hasFile('file')) {
            $ver->url = $request->get('url');
        }
        $ver->version = $request->get('version');
        $ver->file_size = $request->get('file_size');
        $ver->counter = $request->get('counter');
        $ver->save();

        // Update Sitemap Last Update Time
        last_update($this->language_id);

        // Clear cache
        Cache::flush();

        // Redirect to application edit page
        return redirect()->route('apps.edit', $app->id)->with('success', __('admin.content_added'));
    }

    /** Edit */
    public function edit(Request $request, $id)
    {
        // Delete Translation
        if ($request->has('delete')) {

            $row = DB::table('app_translations')->where('app_id', $id)->where('lang_id', $request->get('lang'))->first();

            // Return 404 page if translation not found
            if ($row == null) {
                abort(404);
            }

            DB::table('app_translations')->where('app_id', $id)->where('lang_id', $request->get('lang'))->delete();

            // Clear cache
            Cache::flush();

            return redirect()->back()->with('success', __('admin.content_deleted'));
        }
        
         // Delete Image
        if ($request->has('remove_image')) {

        // Retrieve application details
            $app = Application::find($id);

            // Return 404 page if app not found
            if ($app == null) {
                abort(404);
            }
            
        if (!empty($app->image)) {
            image_delete($app->image);
        }
        
        $app->image = null;

        $app->save();

            // Clear cache
            Cache::flush();

            return redirect()->back()->with('success', __('admin.content_deleted'));
        }

        // Retrieve application details
        $row = Application::find($id);

        if (empty($row->image)) {
            $row->image = 'no_image.png';
        }

        // Return 404 page if application not found
        if ($row == null) {
            abort(404);
        }

        // Tags
        $item_count = count($row->tags);
        $i = 0;
        $tags = '';
        foreach ($row->tags as $key => $value) {
            if (++$i === $item_count) {
                $tags .= $value->name;
            } else {
                $tags .= "$value->name, ";
            }
        }

        $languages = DB::table('translations')->where('id', '!=', $this->language_id)->orderBy('sort', 'ASC')->get();

        $app_translations = DB::table('app_translations')->where('app_id', $id)->get();

        $title = [];
        $description = [];
        $details = [];
        $custom_title_lang = [];
        $custom_description_lang = [];
        $custom_h1_lang = [];

        foreach ($app_translations as $translation) {
            $title[$translation->lang_id] = $translation->title;
            $description[$translation->lang_id] = $translation->description;
            $details[$translation->lang_id] = $translation->details;
            $custom_title_lang[$translation->lang_id] = $translation->custom_title;
            $custom_description_lang[$translation->lang_id] = $translation->custom_description;
            $custom_h1_lang[$translation->lang_id] = $translation->custom_h1;
        }

        // List of versions
        $versions = Version::where('app_id', $id)->orderBy('id', 'DESC')->get();

        // Return view
        return view('adminlte::apps.edit', compact('row', 'id', 'tags', 'languages', 'title', 'description', 'custom_title_lang', 'custom_description_lang', 'custom_h1_lang', 'details', 'versions'));
    }

    /** Update */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'slug' => 'nullable|max:255',
            'description' => 'required|max:755',
            'custom_title' => 'nullable|max:255',
            'custom_description' => 'nullable|max:255',
            'custom_h1' => 'nullable|max:255',
            'page_views' => 'required|numeric|max:2147483647',
            'up_votes' => 'required|numeric|max:2147483647',
            'down_votes' => 'required|numeric|max:2147483647',
            'buy_url' => 'nullable|url',
            'type' => 'required',
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['required', 'integer', 'exists:categories,id'],
            'platforms' => ['required', 'array', 'min:1'],
            'platforms.*' => ['required', 'integer', 'exists:platforms,id'],
        ]);

        if ($request->get('descriptions') != null || $request->get('titles') != null || $request->get('val') != null) {
            // Check if the translation has been added
            translation_check($request->get('titles'), 'title', 'app_id', 'app_translations', $id);
            translation_check($request->get('descriptions'), 'description', 'app_id', 'app_translations', $id);
            translation_check($request->get('val'), 'details', 'app_id', 'app_translations', $id);
            translation_check($request->get('custom_titles'), 'custom_title', 'app_id', 'app_translations', $id);
            translation_check($request->get('custom_descriptions'), 'custom_description', 'app_id', 'app_translations', $id);
            translation_check($request->get('custom_h1s'), 'custom_h1', 'app_id', 'app_translations', $id);
        }

        $slug_check = Application::where('slug', $request->get('slug'))->where('id', '!=', $id)->first();

        if ($slug_check != null) {
            return Redirect::back()->withErrors(__('admin.slug_in_use'));
        }

        // Retrieve application details
        $app = Application::find($id);

        $app->slug = $request->get('slug');
        $app->title = $request->get('title');
        $app->description = $request->get('description');
        $app->custom_title = $request->get('custom_title');
        $app->custom_description = $request->get('custom_description');
        $app->custom_h1 = $request->get('custom_h1');
        $app->page_views = $request->get('page_views');
        $app->up_votes = $request->get('up_votes');
        $app->down_votes = $request->get('down_votes');
        $app->buy_url = $request->get('buy_url');
        $app->license = $request->get('license');
        $app->developer = $request->get('developer');
        $app->details = $request->get('details');
        $app->type = $request->get('type');
        $app->package_name = $request->get('package_name');
        $app->categories()->sync((array) $request->input('categories'));
        $app->platforms()->sync((array) $request->input('platforms'));
        $app->untag();

        $tags = explode(",", $request->get('tags'));
        $app->tag($tags);

        $app->featured = $request->get('featured') ? 1 : 0;
        $app->pinned = $request->get('pinned') ? 1 : 0;
        $app->editors_choice = $request->get('editors_choice') ? 1 : 0;
        $app->must_have = $request->get('must_have') ? 1 : 0;

        // Check if the file has been uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $original_name = $file->getClientOriginalName();
            $request->file->move(public_path('/files'), $original_name);
            $app->url = asset('/files') . '/' . $original_name;
        }

        // Check if the picture has been changed
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $app->image = image_upload($image, '200', '200', $app->image, $this->image_quality, $this->save_as_webp);
        }

        if ($request->get('slug') == null) {
            $app->slug = null;
            $app->update(['title' => $app->title]);
        }

        $app->save();

        // Clear cache
        Cache::flush();

        // Update Sitemap Last Update Time
        last_update($this->language_id);

        // Redirect to application edit page
        return redirect()->route('apps.edit', $app->id)->with('success', __('admin.content_updated'));
    }

    /** Bulk Destory */
    public function bulk_destory(Request $request)
    {

        $submissions = $request->input('app_list');

        if ($submissions == null) {
            return back()->withInput()->withErrors(__('admin.no_record_selected'));
        }

        $submissions = explode(',', $submissions); //split string into array seperated by ', '
        foreach ($submissions as $submission) {
            $app_id = explode('app_', $submission);

            // Retrieve application details
            $app = Application::find($app_id[1]);

            if (!empty($app->image)) {
                image_delete($app->image);
            }

            $app->delete();

            // Delete version records
            DB::table('versions')->where('app_id', $app_id[1])->delete();

            // Delete app translations
            DB::table('app_translations')->where('app_id', $app_id[1])->delete();

            // Delete category records
            DB::table('application_category')->where('application_id', $app_id[1])->delete();

            // Delete platform records
            DB::table('application_platform')->where('application_id', $app_id[1])->delete();

        }

        // Clear cache
        Cache::flush();

        // Redirect back
        return redirect()->route('apps.index')->with('success', __('admin.content_deleted'));
    }

    /** Destroy */
    public function destroy($id)
    {
        // Retrieve application details
        $app = Application::find($id);

        if (!empty($app->image)) {
            image_delete($app->image);
        }

        $app->delete();

        // Delete version records
        DB::table('versions')->where('app_id', $id)->delete();

        // Delete app translations
        DB::table('app_translations')->where('app_id', $id)->delete();

        // Delete category records
        DB::table('application_category')->where('application_id', $id)->delete();

        // Delete platform records
        DB::table('application_platform')->where('application_id', $id)->delete();

        // Delete comments
        DB::table('comments')->where('type', 1)->where('content_id', $id)->delete();

        // Clear cache
        Cache::flush();

        // Update Sitemap Last Update Time
        last_update($this->language_id);

        // Redirect back
        return redirect()->route('apps.index')->with('success', __('admin.content_deleted'));
    }

}
