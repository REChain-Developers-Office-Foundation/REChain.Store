<?php
namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Redirect;

class PageController extends Controller
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
        View::share(['settings' => $settings]);
    }

    /** Index */
    public function index(Request $request)
    {

        if ($request->has('sort')) {
            // List of pages
            $posts = Page::orderBy('sort', 'ASC')->get();

            $id = $request->input('id');
            $sorting = $request->input('sort');

            // Update sort order
            foreach ($posts as $item) {
                Page::where('id', '=', $id)->update(array(
                    'sort' => $sorting,
                ));
            }

            // Clear cache
            Cache::flush();

            return \Response::json('success', 200);
        }

        // List of pages
        $rows = Page::orderBy('sort', 'ASC')->get();

        // Return view
        return view('adminlte::pages.index')->with('rows', $rows);
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
        // Check if slug exists
        $slug_check = Page::where('slug', $request->get('slug'))->first();

        // Return error message if slug is in use
        if ($slug_check != null) {
            return Redirect::back()->withErrors(__('admin.slug_in_use'));
        }

        $this->validate($request, [
            'title' => 'required|max:255',
            'slug' => 'nullable|max:255',
            'content' => 'required',
            'custom_title' => 'nullable|max:255',
            'custom_description' => 'nullable|max:255',
            'custom_h1' => 'nullable|max:255',
            'page_views' => 'required|numeric',
        ]);

        $row = new Page;
        $row->slug = $request->get('slug');
        $row->title = $request->get('title');
        $row->content = $request->get('content');
        $row->custom_title = $request->get('custom_title');
        $row->custom_description = $request->get('custom_description');
        $row->custom_h1 = $request->get('custom_h1');
        $row->page_views = $request->get('page_views');
        $row->footer = $request->get('footer') ? 1 : 0;

        // Retrieve last item in sort order and add +1
        $row->sort = Page::max('sort') + 1;

        $row->save();

        if ($request->get('slug') == null) {
            $row->slug = null;
            $row->update(['title' => $row->title]);
        }

        // Clear cache
        Cache::flush();

        // Redirect back
        return redirect()->route('pages.edit', $row->id)->with('success', __('admin.content_added'));
    }

    /** Edit */
    public function edit(Request $request, $id)
    {
        // Delete Translation
        if ($request->has('delete')) {

            $row = DB::table('page_translations')->where('page_id', $id)->where('lang_id', $request->get('lang'))->first();

            // Return 404 page if translation not found
            if ($row == null) {
                abort(404);
            }

            DB::table('page_translations')->where('page_id', $id)->where('lang_id', $request->get('lang'))->delete();

            // Clear cache
            Cache::flush();

            return redirect()->back()->with('success', __('admin.content_deleted'));
        }

        // Retrieve details
        $row = Page::find($id);

        // Return 404 page if page not found
        if ($row == null) {
            abort(404);
        }

        $languages = DB::table('translations')->where('id', '!=', $this->language_id)->orderBy('sort', 'ASC')->get();

        $page_translations = DB::table('page_translations')->where('page_id', $id)->get();

        $title = [];
        $content = [];

        foreach ($page_translations as $translation) {
            $title[$translation->lang_id] = $translation->title;
            $content[$translation->lang_id] = $translation->content;
        }

        // Return view
        return view('adminlte::pages.edit', compact('row', 'id', 'languages', 'title', 'content'));
    }

    /** Update */
    public function update(Request $request, $id)
    {
        // Check if slug exists
        $slug_check = Page::where('slug', $request->get('slug'))->where('id', '!=', $id)->first();

        // Return error message if slug is in use
        if ($slug_check != null) {
            return Redirect::back()->withErrors(__('admin.slug_in_use'));
        }

        $this->validate($request, [
            'title' => 'required|max:255',
            'slug' => 'nullable|max:255',
            'content' => 'required',
            'custom_title' => 'nullable|max:255',
            'custom_description' => 'nullable|max:255',
            'custom_h1' => 'nullable|max:255',
            'page_views' => 'required|numeric',
        ]);

        if ($request->get('titles') != null || $request->get('val') != null) {
            // Check if title translation added
            translation_check($request->get('titles'), 'title', 'page_id', 'page_translations', $id);
            translation_check($request->get('val'), 'content', 'page_id', 'page_translations', $id);
        }

        // Retrieve details
        $row = Page::find($id);
        $row->slug = $request->get('slug');
        $row->title = $request->get('title');
        $row->content = $request->get('content');
        $row->custom_title = $request->get('custom_title');
        $row->custom_description = $request->get('custom_description');
        $row->custom_h1 = $request->get('custom_h1');
        $row->page_views = $request->get('page_views');
        $row->footer = $request->get('footer') ? 1 : 0;

        if ($request->get('slug') == null) {
            $row->slug = null;
            $row->update(['title' => $row->title]);
        }

        $row->save();

        // Clear cache
        Cache::flush();

        // Redirect back
        return redirect()->route('pages.edit', $row->id)->with('success', __('admin.content_updated'));
    }

    /** Destroy */
    public function destroy($id)
    {
        // Retrieve details
        $row = Page::find($id);

        $row->delete();

        // Clear cache
        Cache::flush();

        // Redirect back
        return redirect()->back()->with('success', __('admin.content_deleted'));
    }

}
