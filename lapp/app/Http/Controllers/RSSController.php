<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Category;
use App\Models\Platform;
use App\Models\News;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class RssController extends Controller
{
    public function __construct()
    {
        // Site Settings
        $this->site_settings = Cache::rememberForever('settings', function () {
            return DB::table('settings')->get();
        });

        foreach ($this->site_settings as $setting) {
            $setting_name = $setting->name;
            $this->$setting_name = $setting->value;
            if ($setting->language == null) {
                $settings[$setting->name] = $setting->value;
            }
        }

        // Languages
        $this->languages = Cache::rememberForever('languages', function () {
            return DB::table('translations')->OrderBy('sort', 'ASC')->get();
        });

        foreach ($this->languages as $language) {
            $lang_codes[] = $language->code;
            $language_id[$language->code] = $language->id;
            $language_icon[$language->code] = $language->icon;
            $language_title[$language->id] = $language->language;
            $language_direction[$language->id] = $language->text_direction;
            $language_codes[$language->id] = $language->code;
            if ($settings['root_language'] == '1' && $language->code == $settings['site_language']) {
                $menu_language_prefix[$language->id] = '/';
            } else {
                $menu_language_prefix[$language->id] = $language->code . '/';
            }
            $locale_tags[$language->id] = $language->locale_code;
        }

        // Check if site main language is running in home directory
        if ($settings['root_language'] == '1') {
            if (request()->segment(1) == $settings['site_language'] && request()->segment(2) == null) {
                Redirect::to(asset('/'), 301)->send();
            }
            if (request()->segment(1) == $settings['site_language']) {
                abort(404);
            }
            if (in_array(request()->segment(1), $lang_codes) && request()->segment(1) != $settings['site_language']) {
                $this->language_id = $language_id[request()->segment(1)];
                $language_code = request()->segment(1);
                $language_icon_code = $language_icon[request()->segment(1)];
                $this->site_language_code = $language_codes[$this->language_id];
            } else {
                $this->language_id = $language_id[$settings['site_language']];
                $language_code = null;
                $language_icon_code = $language_icon[$settings['site_language']];
                $this->site_language_code = $language_codes[$this->language_id];
            }
        } else {
            if (!in_array(request()->segment(1), $lang_codes) && request()->segment(1) == null) {
                Redirect::to($settings['site_language'], 301)->send();
            }
            if (!in_array(request()->segment(1), $lang_codes)) {
                abort(404);
            }
            $language_code = request()->segment(1);
            $this->language_id = $language_id[$language_code];
            $language_icon_code = $language_icon[$language_code];
            $this->site_language_code = $language_codes[$this->language_id];
        }

        $this->language_prefix = $language_code . '/';

        // List of Language Variables
        foreach ($this->site_settings as $setting) {
            $setting_name = $setting->name;
            if ($setting->language == $this->language_id) {
                $this->$setting_name = $setting->value;
                $settings[$setting->name] = $setting->value;
            }
        }

        // Pass data to views
        View::share(['settings' => $settings]);

    }

    /** Apps */
    public function apps()
    {
        // Apps
        if ($this->site_language == $this->site_language_code) {
            $apps = Application::select('slug', 'image', 'title', 'description', 'developer', 'created_at')->orderBy('id', 'desc')->limit(12)->get();
        } else {
            $apps = Application::
                leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('slug', 'image', 'applications.title as main_title', 'app_translations.title', 'app_translations.description', 'developer', 'applications.created_at')
                ->where('app_translations.lang_id', $this->language_id)
                ->orderBy('applications.id', 'desc')->limit(12)->get();
        }

        // Return view
        return response()->view('frontend::rss.apps', ['apps' => $apps, 'language_prefix' => $this->language_prefix, 'language_code' => $this->site_language_code])->header('Content-Type', 'application/xml');
    }

    /** News */
    public function news()
    {
        // News
        if ($this->site_language == $this->site_language_code) {
            $news = News::select('slug', 'image', 'title', 'description', 'created_at')->orderBy('id', 'desc')->limit(2)->get();
        } else {
            $news = News::
                leftJoin('news_translations', 'news.id', '=', 'news_translations.news_id')
                ->select('news.slug', 'news.image', 'news.description', 'news.created_at', 'news_translations.title')
                ->where('news_translations.lang_id', $this->language_id)
                ->orderBy('news.id', 'desc')->limit(2)->get();
        }

        // Return view
        return response()->view('frontend::rss.news', ['news' => $news, 'language_prefix' => $this->language_prefix, 'language_code' => $this->site_language_code])->header('Content-Type', 'application/xml');
    }

    /** Category News */
    public function category_news()
    {
        $slug = request()->slug;

        // Check if category exist
        if ($this->site_language == $this->site_language_code) {
            $category_query = Category::where('type', '2')->where('slug', $slug)->first();
        } else {
            $category_query = Category::
                leftJoin('category_translations', 'categories.id', '=', 'category_translations.cat_id')
                ->select('categories.id', 'category_translations.title')
                ->where('type', '2')
                ->where('category_translations.lang_id', $this->language_id)
                ->where('slug', $slug)->first();
        }

        // Return 404 page if category not found
        if ($category_query == null) {
            abort(404);
        }

        // News
        if ($this->site_language == $this->site_language_code) {
            $news = News::
                leftJoin('category_news', 'news.id', '=', 'category_news.news_id')
                ->where('category_id', $category_query->id)
                ->orderBy('news.id', 'desc')
                ->paginate($this->news_per_page);

        } else {
            $news = News::
                leftJoin('category_news', 'news.id', '=', 'category_news.news_id')
                ->leftJoin('news_translations', 'news.id', '=', 'news_translations.news_id')
                ->where('lang_id', $this->language_id)
                ->where('category_id', $category_query->id)
                ->orderBy('news.id', 'desc')
                ->paginate($this->news_per_page);
        }

        // Return view
        return response()->view('frontend::rss.news', ['news' => $news, 'language_prefix' => $this->language_prefix, 'language_code' => $this->site_language_code])->header('Content-Type', 'application/xml');

    }

    /** Category Apps */
    public function category_apps()
    {
        $slug = request()->slug;

        $slug = request()->slug;

        // Check if category exist
        if ($this->site_language == $this->site_language_code) {
            $category_query = Category::where('slug', $slug)->where('type', '1')->first();
        } else {
            $category_query = Category::
                leftJoin('category_translations', 'categories.id', '=', 'category_translations.cat_id')
                ->select('categories.id', 'category_translations.title')
                ->where('lang_id', $this->language_id)
                ->where('slug', $slug)->first();
        }

        // Return 404 page if category not found
        if ($category_query == null) {
            abort(404);
        }

        // Apps
        if ($this->site_language == $this->site_language_code) {
            $apps = Application::
                leftJoin('application_category', 'applications.id', '=', 'application_category.application_id')
                ->where('category_id', $category_query->id)
                ->orderBy('applications.pinned', 'desc')
                ->orderBy('applications.id', 'desc')
                ->paginate($this->apps_per_page);
        } else {
            $apps = Application::
                leftJoin('application_category', 'applications.id', '=', 'application_category.application_id')
                ->leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->where('lang_id', $this->language_id)
                ->where('category_id', $category_query->id)
                ->orderBy('applications.pinned', 'desc')
                ->orderBy('applications.id', 'desc')
                ->paginate($this->apps_per_page);
        }

        // Return view
        return response()->view('frontend::rss.apps', ['apps' => $apps, 'language_prefix' => $this->language_prefix, 'language_code' => $this->site_language_code])->header('Content-Type', 'application/xml');

    }
    
    /** Platform Apps */
    public function platform_apps()
    {
        $slug = request()->slug;

        $slug = request()->slug;

        // Check if platform exist
        if ($this->site_language == $this->site_language_code) {
            $platform_query = Platform::where('slug', $slug)->first();
        } else {
            $platform_query = Platform::
                leftJoin('platform_translations', 'platforms.id', '=', 'platform_translations.cat_id')
                ->select('platforms.id', 'platform_translations.title')
                ->where('lang_id', $this->language_id)
                ->where('slug', $slug)->first();
        }

        // Return 404 page if platform not found
        if ($platform_query == null) {
            abort(404);
        }

        // Apps
        if ($this->site_language == $this->site_language_code) {
            $apps = Application::
                leftJoin('application_platform', 'applications.id', '=', 'application_platform.application_id')
                ->where('platform_id', $platform_query->id)
                ->orderBy('applications.pinned', 'desc')
                ->orderBy('applications.id', 'desc')
                ->paginate($this->apps_per_page);
        } else {
            $apps = Application::
                leftJoin('application_platform', 'applications.id', '=', 'application_platform.application_id')
                ->leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->where('lang_id', $this->language_id)
                ->where('platform_id', $platform_query->id)
                ->orderBy('applications.pinned', 'desc')
                ->orderBy('applications.id', 'desc')
                ->paginate($this->apps_per_page);
        }

        // Return view
        return response()->view('frontend::rss.apps', ['apps' => $apps, 'language_prefix' => $this->language_prefix, 'language_code' => $this->site_language_code])->header('Content-Type', 'application/xml');

    }

}
