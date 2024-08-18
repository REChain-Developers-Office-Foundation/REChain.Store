<?php
namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageManagerStatic as Image;
use Redirect;

class TopicController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        // List of Topics
        $rows = Topic::orderBy('id', 'DESC')->paginate(15);

        // List of applications
        $apps = Application::orderBy('title', 'ASC')->get()->pluck('title', 'id');

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
        View::share(['rows' => $rows, 'apps' => $apps, 'settings' => $settings]);
    }

    /** Index */
    public function index()
    {
        // Return view
        return view('adminlte::topics.index');
    }

    /** Create */
    public function create()
    {
        // Return view
        return view('adminlte::topics.create');
    }

    /** Store */
    public function store(Request $request)
    {
        // Check if slug exists
        $slug_check = Topic::where('slug', $request->get('slug'))->first();

        // Return error message if slug is in use
        if ($slug_check != null) {
            return Redirect::back()->withInput()->withErrors(__('admin.slug_in_use'));
        }

        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'nullable|max:755',
            'custom_title' => 'nullable|max:255',
            'custom_description' => 'nullable|max:255',
            'custom_h1' => 'nullable|max:255',
            'image' => 'required',
            'page_views' => 'required|numeric|max:2147483647',
        ]);

        $topic = new Topic;

        // Check if the picture has been uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $topic->image = image_upload($image, '880', '514', '', $this->image_quality, $this->save_as_webp, 2);
        }

        $topic->title = $request->get('title');
        $topic->custom_title = $request->get('custom_title');
        $topic->custom_description = $request->get('custom_description');
        $topic->custom_h1 = $request->get('custom_h1');
        $topic->slug = $request->get('slug');
        $topic->description = $request->get('description');
        $topic->page_views = $request->get('page_views');

        $topic->save();

        if ($request->get('slug') == null) {
            $topic->slug = null;
            $topic->update([
                'title' => $topic->title,
            ]);
        }

        DB::table('topic_items')->insert(['list_id' => $topic->id]);

        // Clear cache
        Cache::flush();

        // Redirect to topic edit page
        return redirect()->route('topics.edit', $topic->id)
            ->with('success', __('admin.content_added') . ". <a href=\"".asset(env('ADMIN_URL'))."/topic/$topic->id\">" . __('admin.view_apps_under_topic') . "</a>");
    }

    /** Edit */
    public function edit(Request $request, $id)
    {
        // Delete Translation
        if ($request->has('delete')) {

            $row = DB::table('topic_translations')->where('topic_id', $id)->where('lang_id', $request->get('lang'))->first();

            // Return 404 page if translation not found
            if ($row == null) {
                abort(404);
            }

            DB::table('topic_translations')->where('topic_id', $id)->where('lang_id', $request->get('lang'))->delete();

            // Clear cache
            Cache::flush();

            return redirect()->back()->with('success', __('admin.content_deleted'));
        }
        
        // Retrieve topic details
        $row = Topic::find($id);

        // Return 404 page if topic not found
        if ($row == null) {
            abort(404);
        }

        $languages = DB::table('translations')->where('id', '!=', $this->language_id)->orderBy('sort', 'ASC')->get();

        $topic_translations = DB::table('topic_translations')->where('topic_id', $id)->get();

        $title = [];
        $description = [];

        foreach ($topic_translations as $translation) {
            $title[$translation->lang_id] = $translation->title;
            $description[$translation->lang_id] = $translation->description;
        }

        // Return view
        return view('adminlte::topics.edit', compact('row', 'id', 'languages', 'title', 'description'));
    }

    /** Update */
    public function update(Request $request, $id)
    {
        // Check if slug exists
        $slug_check = Topic::where('slug', $request->get('slug'))->where('id', '!=', $id)->first();

        // Return error message if slug is in use
        if ($slug_check != null) {
            return Redirect::back()->withErrors(__('admin.slug_in_use'));
        }

        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'nullable|max:755',
            'custom_title' => 'nullable|max:255',
            'custom_description' => 'nullable|max:255',
            'custom_h1' => 'nullable|max:255',
            'page_views' => 'required|numeric|max:2147483647',
        ]);

        if ($request->get('titles') != null || $request->get('descriptions') != null) {
            // Check if title translation added
            translation_check($request->get('titles'), 'title', 'topic_id', 'topic_translations', $id);
            translation_check($request->get('descriptions'), 'description', 'topic_id', 'topic_translations', $id);
        }

        // Retrieve topic details
        $topic = Topic::find($id);

        $topic->slug = $request->get('slug');
        $topic->title = $request->get('title');
        $topic->custom_title = $request->get('custom_title');
        $topic->custom_description = $request->get('custom_description');
        $topic->custom_h1 = $request->get('custom_h1');
        $topic->description = $request->get('description');
        $topic->page_views = $request->get('page_views');

        // Check if the picture has been changed
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $topic->image = image_upload($image, '880', '514', $topic->image, $this->image_quality, $this->save_as_webp, 2);
        }

        if ($request->get('slug') == null) {
            $topic->slug = null;
            $topic->update(['title' => $topic->title]);
        }

        $topic->save();
        $topic->update(['title' => $topic->title]);

        // Clear cache
        Cache::flush();

        // Redirect to topic edit page
        return redirect()
            ->route('topics.edit', $topic->id)
            ->with('success', __('admin.content_updated') . ". <a href=\"".asset(env('ADMIN_URL'))."/topic/$topic->id\">" . __('admin.view_apps_under_topic') . "</a>");
    }

    /** Details */
    public function details(Request $request)
    {
        $request_type = $request->get('request');

        if ($request_type == '1') {
            $search_term = $request->get('search');

            // List of applications
            $apps = DB::table('applications')->orderBy('id', 'desc')->where('title', 'like', "%{$search_term}%")->get();

            foreach ($apps as $row) {
                $response[] = array("value" => $row->id, "label" => $row->title);
            }

            echo json_encode($response);
        }

        if ($request_type == '2') {
            $app_id = $request->get('app_id');

            // Get app details
            $app = DB::table('applications')->where('id', $app_id)->first();

            $app_image = s3_switch($app->image ?? 'no_image.png');

            $response[] = array("id" => $app->id, "name" => $app->title, "image" => $app_image);

            echo json_encode($response);
        }

    }

    /** Destroy */
    public function destroy($id)
    {
        // Retrieve topic details
        $topic = Topic::find($id);

        if (!empty($topic->image)) {
            image_delete($topic->image, 2);
        }

        $topic->delete();

        DB::delete('delete from topic_items where list_id = ?', [$id]);

        DB::table('topic_translations')->where('topic_id', $id)->delete();

        // Clear cache
        Cache::flush();

        // Redirect to list of topics
        return redirect()
            ->route('topics.index')
            ->with('success', __('admin.content_deleted'));
    }

}
