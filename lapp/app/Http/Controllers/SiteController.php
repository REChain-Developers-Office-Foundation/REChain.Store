<?php

namespace App\Http\Controllers;

use App;
use App\Models\Application;
use App\Models\Category;
use App\Models\News;
use App\Models\Page;
use App\Models\Platform;
use App\Models\Report;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use MetaTag;
use Redirect;
use Response;
use Spatie\SchemaOrg\Schema;
use Validator;

class SiteController extends Controller
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
            if ($setting->language != null) {
                $slug[$setting->language][$setting->name] = $setting->value;
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

        $this->language_name = $language_title[$this->language_id];

        $this->main_id = $language_id[$settings['site_language']];

        if ($language_direction[$this->language_id] == '1') {
            $this->language_direction = null;
        } else {
            $this->language_direction = 'rtl-';
        }

        // List of Language Variables
        foreach ($this->site_settings as $setting) {
            $setting_name = $setting->name;
            if ($setting->language == $this->language_id) {
                $this->$setting_name = $setting->value;
                $settings[$setting->name] = $setting->value;
            }
        }

        // Open Graph locale tags
        $locale_tags = Cache::rememberForever('locale-tags', function () {
            return DB::table('translations')->get()->pluck('locale_code', 'code');
        });

        // Ad Places
        $ad = Cache::rememberForever('ad-places', function () {
            $ad_places = DB::table('ads')->get();

            foreach ($ad_places as $ads) {
                $ad[$ads->id] = $ads->code;
            }

            return $ad;
        });

        $popular_apps = pure_cache('popular_apps', $this->site_language_code, $this->site_language, $this->language_id);

        $editors_choice = pure_cache('editors_choice', $this->site_language_code, $this->site_language, $this->language_id);

        $this->categories = pure_cache('categories', $this->site_language_code, $this->site_language, $this->language_id);

        $this->platforms = pure_cache('platforms', $this->site_language_code, $this->site_language, $this->language_id);

        $footer_pages = pure_cache('footer_pages', $this->site_language_code, $this->site_language, $this->language_id);

        $twitter_url = $this->twitter_account ? 'https://www.twitter.com/' . $this->twitter_account : '';

        $social_media = array($twitter_url, $this->facebook_page, $this->telegram_page, $this->youtube_page);

        $localBusiness = Schema::Organization()
            ->name($this->site_title)
            ->email($this->admin_email)
            ->url(asset('/'))
            ->sameAs($social_media)
            ->logo(Schema::ImageObject()->url(asset('/'))->id(s3_switch('logo.png')));

        // Pass data to views
        View::share(['ad' => $ad, 'settings' => $settings, 'categories' => $this->categories, 'platforms' => $this->platforms, 'footer_pages' => $footer_pages, 'popular_apps' => $popular_apps, 'locale_tags' => $locale_tags, 'editors_choice' => $editors_choice, 'languages' => $this->languages, 'language_prefix' => $this->language_prefix, 'language_icon_code' => $language_icon_code, 'menu_language_prefix' => $menu_language_prefix, 'language_name' => $this->language_name, 'language_code' => $this->site_language_code, 'localBusiness' => $localBusiness, 'slug' => $slug]);
    }

    /** Index */
    public function index()
    {
        $site_description = short_code($this->site_title, $this->site_description);

        // Meta tags
        meta_tags($this->site_title, $site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        $new_apps = pure_cache('new_apps', $this->site_language_code, $this->site_language, $this->language_id);

        $recently_updated_apps = pure_cache('recently_updated_apps', $this->site_language_code, $this->site_language, $this->language_id);

        $apps_24_hours = pure_cache('apps_24_hours', $this->site_language_code, $this->site_language, $this->language_id);

        $must_have_apps = pure_cache('must_have_apps', $this->site_language_code, $this->site_language, $this->language_id);

        $featured_apps = pure_cache('featured_apps', $this->site_language_code, $this->site_language, $this->language_id);

        $latest_topics = pure_cache('latest_topics', $this->site_language_code, $this->site_language, $this->language_id);

        $latest_news = pure_cache('latest_news', $this->site_language_code, $this->site_language, $this->language_id);

        $sliders = pure_cache('sliders', $this->site_language_code, $this->site_language, $this->language_id);

        $h1_title = short_code($this->site_title, $this->home_page_h1);

        $home_categories = home_categories($this->site_language_code, $this->site_language, $this->language_id);

        // Return view
        return view('' . $this->language_direction . 'frontend::main')->with('new_apps', $new_apps)->with('recently_updated_apps', $recently_updated_apps)->with('apps_24_hours', $apps_24_hours)->with('featured_apps', $featured_apps)->with('must_have_apps', $must_have_apps)->with('latest_topics', $latest_topics)->with('latest_news', $latest_news)->with('sliders', $sliders)->with('h1_title', $h1_title)->with('home_categories', $home_categories)->with('head_type', 0);
    }

    /** Categories */
    public function category()
    {
        $slug = request()->slug;

        // Check if category exist
        if ($this->site_language == $this->site_language_code) {
            $category_query = Category::where('slug', $slug)->where('type', '1')->first();
        } else {
            $category_query = Category::
                leftJoin('category_translations', 'categories.id', '=', 'category_translations.cat_id')
                ->select('categories.id', 'categories.slug', 'category_translations.title')
                ->where('lang_id', $this->language_id)
                ->where('slug', $slug)->first();
        }

        // Return 404 page if category not found
        if ($category_query == null) {
            abort(404);
        }

        $page = request()->has('page') ? request()->get('page') : 1;

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
                ->select('applications.slug', 'applications.image', 'applications.title as main_title', 'app_translations.title', 'applications.developer', 'applications.votes')
                ->where('lang_id', $this->language_id)
                ->where('category_id', $category_query->id)
                ->orderBy('applications.pinned', 'desc')
                ->orderBy('applications.id', 'desc')
                ->paginate($this->apps_per_page);
        }

        // Return 404 page if query is empty
        if ($apps->isEmpty() and $page > '1') {
            abort(404);
        }

        if ($category_query->custom_title != null) {
            $category_title = short_code($this->site_title, $category_query->custom_title, null, null, null, null, $category_query->title);
        } else {
            $category_title = $category_query->title . " › $this->site_title";
        }

        if ($category_query->custom_description != null) {
            $category_description = short_code($this->site_title, $category_query->custom_description, null, null, null, null, $category_query->title);
        } else {
            $category_description = $this->site_description;
        }

        if ($category_query->custom_h1 != null) {
            $h1_title = short_code($this->site_title, $category_query->custom_h1, null, null, null, null, $category_query->title);
        } else {
            $h1_title = $category_query->title;
        }

        // Meta tags
        meta_tags($category_title, $category_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, $category_query->title, url()->current());

        // Records in Other Languages
        $category_languages = other_languages('category', $slug, $this->site_language, $this->site_language_code, $this->main_id, $this->language_id);

        // Return View
        return view('' . $this->language_direction . 'frontend::category')->with('apps', $apps)->with('category_query', $category_query)->with('h1_title', $h1_title)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('category_languages', $category_languages)->with('head_type', 2);
    }

    /** Platforms */
    public function platform()
    {
        $slug = request()->slug;

        // Check if platform exist
        if ($this->site_language == $this->site_language_code) {
            $platform_query = Platform::where('slug', $slug)->first();
        } else {
            $platform_query = Platform::
                leftJoin('platform_translations', 'platforms.id', '=', 'platform_translations.platform_id')
                ->select('platforms.id', 'platform_translations.title')
                ->where('lang_id', $this->language_id)
                ->where('slug', $slug)->first();
        }

        // Return 404 page if platform not found
        if ($platform_query == null) {
            abort(404);
        }

        $page = request()->has('page') ? request()->get('page') : 1;

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
                ->select('applications.*', 'applications.title as main_title', 'app_translations.title')
                ->where('lang_id', $this->language_id)
                ->where('application_platform.platform_id', $platform_query->id)
                ->orderBy('applications.pinned', 'desc')
                ->orderBy('applications.id', 'desc')
                ->paginate($this->apps_per_page);
        }

        // Return 404 page if query is empty
        if ($apps->isEmpty() and $page > '1') {
            abort(404);
        }

        if ($platform_query->custom_title != null) {
            $platform_title = short_code($this->site_title, $platform_query->custom_title, null, null, null, null, null, $platform_query->title);
        } else {
            $platform_title = $platform_query->title . " › $this->site_title";
        }

        if ($platform_query->custom_description != null) {
            $platform_description = short_code($this->site_title, $platform_query->custom_description, null, null, null, null, null, $platform_query->title);
        } else {
            $platform_description = $this->site_description;
        }

        if ($platform_query->custom_h1 != null) {
            $h1_title = short_code($this->site_title, $platform_query->custom_h1, null, null, null, null, null, $platform_query->title);
        } else {
            $h1_title = $platform_query->title;
        }

        // Meta tags
        meta_tags($platform_title, $platform_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, $platform_query->title, url()->current());

        // Records in Other Languages
        $platform_languages = other_languages('platform', $slug, $this->site_language, $this->site_language_code, $this->main_id, $this->language_id);

        // Return view
        return view('' . $this->language_direction . 'frontend::platform')->with('apps', $apps)->with('platform_query', $platform_query)->with('h1_title', $h1_title)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('platform_languages', $platform_languages)->with('head_type', 3);
    }

    /** Apps */
    public function app()
    {
        $slug = request()->slug;

        // Check if application exist
        if ($this->site_language == $this->site_language_code) {
            $app_query = Application::
                leftJoin('versions', 'applications.id', '=', 'versions.app_id')
                ->select('applications.*', 'versions.version', 'versions.file_size', 'versions.url', 'versions.counter', 'versions.updated_at as app_updated', 'versions.id as version_id')
                ->with('tagged')
                ->orderBy('versions.id', 'desc')
                ->where('slug', $slug)->first();
        } else {
            $app_query = Application::
                leftJoin('versions', 'applications.id', '=', 'versions.app_id')
                ->leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('applications.*', 'applications.title as main_title', 'app_translations.title', 'app_translations.description', 'app_translations.custom_title', 'app_translations.custom_description', 'app_translations.custom_h1', 'app_translations.details', 'versions.version', 'versions.file_size', 'versions.url', 'versions.counter', 'versions.updated_at as app_updated', 'versions.id as version_id')
                ->with('tagged')
                ->orderBy('versions.id', 'desc')
                ->where('app_translations.lang_id', $this->language_id)
                ->where('slug', $slug)->first();
        }

        // Return 404 page if application not found
        if ($app_query == null) {
            abort(404);
        }

        // Records in Other Languages
        $app_languages = other_languages('app', $slug, $this->site_language, $this->site_language_code, $this->main_id, $this->language_id);

        // Versions
        $versions = DB::table('versions')->where('app_id', $app_query->id)->orderBy('id', 'DESC')->get();

        // List of comments
        $app_comments = DB::table('comments')->where('content_id', $app_query->id)->where('type', 1)->where('approval', 1)->orderBy('id', 'desc')->get();

        // Count comment
        $comment_data = DB::table('comments')->select('rating')->where('content_id', $app_query->id)->where('approval', 1)->selectRaw('count(rating) AS total_rating')->groupBy('rating')->orderBy('rating', 'desc')->get();

        // Format ratings
        $comment_order = array();
        foreach ($comment_data as $comment_c) {
            $comment_order[$comment_c->rating] = $comment_c->total_rating;
        }

        for ($x = 1; $x <= 5; $x++) {
            if (!array_key_exists("$x", $comment_order)) {
                $comment_order[$x] = 0;
            }
        }
        krsort($comment_order);

        $share_image = $app_query->image;

        // Default application image size
        $share_image_w = 200;
        $share_image_h = 200;

        // Use default images if images are not uploaded
        if (empty($app_query->image)) {
            $app_query->image = 'no_image.png'; // App image
            $share_image = 'default_share_image.png'; // Image to show when sharing
            $share_image_w = 600;
            $share_image_h = 315;
        }

        // Category List for Custom Meta Tags
        $meta_categories = '';
        foreach ($app_query->categories as $key => $row) {
            if ($key != 0) {$meta_categories .= ', ';}
            $meta_categories .= $row->title;
        }

        // Platform List for Custom Meta Tags
        $meta_platforms = '';
        foreach ($app_query->platforms as $key => $row) {
            if ($key != 0) {$meta_platforms .= ', ';}
            $meta_platforms .= $row->title;
        }

        if ($app_query->custom_title != null) {
            $app_title = short_code($this->site_title, $app_query->custom_title, $app_query->title ?: $app_query->main_title, $meta_categories, $meta_platforms);
        } else {
            $app_title = $app_query->title ?: $app_query->main_title . " › $this->site_title";
        }

        if ($app_query->custom_description != null) {
            $app_description = short_code($this->site_title, $app_query->custom_description, $app_query->title, $meta_categories, $meta_platforms);
        } else {
            $app_description = $app_query->description;
        }

        if ($app_query->custom_h1 != null) {
            $h1_title = short_code($this->site_title, $app_query->custom_h1, $app_query->title ?: $app_query->main_title, $meta_categories, $meta_platforms);
        } else {
            $h1_title = $app_query->title ?: $app_query->main_title;
        }

        // Meta tags
        meta_tags($app_title, $app_description, $this->twitter_account, s3_switch($share_image), $share_image_w, $share_image_h, 'website');

        $category_name = [];
        $platform_name = [];
        $os = [];

        foreach ($app_query->platforms as $platform) {
            array_push($os, $platform->title);
        }

        $os = implode(', ', $os);

        foreach ($this->categories as $category) {
            $category_name[$category->id] = $category->title;
            $category_schema_name[$category->id] = $category->application_category;
            $category_slug[$category->id] = $category->slug;
        }

        foreach ($this->platforms as $platform) {
            $platform_name[$platform->id] = $platform->title;
            $platform_slug[$platform->id] = $platform->slug;
        }

        if ($app_query->license != __('admin.free')) {
            $price = preg_replace("/[^0-9.,]/", "", $app_query->license);
        } else {
            $price = 0;
        }

        $mysplit = explode(',', $app_query->screenshots);
        $screenshot_data = array_reverse($mysplit);

        if ($app_query->license != __('admin.free')) {
            $price = preg_replace("/[^0-9.,]/", "", $app_query->license);
        } else {
            $price = 0;
        }

        $schema_data = Schema::SoftwareApplication()
            ->name($app_query->title)
            ->operatingSystem($os)
            ->review(
                array_map(function ($app_comments) {
                    return Schema::Review()
                        ->author(['name' => $app_comments->name, 'type' => 'Person'])
                        ->reviewRating(['ratingValue' => $app_comments->rating, 'type' => 'Rating'])
                        ->reviewBody($app_comments->comment)
                        ->datePublished(\Carbon\Carbon::parse($app_comments->created_at)->translatedFormat('F d, Y'));
                }, iterator_to_array($app_comments))
            )

            ->offers(Schema::Offer()
                    ->price($price)
                    ->priceCurrency($this->schema_org_price_currency)
            );

        if ($app_query->total_votes != '0') {
            $schema_data->aggregateRating(Schema::aggregateRating()
                    ->ratingValue($app_query->votes)
                    ->ratingCount($app_query->total_votes)
            );
        }

        $category_list = $app_query->categories;
        $platform_list = $app_query->platforms;

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = Schema::BreadcrumbList()
            ->itemListElement([
                \Spatie\SchemaOrg\Schema::ListItem()
                    ->position(1)
                    ->name($this->site_title)
                    ->item(asset($this->language_prefix)),
                array_map(function ($platform_list) {
                    return Schema::ListItem()
                        ->position(2)
                        ->name($platform_list->title)
                        ->item(url($this->language_prefix . $this->platform_base . '/' . $platform_list->slug));
                }, iterator_to_array($platform_list)),
                array_map(function ($category_list) {
                    return Schema::ListItem()
                        ->position(3)
                        ->name($category_list->title)
                        ->item(url($this->language_prefix . $this->category_base . '/' . $category_list->slug));
                }, iterator_to_array($category_list)),
                \Spatie\SchemaOrg\Schema::ListItem()
                    ->position(4)
                    ->name($app_query->title)
                    ->item(url()->current()),
            ]);

        // Update page views count
        DB::update("update applications set page_views = page_views+1 WHERE id = $app_query->id");

        // Update hits count
        DB::update("update applications set hits = hits+1 WHERE id = $app_query->id");

        $post_categories = $app_query->categories->pluck('id');

        if ($this->site_language == $this->site_language_code) {
            $other_apps_category = Application::whereHas('categories', function ($query) use ($post_categories, $app_query) {
                $query->whereIn('application_category.category_id', $post_categories)->where('applications.id', '!=', $app_query->id);
            })->orderBy('id', 'desc')->limit(15)->get();

        } else {

            $other_apps_category = Application::leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('applications.slug', 'applications.image', 'applications.title as main_title', 'app_translations.title', 'applications.developer', 'applications.votes')
                ->whereHas('categories', function ($query) use ($post_categories, $app_query) {
                    $query->whereIn('application_category.category_id', $post_categories)->where('applications.id', '!=', $app_query->id);
                })
                ->where('app_translations.lang_id', $this->language_id)
                ->orderBy('applications.id', 'desc')
                ->limit(15)
                ->get();
        }

        // Return View
        return view('' . $this->language_direction . 'frontend::app')->with('app_query', $app_query)->with('comment_order', $comment_order)->with('app_comments', $app_comments)->with('screenshot_data', $screenshot_data)->with('versions', $versions)->with('category_name', $category_name)->with('platform_name', $platform_name)->with('h1_title', $h1_title)->with('schema_data', $schema_data)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('other_apps_category', $other_apps_category)->with('app_languages', $app_languages)->with('head_type', 1);
    }

    /** Search */
    public function search()
    {
        $search_query = request()->post('term');

        $char_count = strlen($search_query);

        // Meta tags
        meta_tags(__('general.search') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        MetaTag::setTags([
            'robots' => 'noindex',
        ]);

        // Return a warning message if the search text is less than or equal to 2 characters
        if ($char_count <= 2) {
            $apps = [];
            return view('' . $this->language_direction . 'frontend::search')->withErrors(['msg', 'The Message'])->with('apps', $apps)->with('search_query', $search_query)->with('head_type', 20);
        }

        // Search query
        if ($this->site_language == $this->site_language_code) {
            $apps = Application::where('title', 'like', "%{$search_query}%")->orderBy('id', 'desc')->limit(30)->get();
        } else {
            $apps = Application::
                leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('applications.*', 'applications.title as main_title', 'app_translations.title')
                ->where('app_translations.lang_id', $this->language_id)
                ->where('applications.title', 'like', "%{$search_query}%")
                ->orWhere(function ($query) use ($search_query) {
                    $query->where('app_translations.title', 'like', "%{$search_query}%")
                        ->where('app_translations.lang_id', $this->language_id);
                })
                ->orderBy('applications.id', 'desc')->limit(5)->get();
        }

        // Return view
        return view('' . $this->language_direction . 'frontend::search')->with('apps', $apps)->with('search_query', $search_query)->with('head_type', 20);
    }

    /** Json Search */
    public function json_search(Request $request)
    {
        $search_term = $request->get('search');

        if ($this->site_language == $this->site_language_code) {
            $rows = Application::where('title', 'like', "%{$search_term}%")->orderBy('id', 'desc')->limit(5)->get();
        } else {
            $rows = Application::
                leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('applications.*', 'applications.title as main_title', 'app_translations.title')
                ->where('app_translations.lang_id', $this->language_id)
                ->where('applications.title', 'like', "%{$search_term}%")
                ->orWhere(function ($query) use ($search_term) {
                    $query->where('app_translations.title', 'like', "%{$search_term}%")
                        ->where('app_translations.lang_id', $this->language_id);
                })
                ->orderBy('applications.id', 'desc')->limit(5)->get();
        }

        if ($rows->isEmpty()) {
            $response = array();
        } else {
            foreach ($rows as $row) {
                $image_url = s3_switch($row->image ?? 'no_image.png');

                $site_url = asset($this->language_prefix . $this->app_base) . '/' . $row->slug;

                $response[] = array("value" => $row->id, "title" => $row->title ?? $row->main_title, "url" => $site_url, "image" => $image_url);
            }
        }
        echo json_encode($response);
    }

    /** Custom Pages */
    public function page()
    {
        $slug = request()->slug;

        // Page query
        if ($this->site_language == $this->site_language_code) {
            $page_query = Page::where('slug', $slug)->first();
        } else {
            $page_query = Page::
                leftJoin('page_translations', 'pages.id', '=', 'page_translations.page_id')
                ->where('lang_id', $this->language_id)
                ->where('slug', $slug)->first();
        }

        // Return 404 page if page not found
        if ($page_query == null) {
            abort(404);
        }

        if ($page_query->custom_title != null) {
            $page_title = short_code($this->site_title, $page_query->custom_title, null, null, null, $page_query->title);
        } else {
            $page_title = $page_query->title . " › $this->site_title";
        }

        if ($page_query->custom_description != null) {
            $page_description = short_code($this->site_title, $page_query->custom_description, null, null, null, $page_query->title);
        } else {
            $page_description = $this->site_description;
        }

        if ($page_query->custom_h1 != null) {
            $h1_title = short_code($this->site_title, $page_query->custom_h1, null, null, null, $page_query->title);
        } else {
            $h1_title = $page_query->title;
        }

        // Meta tags
        meta_tags($page_title, $page_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'article');

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, $page_query->title, url()->current());

        // Update page views count
        DB::update("update pages set page_views = page_views+1 WHERE id = $page_query->id");

        // Records in Other Languages
        $page_languages = other_languages('page', $slug, $this->site_language, $this->site_language_code, $this->main_id, $this->language_id);

        // Return view
        return view('' . $this->language_direction . 'frontend::custom_page')->with('page_query', $page_query)->with('h1_title', $h1_title)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('page_languages', $page_languages)->with('head_type', 4);
    }

    /** Topics */
    public function topic()
    {
        $page = request()->has('page') ? request()->get('page') : 1;

        // List of topics
        $all_topics = DB::table('topics')->orderBy('id', 'desc')->paginate(9);

        // News
        if ($this->site_language == $this->site_language_code) {
            $all_topics = Topic::
                orderBy('topics.id', 'desc')
                ->paginate($this->topics_per_page);
        } else {
            $all_topics = Topic::
                leftJoin('topic_translations', 'topics.id', '=', 'topic_translations.topic_id')
                ->where('lang_id', $this->language_id)
                ->orderBy('topics.id', 'desc')
                ->paginate($this->topics_per_page);
        }

        // Meta tags
        meta_tags(__('general.topics') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.topics'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::topics')->with('all_topics', $all_topics)->with('all_topics', $all_topics)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('head_type', 8);
    }

    /** Topic Item */
    public function topic_item()
    {
        $slug = request()->slug;

        // Topics query
        if ($this->site_language == $this->site_language_code) {
            $topic_query = Topic::where('slug', $slug)->first();
        } else {
            $topic_query = Topic::
                leftJoin('topic_translations', 'topics.id', '=', 'topic_translations.topic_id')
                ->select('topics.id', 'topics.image', 'topics.slug', 'topic_translations.title', 'topic_translations.description')
                ->where('lang_id', $this->language_id)
                ->where('slug', $slug)->first();
        }

        // Return 404 page if news not found
        if ($topic_query == null) {
            abort(404);
        }

        // Topics list query
        $topic_list_query = DB::table('topic_items')->where('list_id', $topic_query->id)->first();

        if ($topic_list_query == null) {
            $topic_list_query = [];
        } else {
            $topic_list_query = explode(',', $topic_list_query->app_list);
            $topic_list_query = array_filter($topic_list_query);
        }

        // Apps
        if ($this->site_language == $this->site_language_code) {
            $apps = Application::WhereIn('id', $topic_list_query)->orderBy('id', 'desc')->get();
        } else {
            $apps = Application::
                leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('applications.id', 'applications.title as main_title', 'app_translations.title', 'applications.developer', 'applications.slug', 'applications.votes', 'applications.image', 'applications.license')
                ->where('app_translations.lang_id', $this->language_id)
                ->WhereIn('app_translations.app_id', $topic_list_query)
                ->orderBy('applications.id', 'desc')->get();
        }

        $share_image = $topic_query->image;
        $share_image_w = 880;
        $share_image_h = 514;

        if ($topic_query->custom_title != null) {
            $topic_title = short_code($this->site_title, $topic_query->custom_title, null, null, null, null, null, null, null, $topic_query->title);
        } else {
            $topic_title = $topic_query->title . " › $this->site_title";
        }

        if ($topic_query->custom_description != null) {
            $topic_description = short_code($this->site_title, $topic_query->custom_description, null, null, null, null, null, null, null, $topic_query->title);
        } else {
            $topic_description = $topic_query->description;
        }

        if ($topic_query->custom_h1 != null) {
            $h1_title = short_code($this->site_title, $topic_query->custom_h1, null, null, null, null, null, null, null, $topic_query->title);
        } else {
            $h1_title = $topic_query->title;
        }

        // Meta tags
        meta_tags($topic_title, $topic_description, $this->twitter_account, s3_switch($share_image, 2), $share_image_w, $share_image_h, 'website');

        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.topics'), $this->topic_base, $topic_title, url()->current());

        // Update hits count
        DB::update("update topics set page_views = page_views+1 WHERE id = $topic_query->id");

        // Records in Other Languages
        $topic_languages = other_languages('topic', $slug, $this->site_language, $this->site_language_code, $this->main_id, $this->language_id);

        // Return view
        return view('' . $this->language_direction . 'frontend::topic')->with('topic_query', $topic_query)->with('apps', $apps)->with('topic_list_query', $topic_list_query)->with('h1_title', $h1_title)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('topic_languages', $topic_languages)->with('head_type', 7);
    }

    /** All News */
    public function all_news()
    {
        $page = request()->has('page') ? request()->get('page') : 1;

        // News
        if ($this->site_language == $this->site_language_code) {
            $all_news = News::
                orderBy('news.id', 'desc')
                ->paginate($this->news_per_page);

        } else {
            $all_news = News::
                leftJoin('news_translations', 'news.id', '=', 'news_translations.news_id')
                ->where('lang_id', $this->language_id)
                ->orderBy('news.id', 'desc')
                ->paginate($this->news_per_page);
        }

        // Meta tags
        meta_tags(__('general.news') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        foreach ($this->categories as $category) {
            $category_name[$category->id] = $category->title;
        }

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.news'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::news_all')->with('all_news', $all_news)->with('category_news', '0')->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('head_type', 6);
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
                ->select('categories.id', 'categories.slug', 'category_translations.title')
                ->where('type', '2')
                ->where('category_translations.lang_id', $this->language_id)
                ->where('slug', $slug)->first();
        }

        // Return 404 page if category not found
        if ($category_query == null) {
            abort(404);
        }

        $page = request()->has('page') ? request()->get('page') : 1;

        // News
        if ($this->site_language == $this->site_language_code) {
            $all_news = News::
                leftJoin('category_news', 'news.id', '=', 'category_news.news_id')
                ->where('category_id', $category_query->id)
                ->orderBy('news.id', 'desc')
                ->paginate($this->news_per_page);

        } else {
            $all_news = News::
                leftJoin('category_news', 'news.id', '=', 'category_news.news_id')
                ->leftJoin('news_translations', 'news.id', '=', 'news_translations.news_id')
                ->where('lang_id', $this->language_id)
                ->where('category_id', $category_query->id)
                ->orderBy('news.id', 'desc')
                ->paginate($this->news_per_page);
        }

        // Meta tags
        meta_tags($category_query->title . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.news'), $this->news_base, $category_query->title, url()->current());

        // Records in Other Languages
        $news_category_languages = other_languages('category_news', $slug, $this->site_language, $this->site_language_code, $this->main_id, $this->language_id);

        // Return view
        return view('' . $this->language_direction . 'frontend::news_all')->with('all_news', $all_news)->with('category_query', $category_query)->with('category_news', '1')->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('news_category_languages', $news_category_languages)->with('head_type', 19);
    }

    /** New Apps */
    public function new_apps()
    {
        if ($this->site_language == $this->site_language_code) {
            $apps = Application::orderBy('applications.id', 'desc')->paginate(30);
        } else {
            $apps = Application::
                leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('applications.slug', 'applications.image', 'applications.title as main_title', 'app_translations.title', 'applications.developer', 'applications.votes')
                ->where('app_translations.lang_id', $this->language_id)
                ->orderBy('applications.id', 'desc')->paginate(30);
        }

        // Meta tags
        meta_tags(__('general.new_apps') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.new_apps'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::more_pages')->with('apps', $apps)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('page_type', '1')->with('head_type', 23);
    }

    /** Recently Updated Apps */
    public function recently_updated_apps()
    {
        if ($this->site_language == $this->site_language_code) {
            $apps = Application::whereColumn('created_at', '!=', 'updated_at')->orderBy('applications.updated_at', 'desc')->paginate(30);
        } else {
            $apps = Application::
                leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('applications.slug', 'applications.image', 'applications.title as main_title', 'app_translations.title', 'applications.developer', 'applications.votes')
                ->where('app_translations.lang_id', $this->language_id)
                ->whereColumn('applications.created_at', '!=', 'applications.updated_at')
                ->orderBy('applications.updated_at', 'desc')->paginate(30);
        }

        // Meta tags
        meta_tags(__('general.recently_updated_apps') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.recently_updated_apps'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::more_pages')->with('apps', $apps)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('page_type', '6')->with('head_type', 14);
    }

    /** Featured Apps */
    public function featured_apps()
    {
        if ($this->site_language == $this->site_language_code) {
            $apps = Application::where('featured', 1)->orderBy('applications.id', 'desc')->paginate(30);
        } else {
            $apps = Application::
                leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('applications.slug', 'applications.image', 'applications.title as main_title', 'app_translations.title', 'applications.developer', 'applications.votes')
                ->where('app_translations.lang_id', $this->language_id)
                ->where('featured', 1)
                ->orderBy('applications.id', 'desc')->paginate(30);
        }

        // Meta tags
        meta_tags(__('general.featured_apps') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.featured_apps'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::more_pages')->with('apps', $apps)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('page_type', '2')->with('head_type', 10);
    }

    /** Must-Have Apps */
    public function must_have_apps()
    {
        if ($this->site_language == $this->site_language_code) {
            $apps = Application::where('must_have', 1)->orderBy('applications.id', 'desc')->paginate(30);
        } else {
            $apps = Application::
                leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('applications.slug', 'applications.image', 'applications.title as main_title', 'app_translations.title', 'applications.developer', 'applications.votes')
                ->where('app_translations.lang_id', $this->language_id)
                ->where('must_have', 1)
                ->orderBy('applications.id', 'desc')->paginate(30);
        }

        // Meta tags
        meta_tags(__('general.must_have_apps') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.must_have_apps'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::more_pages')->with('apps', $apps)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('page_type', '3')->with('head_type', 13);
    }

    /** Popular Apps */
    public function popular_apps()
    {
        if ($this->site_language == $this->site_language_code) {
            $apps = Application::orderBy('applications.page_views', 'desc')->paginate(33);
        } else {
            $apps = Application::
                leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('applications.slug', 'applications.image', 'applications.title as main_title', 'app_translations.title', 'applications.developer', 'applications.votes')
                ->where('app_translations.lang_id', $this->language_id)
                ->orderBy('applications.page_views', 'desc')->paginate(33);
        }

        // Meta tags
        meta_tags(__('general.popular_apps') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.popular_apps'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::more_pages')->with('apps', $apps)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('page_type', '4')->with('head_type', 11);
    }

    /** Popular Apps in Last 24 Hours */
    public function popular_apps_24_hours()
    {
        if ($this->site_language == $this->site_language_code) {
            $apps = Application::orderBy('applications.hits', 'desc')->paginate(33);
        } else {
            $apps = Application::
                leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('applications.slug', 'applications.image', 'applications.title as main_title', 'app_translations.title', 'applications.developer', 'applications.votes')
                ->where('app_translations.lang_id', $this->language_id)
                ->orderBy('applications.hits', 'desc')->paginate(33);
        }

        // Meta tags
        meta_tags(__('general.popular_apps') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.popular_apps'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::more_pages')->with('apps', $apps)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('page_type', '7')->with('head_type', 11);
    }

    /** Editor's Choice */
    public function editors_choice()
    {
        if ($this->site_language == $this->site_language_code) {
            $apps = Application::where('editors_choice', 1)->orderBy('applications.id', 'desc')->paginate(33);
        } else {
            $apps = Application::
                leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('applications.slug', 'applications.image', 'applications.title as main_title', 'app_translations.title', 'applications.developer', 'applications.votes')
                ->where('editors_choice', 1)
                ->where('app_translations.lang_id', $this->language_id)
                ->orderBy('applications.id', 'desc')->paginate(33);
        }

        // Meta tags
        meta_tags(__('general.editors_choice') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.editors_choice'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::more_pages')->with('apps', $apps)->with('page_type', '5')->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('head_type', 12);
    }

    /** News */
    public function news()
    {
        $slug = request()->slug;

        // News query
        if ($this->site_language == $this->site_language_code) {
            $news_query = News::where('slug', $slug)->first();
        } else {
            $news_query = News::
                leftJoin('news_translations', 'news.id', '=', 'news_translations.news_id')
                ->select('news.*', 'news_translations.title', 'news_translations.description', 'news_translations.content')
                ->where('news_translations.lang_id', $this->language_id)
                ->where('slug', $slug)->first();
        }

        // Return 404 page if news not found
        if ($news_query == null) {
            abort(404);
        }

        $share_image = $news_query->image;
        $share_image_w = 880;
        $share_image_h = 514;

        if ($news_query->custom_title != null) {
            $news_title = short_code($this->site_title, $news_query->custom_title, null, null, null, null, null, null, $news_query->title);
        } else {
            $news_title = $news_query->title . " › $this->site_title";
        }

        if ($news_query->custom_description != null) {
            $news_description = short_code($this->site_title, $news_query->custom_description, null, null, null, null, null, null, $news_query->title);
        } else {
            $news_description = $news_query->description;
        }

        if ($news_query->custom_h1 != null) {
            $h1_title = short_code($this->site_title, $news_query->custom_h1, null, null, null, null, null, null, $news_query->title);
        } else {
            $h1_title = $news_query->title;
        }

        // Meta tags
        meta_tags($news_title, $news_description, $this->twitter_account, s3_switch($share_image, 1), $share_image_w, $share_image_h, 'article');

        $category_name = [];

        foreach ($this->categories as $category) {
            $category_name[$category->id] = $category->title;
        }

        // List of comments
        $news_comments = DB::table('comments')->where('content_id', $news_query->id)->where('type', 2)->where('approval', 1)->orderBy('id', 'desc')->get();

        $category_list = $news_query->categories;

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = Schema::BreadcrumbList()
            ->itemListElement([
                \Spatie\SchemaOrg\Schema::ListItem()
                    ->position(1)
                    ->name($this->site_title)
                    ->item(asset($this->language_prefix)),
                array_map(function ($category_list) {
                    return Schema::ListItem()
                        ->position(2)
                        ->name($category_list->title)
                        ->item(url($this->language_prefix . $this->news_base . '/' . $category_list->slug));
                }, iterator_to_array($category_list)),
                \Spatie\SchemaOrg\Schema::ListItem()
                    ->position(3)
                    ->name($news_query->title)
                    ->item(url()->current()),
            ]);

        $news_schema = Schema::NewsArticle()
            ->headline($news_title)
            ->author(['name' => $this->site_title, 'url' => asset($this->language_prefix), 'type' => 'Organization'])
            ->image(asset('images/' . $news_query->image))
            ->Comment(
                array_map(function ($news_comments) {
                    return Schema::Comment()
                        ->name($news_comments->title)
                        ->author($news_comments->name)
                        ->comment($news_comments->comment)
                        ->datePublished(\Carbon\Carbon::parse($news_comments->created_at)->translatedFormat('F d, Y'));
                }, iterator_to_array($news_comments))
            );

        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.news'), $this->news_base, $news_query->title, url()->current());

        // Update hits count
        DB::update("update news set page_views = page_views+1 WHERE id = $news_query->id");

        // Records in Other Languages
        $news_languages = other_languages('news', $slug, $this->site_language, $this->site_language_code, $this->main_id, $this->language_id);

        // Return view
        return view('' . $this->language_direction . 'frontend::news')->with('page_query', $news_query)->with('reading_time', reading_time($news_query->content, "2"))->with('category_name', $category_name)->with('news_comments', $news_comments)->with('h1_title', $h1_title)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('news_schema', $news_schema)->with('news_languages', $news_languages)->with('head_type', 5);
    }

    /** Redirect */
    public function redirect()
    {
        $version_id = request()->id;

        // Check if application exist
        if ($this->site_language == $this->site_language_code) {
            $app_query = Application::
                leftJoin('versions', 'applications.id', '=', 'versions.app_id')
                ->select('versions.id', 'versions.version', 'applications.slug', 'applications.title', 'applications.image', 'applications.votes', 'applications.type')
                ->where('versions.id', $version_id)->first();
        } else {
            $app_query = Application::
                leftJoin('versions', 'applications.id', '=', 'versions.app_id')
                ->leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('versions.id', 'versions.version', 'applications.slug', 'applications.title as main_title', 'app_translations.title', 'applications.image', 'applications.votes', 'applications.type')
                ->where('lang_id', $this->language_id)
                ->where('versions.id', $version_id)->first();
        }

        $version = $app_query->version ?? '';

        // Return 404 page if application not found
        if ($app_query == null && $version == null) {
            abort(404);
        }

        // Meta tags
        MetaTag::setTags([
            'title' => "$app_query->title $version › $this->site_title",
            'canonical' => $this->language_prefix . $this->app_base . '/' . $app_query->slug,
        ]);

        // Records in Other Languages
        $app_languages = other_languages('app', $app_query->slug, $this->site_language, $this->site_language_code, $this->main_id, $this->language_id);

        // Return View
        return view('' . $this->language_direction . 'frontend::redirect')->with('app_query', $app_query)->with('app_languages', $app_languages)->with('head_type', 22);
    }

    /** Random App */
    public function random()
    {
        // Grab a random link
        $random_app = Application::select('id', 'slug')->inRandomOrder()->first();

        // Redirect to link
        return redirect("/$this->app_base/$random_app[slug]");
    }

    /** App Submission */
    public function submission()
    {
        // Check if submission form is enabled
        if ($this->show_submission_form != '1') {
            abort(404);
        }

        // Meta tags
        meta_tags(__('general.submit_your_app') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.submit_your_app'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::submission')->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('head_type', 21);
    }

    /** Tags */
    public function tags()
    {
        $slug = request()->slug;

        $tag_title = DB::table('tagging_tags')->where('slug', $slug)->first();

        // Return 404 page if tag not found
        if ($tag_title == null) {
            abort(404);
        }

        if ($this->site_language == $this->site_language_code) {
            $apps = Application::withAnyTag([$slug])->orderBy('applications.id', 'desc')->get();
        } else {
            $apps = Application::
                leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                ->select('applications.slug', 'applications.image', 'applications.title as main_title', 'app_translations.title', 'applications.developer')
                ->where('app_translations.lang_id', $this->language_id)
                ->withAnyTag([$slug])->orderBy('applications.id', 'desc')->get();
        }

        // Meta tags
        meta_tags(__('general.tagged_with', ['keyword' => $tag_title->name]), $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, $tag_title->name, url()->current());

        return view('' . $this->language_direction . 'frontend::tags')->with('apps', $apps)->with('tag_title', $tag_title->name)->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('head_type', null);
    }

    /** Cronjob */
    public function cronjob($slug)
    {
        // Grab cronjob code
        $cronjob_check = DB::table('settings')->where('name', 'cronjob_code')->first();

        $now = \Carbon\Carbon::now();

        // Check if crontab code is valid
        if ($cronjob_check->value == $slug) {

            // Update last run time
            DB::update("update settings set value = '$now' WHERE name = 'system_cronjob_last_run'");

            // Clear cache
            Cache::flush();

            // Clear entries from table
            DB::table('votes')->truncate();

            // Return success message
            return Response::json(array(
                'success' => true,
            ), 200);
        }
    }

    /** Hourly Cronjob */
    public function hourly_cronjob($slug)
    {
        // Grab cronjob code
        $cronjob_check = DB::table('settings')->where('name', 'hourly_cronjob_code')->first();

        $now = \Carbon\Carbon::now();

        // Check if cronjob code is valid
        if ($cronjob_check->value == $slug) {

            // Update last run time
            DB::update("update settings set value = '$now' WHERE name = 'hourly_cronjob_last_run'");

            $hour = date("H");

            if ($hour == '00') {
                // Reset hits
                DB::table('applications')->update(array('hits' => 0));
                // Clear cache
                Cache::flush();
            } else {
                // Clear cache
                Cache::flush();
            }

            // Return success message
            return Response::json(array(
                'success' => true,
            ), 200);
        }
    }

    /** Contact Page */
    public function contact()
    {
        // Meta tags
        meta_tags(__('general.contact') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.contact'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::contact')->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('head_type', 9);
    }

    /** Browse History */
    public function browse_history()
    {
        // Meta tags
        meta_tags(__('general.browse_history') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        if ($this->no_index_history == '1') {
            MetaTag::setTags([
                'robots' => 'noindex',
            ]);
        }

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.browse_history'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::history')->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('head_type', 17);
    }

    /** Favorites */
    public function favorites()
    {
        // Meta tags
        meta_tags(__('general.favorites') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        if ($this->no_index_favorites == '1') {
            MetaTag::setTags([
                'robots' => 'noindex',
            ]);
        }

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.favorites'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::favorites')->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('head_type', 18);
    }

    /** All Categories */
    public function all_categories()
    {
        // Meta tags
        meta_tags(__('general.all_categories') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Categories
        $rows = $this->categories;

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.all_categories'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::browse')->with('rows', $rows)->with('type', '1')->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('head_type', 15);
    }

    /** All Platforms */
    public function all_platforms()
    {
        // Meta tags
        meta_tags(__('general.all_platforms') . ' › ' . $this->site_title, $this->site_description, $this->twitter_account, s3_switch('default_share_image.png'), 600, 315, 'website');

        // Platforms
        $rows = $this->platforms;

        // Schema.org Breadcrumbs
        $breadcrumb_schema_data = schema_generator($this->site_title, $this->language_prefix, __('general.all_categories'), url()->current());

        // Return view
        return view('' . $this->language_direction . 'frontend::browse')->with('rows', $rows)->with('type', '2')->with('breadcrumb_schema_data', $breadcrumb_schema_data)->with('head_type', 16);
    }

    /** Report */
    public function report(Request $request)
    {
        $rules = array(
            'app_id' => 'required',
            'email' => 'required|email',
            'reason' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        // Return error response if form validation fails
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ), 400);

        } else {

            $process = 1;

            if ($this->enable_google_recaptcha == '1') {

                // Google reCAPTCHA validation
                $secret = $this->google_recaptcha_secret_key;
                $recaptcha_data = request()->recaptcha;

                $client = new \GuzzleHttp\Client();
                $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                    'form_params' => [
                        'secret' => $secret,
                        'response' => $recaptcha_data,
                    ],
                ]);

                $response = $response->getBody();

                $responseData = json_decode($response, true);

                if ($responseData['success'] == false) {

                    $process = 0;

                    // If Google reCAPTCHA validation fails, return error response
                    return Response::json(array(
                        'success' => false,
                        'errors' => $responseData['error-codes'],
                    ), 400);
                }
            }

            if ($process == '1') {

                $email = $request->get('email');
                $reason = $request->get('reason');
                $ip_address = $request->ip();
                $admin_email = $this->enable_google_recaptcha;
                $link = request()->headers->get('referer');

                $client_ip = $request->ip();

                Report::insert(
                    [
                        'app_id' => request()->app_id,
                        'email' => request()->email,
                        'reason' => request()->reason,
                        'ip' => $client_ip,
                        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    ]
                );

                // Return success message
                return '<div class="alert alert-success mt-3 show" role="alert">' . __('general.report_submission_thanks') . '</div>';
            }

        }
    }

}
