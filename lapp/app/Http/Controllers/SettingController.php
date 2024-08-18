<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Translation;
use App\Models\Platform;
use App\Models\Sitemap;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageManagerStatic as Image;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Site Settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $this->settings[$setting->name] = $setting->value;
            $setting_name = $setting->name;
            $this->$setting_name = $setting->value;
        }

        // Pass data to views
        View::share(['settings' => $this->settings]);
    }

    /** General Settings */
    public function index(Request $request)
    {
        // Generate Cronjob Code - Google Play Store
        if ($request->has('submission_cronjob_code_google')) {

            $rand = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 25)), 0, 25);

            // Update cronjob link
            Setting::where('name', 'submission_cronjob_code_google')->update(['value' => $rand]);

            // Clear cache
            Cache::flush();

            // Redirect back
            return back()->with('success', __('admin.new_link_generated'));
        }

        // Generate Cronjob Code - Apple App Store
        if ($request->has('submission_cronjob_code_apple')) {

            $rand = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 25)), 0, 25);

            // Update cronjob link
            Setting::where('name', 'submission_cronjob_code_apple')->update(['value' => $rand]);

            // Clear cache
            Cache::flush();

            // Redirect back
            return back()->with('success', __('admin.new_link_generated'));
        }

        // Generate Cronjob Code
        if ($request->has('cronjob')) {

            $rand = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 25)), 0, 25);

            // Update cronjob link
            Setting::where('name', 'cronjob_code')->update(['value' => $rand]);

            // Clear cache
            Cache::flush();

            // Redirect to settings page
            return back()->with('success', __('admin.new_link_generated'));
        }

        // Generate Cronjob Code
        if ($request->has('hourly_cronjob')) {

            $rand = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 25)), 0, 25);

            // Update cronjob link
            Setting::where('name', 'hourly_cronjob_code')->update(['value' => $rand]);

            // Clear cache
            Cache::flush();

            // Redirect to settings page
            return back()->with('success', __('admin.new_link_generated'));
        }

        // Languages
        $languages = Translation::pluck('language', 'code');
        
        // List of Platforms
        $platforms = Platform::orderBy('title', 'ASC')->get()->pluck('title', 'id');

        // Return view
        return view('adminlte::settings.general')->with('languages', $languages)->with('platforms', $platforms);
    }

    // Checkbox Update Function
    public function checkbox_update($box, $box_data)
    {
        if ($box_data == 'on') {
            DB::update("update settings set value = '1' WHERE name = '$box'");
        } else {
            DB::update("update settings set value = '0' WHERE name = '$box'");
        }
    }

    /** General Settings Update */
    public function store(Request $request)
    {
        DB::update("update settings set value = value+1 WHERE name = 'update_count'");

        $this->validate($request, [
            'apps_per_page' => 'required|numeric',
            'news_per_page' => 'required|numeric',
            'topics_per_page' => 'required|numeric',
            'sitemap_records_per_page' => 'required|numeric',
            'cookie_prefix' => 'required|max:15',
            'image_quality' => 'required|integer|between:0,100',
            'facebook_page' => 'nullable|url',
            'telegram_page' => 'nullable|url',
            'youtube_page' => 'nullable|url',
            'admin_email' => 'required|email',
        ]);

        foreach ($request->except(array(
            '_token',
            '_method',
        )) as $key => $value) {

            $value = addslashes($value);

            // Update settings
            DB::update("update settings set value = '$value' WHERE name = '$key'");

            $this->checkbox_update('save_as_webp', $request->get('save_as_webp'));
            $this->checkbox_update('show_cookie_bar', $request->get('show_cookie_bar'));
            $this->checkbox_update('enable_google_recaptcha', $request->get('enable_google_recaptcha'));
            $this->checkbox_update('enable_cache', $request->get('enable_cache'));
            $this->checkbox_update('auto_submission_gps', $request->get('auto_submission_gps'));
            $this->checkbox_update('auto_submission_aas', $request->get('auto_submission_aas'));
            $this->checkbox_update('auto_comment_approval', $request->get('auto_comment_approval'));
            $this->checkbox_update('show_submission_form', $request->get('show_submission_form'));
            $this->checkbox_update('reading_time', $request->get('reading_time'));
            $this->checkbox_update('rss_link', $request->get('rss_link'));
            $this->checkbox_update('random_app_link', $request->get('random_app_link'));
            $this->checkbox_update('enable_show_more', $request->get('enable_show_more'));
            $this->checkbox_update('infinite_scroll', $request->get('infinite_scroll'));
            $this->checkbox_update('rating_as_number', $request->get('rating_as_number'));
            $this->checkbox_update('use_text_logo', $request->get('use_text_logo'));
            $this->checkbox_update('root_language', $request->get('root_language'));
            $this->checkbox_update('no_index_history', $request->get('no_index_history'));
            $this->checkbox_update('no_index_favorites', $request->get('no_index_favorites'));
            $this->checkbox_update('show_top_bar', $request->get('show_top_bar'));

            $driver = env('FILESYSTEM_DRIVER');

            // Upload Site Logo
            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $location = public_path('images/logo.png');

                if ($driver == 's3') {
                    Storage::disk('s3')->put('/images/logo.png', file_get_contents($image), 'public');

                } else {
                    Image::make($image)->save($location);
                }
            }

            // Upload Favicon
            if ($request->hasFile('favicon')) {
                $image = $request->file('favicon');
                $location = public_path('images/favicon.png');

                if ($driver == 's3') {
                    $imageFile = \Image::make($image)->resize('192', '192')->stream('png', $this->image_quality);
                    Storage::disk('s3')->put('/images/favicon.png', $imageFile, 'public');

                } else {
                    Image::make($image)->resize(192, 192)->save($location);

                }
            }

            // Upload Default Share Image
            if ($request->hasFile('default_app_image')) {

                $image = $request->file('default_app_image');
                $location = public_path('images/no_image.png');

                if ($driver == 's3') {
                    $imageFile = \Image::make($image)->resize('200', '200')->stream('png', $this->image_quality);
                    Storage::disk('s3')->put('/images/no_image.png', $imageFile, 'public');
                } else {
                    Image::make($image)->resize(200, 200)->save($location);
                }
            }

            // Upload Default App Image
            if ($request->hasFile('share')) {

                $image = $request->file('share');
                $location = public_path('images/default_share_image.png');

                if ($driver == 's3') {
                    $imageFile = \Image::make($image)->resize('600', '315')->stream('png', $this->image_quality);
                    Storage::disk('s3')->put('/images/default_share_image.png', $imageFile, 'public');

                } else {
                    Image::make($image)->resize(600, 315)->save($location);

                }

            }

        }

        $custom_css = '.navbar {
