<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageManagerStatic as Image;
use Redirect;

class NewsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        // List of categories
        $categories = Category::orderBy('title', 'ASC')->where('type', '2')->get()->pluck('title', 'id');

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
        View::share(['settings' => $settings, 'categories' => $categories]);
    }

    /** Index */
    public function index()
    {
        // List of news
        $rows = News::orderBy('id', 'DESC')->paginate(10);

        // Return view
        return view('adminlte::news.index', compact('rows'));
    }

    /** Create */
    public function create()
    {
        // Return view
        return view('adminlte::news.create');
    }

    /** Store */
    public function store(Request $request)
    {
        // Check if slug exists
        $slug_check = News::where('slug', $request->get('slug'))->first();

        // Return error message if slug is in use
        if ($slug_check != null) {
            return Redirect::back()->withInput()->withErrors(__('admin.slug_in_use'));
        }

        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required|max:755',
            'custom_title' => 'nullable|max:255',
            'custom_description' => 'nullable|max:255',
            'custom_h1' => 'nullable|max:255',
            'content' => 'required',
            'image' => 'required',
            'page_views' => 'required|numeric|max:2147483647',
        ]);

        $news = new News;
        $news->slug = $request->get('slug');
        $news->title = $request->get('title');
        $news->custom_title = $request->get('custom_title');
        $news->custom_description = $request->get('custom_description');
        $news->custom_h1 = $request->get('custom_h1');
        $news->description = $request->get('description');
        $news->content = $request->get('content');
        $news->page_views = $request->get('page_views');

        // Check if the picture has been uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $news->image = image_upload($image, '880', '514', '', $this->image_quality, $this->save_as_webp, 1);
        }

        $news->save();

        $news->categories()->sync((array) $request->input('categories'));

        if ($request->get('slug') == null) {
            $news->slug = null;
            $news->update([
                'title' => $news->title,
            ]);
        }

        // Clear cache
        Cache::flush();

        // Redirect to news edit page
        return redirect()->route('news.edit', $news->id)
            ->with('success', __('admin.content_added'));
    }

    /** Edit */
    public function edit(Request $request, $id)
    {
        // Delete Translation
        if ($request->has('delete')) {

            $row = DB::table('news_translations')->where('news_id', $id)->where('lang_id', $request->get('lang'))->first();

            // Return 404 page if translation not found
            if ($row == null) {
                abort(404);
            }

            DB::table('news_translations')->where('news_id', $id)->where('lang_id', $request->get('lang'))->delete();

            // Clear cache
            Cache::flush();

            return redirect()->back()->with('success', __('admin.content_deleted'));
        }
        
        // Retrieve news details
        $row = News::find($id);

        // Return 404 page if news not found
        if ($row == null) {
            abort(404);
        }

        $languages = DB::table('translations')->where('id', '!=', $this->language_id)->orderBy('sort', 'ASC')->get();

        $news_translations = DB::table('news_translations')->where('news_id', $id)->get();

        $title = [];
        $description = [];
        $content = [];

        foreach ($news_translations as $translation) {
            $title[$translation->lang_id] = $translation->title;
            $description[$translation->lang_id] = $translation->description;
            $content[$translation->lang_id] = $translation->content;
        }

        // Return view
        return view('adminlte::news.edit', compact('row', 'id', 'languages', 'title', 'description', 'content'));
    }

    /** Update */
    public function update(Request $request, $id)
    {
        // Check if slug exists
        $slug_check = News::where('slug', $request->get('slug'))->where('id', '!=', $id)->first();

        // Return error message if slug is in use
        if ($slug_check != null) {
            return Redirect::back()->withErrors(__('admin.slug_in_use'));
        }

        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required|max:755',
            'custom_title' => 'nullable|max:255',
            'custom_description' => 'nullable|max:255',
            'custom_h1' => 'nullable|max:255',
            'content' => 'required',
            'page_views' => 'required|numeric|max:2147483647',
        ]);

        if ($request->get('titles') != null || $request->get('descriptions') != null || $request->get('val') != null) {
            // Check if title translation added
            translation_check($request->get('titles'), 'title', 'news_id', 'news_translations', $id);
            translation_check($request->get('descriptions'), 'description', 'news_id', 'news_translations', $id);
            translation_check($request->get('val'), 'content', 'news_id', 'news_translations', $id);
        }

        // Retrieve news details
        $news = News::find($id);

        $news->slug = $request->get('slug');
        $news->title = $request->get('title');
        $news->custom_title = $request->get('custom_title');
        $news->custom_description = $request->get('custom_description');
        $news->custom_h1 = $request->get('custom_h1');
        $news->description = $request->get('description');
        $news->content = $request->get('content');
        $news->page_views = $request->get('page_views');
        $news->categories()->sync((array) $request->input('categories'));

        // Check if the picture has been changed
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $news->image = image_upload($image, '880', '514', $news->image, $this->image_quality, $this->save_as_webp, 1);
        }

        if ($request->get('slug') == null) {
            $news->slug = null;
            $news->update(['title' => $news->title]);
        }

        $news->save();
        $news->update(['title' => $news->title]);

        // Clear cache
        Cache::flush();

        // Redirect to news edit page
        return redirect()
            ->route('news.edit', $news->id)
            ->with('success', __('admin.content_updated'));
    }

    /** Destroy */
    public function destroy($id)
    {
        // Retrieve news details
        $news = News::find($id);

        if (!empty($news->image)) {
            image_delete($news->image, 1);
        }

        $news->delete();

        DB::table('news_translations')->where('news_id', $id)->delete();

        // Clear cache
        Cache::flush();
        
        // Delete comments
        DB::table('comments')->where('type', 2)->where('content_id', $id)->delete();

        // Redirect to list of news
        return redirect()
            ->route('news.index')
            ->with('success', __('admin.content_deleted'));
    }

}
