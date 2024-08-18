<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class CrawlerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        // List of categories
        $categories = DB::table('categories')->where('type', '1')->orderBy('sort', 'ASC')->get();

        // Site Settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $settings[$setting->name] = $setting->value;
        }

        $this->google_play_default_country = $settings['google_play_default_country'];
        $this->google_play_default_language = $settings['google_play_default_language'];
        $this->crawler_categories_apple = $settings['crawler_categories_apple'];

        // Pass data to views
        View::share(['categories' => $categories, 'crawler_categories_google' => $settings['crawler_categories_google'], 'crawler_categories_apple' => $settings['crawler_categories_apple']]);
    }

    /** Index - Google */
    public function index_google()
    {
        // Retrieve categories from Google Play Store
        $gplay = new \Nelexa\GPlay\GPlayApps($defaultLocale = $this->google_play_default_language, $defaultCountry = $this->google_play_default_country);
        $app_categories = $gplay->getCategories();

        // Return view
        return view('adminlte::settings.app_crawler_google', compact('app_categories'));
    }

    /** Update - Google */
    public function update_google(Request $request)
    {
        $items = array();
        foreach ($request->except(array('_token', '_method')) as $key => $value) {
            $items[$key] = $value;
        }

        $json = json_encode($items);

        DB::update("update settings set value = '$json' WHERE name = 'crawler_categories_google'");

        // Clear cache
        Cache::flush();

        // Redirect to settings page
        return redirect(env('ADMIN_URL').'/scraper_categories_google')->with('success', __('admin.content_updated'));
    }

    /** Index - Apple */
    public function index_apple()
    {
        // Retrieve categories
        $app_categories = json_decode($this->crawler_categories_apple);

        // Return view
        return view('adminlte::settings.app_crawler_apple', compact('app_categories'));
    }

    /** Update - Apple */
    public function update_apple(Request $request)
    {
        $items = array();
        foreach ($request->except(array('_token', '_method')) as $key => $value) {
            $items[$key] = $value;
        }

        $json = json_encode($items);

        DB::update("update settings set value = '$json' WHERE name = 'crawler_categories_apple'");

        // Clear cache
        Cache::flush();

        // Redirect to settings page
        return redirect(env('ADMIN_URL').'/scraper_categories_apple')->with('success', __('admin.content_updated'));
    }

    /** Clear Cache */
    public function clear_cache()
    {
        // Clear cache
        Cache::flush();

        // Redirect to list of applications
        return redirect(env('ADMIN_URL').'/apps')->with('success', __('admin.system_cache_cleared'));
    }

}
