<?php
namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Category;
use App\Models\News;
use App\Models\Page;
use App\Models\Platform;
use App\Models\Setting;
use App\Models\Topic;
use App\Models\Sitemap;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class SitemapController extends Controller
{

    public function __construct()
    {
        // Site Settings
        $settings = Cache::rememberForever('settings', function () {
            return Setting::get();
        });

        foreach ($settings as $setting) {
            $this->settings[$setting->name] = $setting->value;

            if ($setting->language != null) {
                $slug[$setting->language][$setting->name] = $setting->value;
            }

        }

        $this->languages = Cache::rememberForever('languages', function () {
            return DB::table('translations')->OrderBy('sort', 'ASC')->get();
        });

        foreach ($this->languages as $language) {
            $lang_codes[$language->id] = $language->code;
            $lang_id[$language->code] = $language->id;
            $lang_last_mod[$language->id] = $slug[$language->id]['last_mod'];

            if ($language->code == $this->settings['site_language']) {
                $main_site_id = $lang_id[$language->code];
                if ($this->settings['root_language'] == '1') {
                    $main_base_prefix = '';
                } else {
                    $main_base_prefix = $language->code;
                }
            }

        }
        
        // Pass data to views
        View::share(['settings' => $this->settings, 'lang_codes' => $lang_codes, 'lang_last_mod' => $lang_last_mod, 'slug' => $slug, 'main_base_prefix' => $main_base_prefix, 'main_site_id' => $main_site_id]);
    }

    /** Index */
    public function index()
    {
        // Total apps
        $total_apps = Application::count();

        // Total apps (translations)
        $total_apps_translations = DB::table('app_translations')->count();

        $total_apps = $total_apps + $total_apps_translations;

        // Total categories
        $total_categories = Category::count();

        // Total categories (translations)
        $total_category_translations = DB::table('category_translations')->count();

        $total_categories = $total_categories + $total_category_translations;

        // Total platforms
        $total_platforms = Platform::count();

        // Total platforms (translations)
        $total_platform_translations = DB::table('platform_translations')->count();

        $total_platforms = $total_platforms + $total_platform_translations;

        // Total news
        $total_news = News::count();

        // Total news (translations)
        $total_news_translations = DB::table('news_translations')->count();

        $total_news = $total_news + $total_news_translations;

        // Total topics
        $total_topics = Topic::count();

        // Total topics (translations)
        $total_topics_translations = DB::table('topic_translations')->count();

        $total_topics = $total_topics + $total_topics_translations;

        // Total pages
        $total_pages = Page::count();

        // Total pages (translations)
        $total_pages_translations = DB::table('platform_translations')->count();

        $total_pages = $total_pages + $total_pages_translations;

        // Total tags
        $total_tags = DB::table('tagging_tags')->count();
        
        // Return view
        return response()->view('frontend::sitemap.index', ['total_apps' => $total_apps, 'total_categories' => $total_categories, 'total_platforms' => $total_platforms, 'total_news' => $total_news, 'total_topics' => $total_topics, 'total_pages' => $total_pages, 'total_tags' => $total_tags])->header('Content-Type', 'text/xml');
    }

    /** Sitemaps */
    public function sitemap()
    {
        // List of Sitemaps
        $sitemaps = array('addl', 'app', 'category', 'platform', 'news', 'topic', 'page', 'tag');
        $slug = request()->slug;

        if (!in_array($slug, $sitemaps)) {
            abort(404);
        }

        switch ($slug) {

            /** Additional Sitemaps */
            case "addl":
                
                // Additional Sitemaps
                $addl_sitemaps = Sitemap::get();

                // Return view
                return response()->view('frontend::sitemap.addl', ['languages' => $this->languages, 'addl_sitemaps' => $addl_sitemaps])->header('Content-Type', 'text/xml');

                break;

            /** Apps Sitemap */
            case "app":

                // Apps
                $rows = Application::select('id', 'slug', 'updated_at', DB::raw('null as translation_update'), DB::raw('null as lang_id'));

                // App Translations
                $row_translations = Application::
                    leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                    ->select('applications.id', 'applications.slug', 'applications.updated_at', 'app_translations.updated_at as translation_update', 'app_translations.lang_id');

                $rows = $rows->union($row_translations)->paginate($this->settings['sitemap_records_per_page']);

                // Return view
                return response()->view('frontend::sitemap.apps', ['rows' => $rows])->header('Content-Type', 'text/xml');

                break;

            /** Categories Sitemap */
            case "category":

                // Categories
                $rows = Category::select('id', 'slug', 'updated_at', DB::raw('null as lang_id'));

                // Category Translations
                $row_translations = Category::
                    leftJoin('category_translations', 'categories.id', '=', 'category_translations.cat_id')
                    ->select('categories.id', 'categories.slug', 'categories.updated_at', 'category_translations.lang_id');

                $rows = $rows->union($row_translations)->paginate($this->settings['sitemap_records_per_page']);

                // Return view
                return response()->view('frontend::sitemap.categories', ['rows' => $rows])->header('Content-Type', 'text/xml');

                break;

            /** Platforms Sitemap */
            case "platform":

                // Platforms
                $rows = Platform::select('id', 'slug', 'updated_at', DB::raw('null as lang_id'));

                // Platform Translations
                $row_translations = Platform::
                    leftJoin('platform_translations', 'platforms.id', '=', 'platform_translations.platform_id')
                    ->select('platforms.id', 'platforms.slug', 'platforms.updated_at', 'platform_translations.lang_id');

                $rows = $rows->union($row_translations)->paginate($this->settings['sitemap_records_per_page']);

                // Return view
                return response()->view('frontend::sitemap.platforms', ['rows' => $rows])->header('Content-Type', 'text/xml');

                break;

            /** News Sitemap */
            case "news":

                // News
                $rows = News::select('id', 'slug', 'updated_at', DB::raw('null as lang_id'));

                // News Translations
                $row_translations = News::
                    leftJoin('news_translations', 'news.id', '=', 'news_translations.news_id')
                    ->select('news.id', 'news.slug', 'news.updated_at', 'news_translations.lang_id');

                $rows = $rows->union($row_translations)->paginate($this->settings['sitemap_records_per_page']);

                // Return view
                return response()->view('frontend::sitemap.news', ['rows' => $rows])->header('Content-Type', 'text/xml');

                break;

            /** Topics Sitemap */
            case "topic":

                // Topics
                $rows = Topic::select('id', 'slug', 'updated_at', DB::raw('null as lang_id'));

                // Topics Translations
                $row_translations = Topic::
                    leftJoin('topic_translations', 'topics.id', '=', 'topic_translations.topic_id')
                    ->select('topics.id', 'topics.slug', 'topics.updated_at', 'topic_translations.lang_id');

                $rows = $rows->union($row_translations)->paginate($this->settings['sitemap_records_per_page']);

                // Return view
                return response()->view('frontend::sitemap.topics', ['rows' => $rows])->header('Content-Type', 'text/xml');

                break;

            /** Pages Sitemap */
            case "page":

                // Pages
                $rows = Page::select('id', 'slug', 'updated_at', DB::raw('null as lang_id'));

                // Platform Translations
                $row_translations = Page::
                    leftJoin('page_translations', 'pages.id', '=', 'page_translations.page_id')
                    ->select('pages.id', 'pages.slug', 'pages.updated_at', 'page_translations.lang_id');

                $rows = $rows->union($row_translations)->paginate($this->settings['sitemap_records_per_page']);

                // Return view
                return response()->view('frontend::sitemap.pages', ['rows' => $rows])->header('Content-Type', 'text/xml');

                break;

            /** Tags Sitemap */
            case "tag":

                // Tags
                $rows = DB::table('tagging_tags')->orderBy('id', 'desc')->get();

                // Return view
                return response()->view('frontend::sitemap.tags', ['rows' => $rows])->header('Content-Type', 'text/xml');

                break;

        }
    }

}