background-color: ' . $request->get('navbar_background_color') . ' !important;
}

.navbar-collapse {
    background-color: ' . $request->get('navbar_background_color') . ' !important;
}

.navbar-expand-lg .navbar-nav .dropdown-menu {
     background-color: ' . $request->get('navbar_background_color') . ' !important;
}

.nav-link {
    color: ' . $request->get('navbar_text_color') . ' !important;
}

.navbar-nav a {
    color: ' . $request->get('navbar_text_color') . ' !important;
}

.nav-link:hover {
    color: ' . $request->get('navbar_text_hover_color') . ' !important;
}

.navbar-toggler svg {
    fill: ' . $request->get('navbar_toggler_color') . ' !important;
}

.dropdown-item:active, .dropdown-item:hover {
  background-color: ' . $request->get('navbar_background_color') . ' !important;
  color: ' . $request->get('navbar_text_hover_color') . ' !important;
}

.dropdown-toggle::after {
      color: ' . $request->get('dropdown_arrow_color') . ' !important;
}

.offcanvas-title {
      color: ' . $request->get('navbar_text_color') . ' !important;
}

';

        File::put('css/custom.css', $custom_css);

        // Clear cache
        Cache::flush();

        // Redirect to settings page
        return redirect(env('ADMIN_URL') . '/general_settings')->with('success', __('admin.content_updated'));
    }

    /** SEO Settings */
    public function seo_settings()
    {
        // List of languages
        $translations = Translation::orderBy('id', 'ASC')->get();

        foreach ($translations as $language) {
            $language_code[$language->id] = $language->icon;
        }

        // Return view
        return view('adminlte::settings.seo_settings')->with('translations', $translations)->with('language_menu', '1')->with('language_code', $language_code);
    }

    /** Language SEO Settings */
    public function seo_settings_language(Request $request)
    {
        $id = request()->id;

        // Language details
        $translation = Translation::where('id', $id)->first();

        // Return 404 page if language not found
        if ($translation == null) {
            abort(404);
        }

        // Site Settings
        $site_settings_seo = DB::table('settings')->where('language', $id)->get();

        foreach ($site_settings_seo as $seo_setting) {
            $seo_settings[$seo_setting->name] = $seo_setting->value;
        }

        // Clear cache
        Cache::flush();

        // Return view
        return view('adminlte::settings.seo_settings')->with('seo_settings', $seo_settings)->with('language_menu', '0');
    }

    /** SEO Settings Update */
    public function seo_settings_update(Request $request)
    {

        $id = request()->id;

        $this->validate($request, [
            'site_title' => 'required',
            'app_base' => 'required',
        ]);

        foreach ($request->except(array(
            '_token',
            '_method',
        )) as $key => $value) {

            $value = addslashes($value);

            // Update settings
            DB::update("update settings set value = '$value' WHERE name = '$key' AND language = '$id'");
        }

        // Redirect to SEO settings page
        return back()->with('success', __('admin.content_updated'));

    }

    /** PWA Settings */
    public function pwa_settings()
    {
        // Return view
        return view('adminlte::settings.pwa');
    }

    /** PWA Settings Update */
    public function pwa_settings_update(Request $request)
    {

        $this->validate($request, [
            'pwa_name' => 'required|max:255',
            'pwa_short_name' => 'required|max:255',
            'pwa_description' => 'required|max:255',
            'pwa_theme_color' => 'required|max:7',
            'pwa_background_color' => 'required|max:7',
        ]);

        foreach ($request->except(array(
            '_token',
            '_method',
        )) as $key => $value) {

            $value = addslashes($value);

            // Update settings
            DB::update("update settings set value = '$value' WHERE name = '$key'");

            $manifest_json = '{
  "id": "/",
  "scope": "/",
  "name": "' . $request->get('pwa_name') . '",
  "display": "standalone",
  "start_url": "/",
  "short_name": "' . $request->get('pwa_short_name') . '",
  "theme_color": "' . $request->get('pwa_theme_color') . '",
  "description": "' . $request->get('pwa_description') . '",
  "orientation": "portrait",
  "background_color": "' . $request->get('pwa_background_color') . '",
  "related_applications": [],
  "prefer_related_applications": false,
  "display_override": [],
  "icons": [
    {
      "src": "/images/pwa-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    },
    {
      "src": "/images/pwa-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/images/pwa-48-48.png",
      "sizes": "48x48",
      "type": "image/png"
    },
    {
      "src": "/images/pwa-24-24.png",
      "sizes": "24x24",
      "type": "image/png"
    }
  ],
  "screenshots": [
    {
      "src": "/images/pwa-screenshot.png",
      "sizes": "1080x1910",
      "type": "image/png"
    }
  ],
 "features": [
    "Cross Platform",
    "fast",
    "simple"
  ],
  "categories": [
    "utilities"
  ],
  "shortcuts": []
}';

            File::put('manifest.json', $manifest_json);

            // Upload PWA Screenshot
            if ($request->hasFile('pwa_screenshot')) {
                $image = $request->file('pwa_screenshot');
                $location = public_path('images/pwa-screenshot.png');
                Image::make($image)->resize(1080, 1920)->save($location);
            }

            // Upload PWA Icon (512x512)
            if ($request->hasFile('pwa_512')) {
                $image = $request->file('pwa_512');
                $location = public_path('images/pwa-512x512.png');
                Image::make($image)->resize(512, 512)->save($location);
            }

            // Upload PWA Icon (192x192)
            if ($request->hasFile('pwa_192')) {
                $image = $request->file('pwa_192');
                $location = public_path('images/pwa-192x192.png');
                Image::make($image)->resize(192, 192)->save($location);
            }

            // Upload PWA Icon (48x48)
            if ($request->hasFile('pwa_48')) {
                $image = $request->file('pwa_48');
                $location = public_path('images/pwa-48x48.png');
                Image::make($image)->resize(48, 48)->save($location);
            }

            // Upload PWA Icon (24x24)
            if ($request->hasFile('pwa_24')) {
                $image = $request->file('pwa_24');
                $location = public_path('images/pwa-24x24.png');
                Image::make($image)->resize(24, 24)->save($location);
            }

            $this->checkbox_update('enable_pwa', $request->get('enable_pwa'));
        }

        // Clear cache
        Cache::flush();

        // Redirect to PWA settings page
        return back()->with('success', __('admin.content_updated'));
    }

    /** OpenAI Settings */
    public function openai_settings()
    {
        // Return view
        return view('adminlte::settings.openai');
    }

    /** OpenAI Settings Update */
    public function openai_settings_update(Request $request)
    {

        $this->validate($request, [
            'openai_max_tokens' => 'required|numeric',
            'openai_temperature' => 'required|numeric',
            'openai_regenerate_command' => 'required|max:65535',
            'openai_auto_regenerate_command' => 'required|max:65535',
        ]);

        foreach ($request->except(array(
            '_token',
            '_method',
        )) as $key => $value) {

            $value = addslashes($value);

            // Update settings
            DB::update("update settings set value = '$value' WHERE name = '$key'");

            $this->checkbox_update('openai_auto_submission', $request->get('openai_auto_submission'));
        }

        // Clear cache
        Cache::flush();

        // Redirect to OpenAI settings page
        return back()->with('success', __('admin.content_updated'));
    }


    /** CDN Settings */
    public function cdn_settings()
    {
        // Return view
        return view('adminlte::settings.cdn');
    }

    /** CDN Settings Update */
    public function cdn_settings_update(Request $request)
    {
        push_assets();

        // Clear cache
        Cache::flush();

        // Redirect to OpenAI settings page
        return back()->with('success', __('admin.assets_pushed'));
    }

    /** Sitemap Settings */
    public function sitemap_settings()
    {
        // Additional Sitemaps
        $addl_sitemaps = Sitemap::get();
        
        // Return view
        return view('adminlte::settings.sitemap')->with('addl_sitemaps', $addl_sitemaps);
    }

    /** Sitemap Settings Update */
    public function sitemap_settings_update(Request $request)
    {

        $this->validate($request, [
            'sitemap_home_changefreq' => 'required',
            'sitemap_home_priority' => 'required',
        ]);

        foreach ($request->except(array(
            '_token',
            '_method',
            'url_addl',
            'changefreq_addl',
            'priority_addl',
            'last_update',
        )) as $key => $value) {

            $value = addslashes($value);

            // Update settings
            DB::update("update settings set value = '$value' WHERE name = '$key'");
        }
        
        $sitemap_data['url'] = array();
        
        foreach ($request->except(array(
            '_token',
            '_method',
        )) as $key => $value) {
            
        if($key == 'url_addl') {
            $sitemap_data['url'] = $value;
        }
        
        if($key == 'changefreq_addl') {
            $sitemap_data['changefreq'] = $value;
        }
        
        if($key == 'priority_addl') {
            $sitemap_data['priority'] = $value;
        }
        
        if($key == 'last_update') {
            $sitemap_data['update'] = $value;
        }
        
        }
        
        
        DB::table('addl_sitemaps')->truncate();
        
        for ($x = 0; $x < count($sitemap_data['url']); $x++) {
        
            DB::table('addl_sitemaps')->insert(
            [
                'url' => $sitemap_data['url'][$x],
                'changefreq' => $sitemap_data['changefreq'][$x],
                'priority' => $sitemap_data['priority'][$x],
                'last_update' => $sitemap_data['update'][$x],
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ]
        );
        echo $x;
        
        }

        // Clear cache
        Cache::flush();

        // Redirect to OpenAI settings page
        return back()->with('success', __('admin.content_updated'));
    }

    /** Clear Cache */
    public function clear_cache()
    {

        // Clear cache
        Cache::flush();

        // Redirect back
        return back()->with('success', __('admin.system_cache_cleared'));
    }

}
