<?php

namespace App\Http\Controllers;

use App;
use App\Models\Translation;
use DateTime;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class TranslationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        // List of translations
        $rows = Translation::orderBy('sort', 'ASC')->get();

        // List of countries
        $countries = json_decode(file_get_contents(public_path() . "/vendor/countries.json"), true);

        // Pass data to views
        View::share(['rows' => $rows, 'countries' => $countries]);
    }

    /** Index */
    public function index(Request $request)
    {
        if ($request->has('sort')) {
            // List of translations
            $translations = Translation::orderBy('sort', 'ASC')->get();

            $id = $request->input('id');
            $sorting = $request->input('sort');

            // Update sort order
            foreach ($translations as $item) {
                Translation::where('id', '=', $id)->update(array(
                    'sort' => $sorting,
                ));
            }

            // Clear cache
            Cache::flush();

            return \Response::json('success', 200);
        }

        // Return view
        return view('adminlte::translations.index');
    }

    /** Create */
    public function create()
    {
        // Return view
        return view('adminlte::translations.create');
    }

    /** Store */
    public function store(Request $request)
    {
        $this->validate($request, [
            'language' => 'required',
            'code' => 'required|unique:translations,code|max:2',
            'locale_code' => 'required',
            'icon' => 'required',
            'text_direction' => 'required',
        ]);

        $translate = new Translation;
        $translate->language = $request->get('language');
        $translate->code = $request->get('code');
        $translate->locale_code = $request->get('locale_code');
        $translate->icon = $request->get('icon');
        $translate->text_direction = $request->get('text_direction');

        // Retrieve last item in sort order and add +1
        $translate->sort = Translation::max('sort') + 1;

        $translation_folder = app()['path.lang'] . '/' . $translate->code;

        File::makeDirectory($translation_folder);
        File::copy(app()['path.lang'] . '/en/general.php', app()['path.lang'] . '/' . $translate->code . '/general.php');
        File::copy(app()['path.lang'] . '/en/admin.php', app()['path.lang'] . '/' . $translate->code . '/admin.php');

        $translate->save();

        $current_time = \Carbon\Carbon::now()->toDateTimeString();
        $last_mod = gmdate(DateTime::W3C, strtotime($current_time));

        // Insert slug records
        $data = [
            ['name' => 'app_base', 'value' => 'apps', 'language' => $translate->id],
            ['name' => 'news_base', 'value' => 'news', 'language' => $translate->id],
            ['name' => 'category_base', 'value' => 'categories', 'language' => $translate->id],
            ['name' => 'platform_base', 'value' => 'platforms', 'language' => $translate->id],
            ['name' => 'page_base', 'value' => 'pages', 'language' => $translate->id],
            ['name' => 'tag_base', 'value' => 'tags', 'language' => $translate->id],
            ['name' => 'topic_base', 'value' => 'topics', 'language' => $translate->id],
            ['name' => 'read_base', 'value' => 'read', 'language' => $translate->id],
            ['name' => 'contact_slug', 'value' => 'contact', 'language' => $translate->id],
            ['name' => 'site_title', 'value' => 'Site Title', 'language' => $translate->id],
            ['name' => 'site_description', 'value' => 'Site Description', 'language' => $translate->id],
            ['name' => 'home_page_h1', 'value' => 'Welcome', 'language' => $translate->id],
            ['name' => 'last_mod', 'value' => $last_mod, 'language' => $translate->id],
        ];

        DB::table('settings')->insert($data); // Query Builder approach

        // Clear cache
        Cache::flush();

        // Redirect to translation edit page
        return redirect()->route('translations.edit', $translate->id)->with('success', __('admin.content_added'));
    }

    /** Edit */
    public function edit($id)
    {
        // Retrieve translation details
        $translation = Translation::find($id);

        // Return 404 page if translation not found
        if ($translation == null) {
            abort(404);
        }

        $frontend_location = app()['path.lang'] . '/en/general.php';
        $admin_location = app()['path.lang'] . '/en/admin.php';
        $translation_admin_org = include $admin_location;
        $translation_frontend_org = include $frontend_location;

        $frontend_location_target = app()['path.lang'] . '/' . $translation->code . '/general.php';
        $admin_location_target = app()['path.lang'] . '/' . $translation->code . '/admin.php';
        $translation_frontend_target = include $frontend_location_target;
        $translation_admin_target = include $admin_location_target;

        // Return view
        return view('adminlte::translations.edit', compact('translation', 'id', 'translation_admin_org', 'translation_frontend_org', 'translation_frontend_target', 'translation_admin_target'));
    }

    /** Update */
    public function update(Request $request, $id)
    {
        // Retrieve translation details
        $translate = Translation::find($id);

        if ($request->get('translation_type') == 1) {
            $this->validate($request, [
                'language' => 'required',
                'code' => 'required|max:2|unique:translations,code,' . $id,
                'locale_code' => 'required',
                'icon' => 'required',
                'text_direction' => 'required',
            ]);

            $translation_folder = $translate->code;
            $translate->language = $request->get('language');
            $translate->code = $request->get('code');
            $translate->locale_code = $request->get('locale_code');
            $translate->icon = $request->get('icon');
            $translate->text_direction = $request->get('text_direction');

            if ($translate->isDirty('code')) {
                File::moveDirectory(app()['path.lang'] . '/' . $translation_folder, app()['path.lang'] . '/' . $translate->code);
            }

            $translate->save();
        }

        if ($request->get('translation_type') == 2 or $request->get('translation_type') == 3) {

            function varexport($expression, $return = false)
            {
                $export = var_export($expression, true);
                $export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
                $array = preg_split("/\r\n|\n|\r/", $export);
                $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $array);
                $export = join(PHP_EOL, array_filter(["["] + $array));
                $export = "<?php\nreturn " . $export . ";";
                if ((bool) $return) {
                    return $export;
                } else {
                    echo $export;
                }

            }

            $a = array();

            foreach ($request->except(array('_token', '_method')) as $key => $value) {
                $a[$key] = $value;
            }

            if ($request->get('translation_type') == 2) {
                $target_lang = app()['path.lang'] . '/' . $translate->code . '/general.php';
            }
            if ($request->get('translation_type') == 3) {
                $target_lang = app()['path.lang'] . '/' . $translate->code . '/admin.php';
            }

            File::put($target_lang, varexport($a, true));

        }

        // Clear cache
        Cache::flush();

        // Redirect to translation edit page
        return redirect()->route('translations.edit', $translate->id)->with('success', __('admin.content_updated'));
    }

    /** Destroy */
    public function destroy($id)
    {

        // Check if user is trying to delete main language (English)
        if ($id == '1') {

            // Redirect to list of translations
            return redirect()->route('translations.index')->with('error', __('admin.error'));

        } else {

            // Retrieve translation details
            $translate = Translation::find($id);

            $translate->delete();

            // Delete language records in settings table
            DB::table('settings')->where('language', $id)->delete();

            // Delete translation records
            DB::table('app_translations')->where('lang_id', $id)->delete();
            DB::table('category_translations')->where('lang_id', $id)->delete();
            DB::table('news_translations')->where('lang_id', $id)->delete();
            DB::table('page_translations')->where('lang_id', $id)->delete();
            DB::table('platform_translations')->where('lang_id', $id)->delete();
            DB::table('topic_translations')->where('lang_id', $id)->delete();

            File::deleteDirectory(app()['path.lang'] . '/' . $translate->code);

            // Clear cache
            Cache::flush();

            // Redirect to list of translations
            return redirect()->route('translations.index')->with('success', __('admin.content_deleted'));

        }

    }

}
