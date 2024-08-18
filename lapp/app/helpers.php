<?php
use App\Models\Application;
use App\Models\Category;
use App\Models\News;
use App\Models\Page;
use App\Models\Platform;
use App\Models\Topic;
use Carbon\Carbon;
use Spatie\SchemaOrg\Schema;

// Records in Other Languages
function other_languages($type, $slug, $site_language, $site_language_code, $main_id, $language_id)
{

    if ($type == 'app') {
        $translations = Application::
            leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
            ->leftJoin('translations', 'app_translations.lang_id', '=', 'translations.id')
            ->select('translations.id', 'translations.code', 'translations.language')
            ->where('applications.slug', $slug)
            ->get();
    }
    if ($type == 'category') {
        $translations = Category::
            leftJoin('category_translations', 'categories.id', '=', 'category_translations.cat_id')
            ->leftJoin('translations', 'category_translations.lang_id', '=', 'translations.id')
            ->select('translations.id', 'translations.code', 'translations.language')
            ->where('categories.slug', $slug)->get();
    }
    if ($type == 'platform') {
        $translations = Platform::
            leftJoin('platform_translations', 'platforms.id', '=', 'platform_translations.platform_id')
            ->leftJoin('translations', 'platform_translations.lang_id', '=', 'translations.id')
            ->select('translations.id', 'translations.code', 'translations.language')
            ->where('platforms.slug', $slug)->get();
    }
    if ($type == 'page') {
        $translations = Page::
            leftJoin('page_translations', 'pages.id', '=', 'page_translations.page_id')
            ->leftJoin('translations', 'page_translations.lang_id', '=', 'translations.id')
            ->select('translations.id', 'translations.code', 'translations.language')
            ->where('pages.slug', $slug)->get();
    }
    if ($type == 'news') {
        $translations = News::
            leftJoin('news_translations', 'news.id', '=', 'news_translations.news_id')
            ->leftJoin('translations', 'news_translations.lang_id', '=', 'translations.id')
            ->select('translations.id', 'translations.code', 'translations.language')
            ->where('news.slug', $slug)->get();
    }
    if ($type == 'topic') {
        $translations = Topic::
            leftJoin('topic_translations', 'topics.id', '=', 'topic_translations.topic_id')
            ->leftJoin('translations', 'topic_translations.lang_id', '=', 'translations.id')
            ->select('translations.id', 'translations.code', 'translations.language')
            ->where('topics.slug', $slug)->get();
    }
    if ($type == 'category_news') {
        $translations = Category::
            leftJoin('category_translations', 'categories.id', '=', 'category_translations.cat_id')
            ->leftJoin('translations', 'category_translations.lang_id', '=', 'translations.id')
            ->select('translations.id', 'translations.code', 'translations.language')
            ->where('categories.type', '2')
            ->where('categories.slug', $slug)->get();
    }
    $languages = array();

    $data = array(
        'code' => $site_language,
        'id' => $main_id,
    );
    array_push($languages, $data);

    foreach ($translations as $translation) {
        $data = array(
            'code' => $translation->code,
            'id' => $translation->id,
        );
        if ($translation->code != null) {
            array_push($languages, $data);
        }

    }

    return $languages;
}

// Short Code Function
function short_code($site_title, $title_format = null, $app_title = null, $categories = null, $platforms = null, $page_title = null, $category_title = null, $platform_title = null, $news_title = null, $topic_title = null)
{
    $date = Carbon::now();

    $before = array(
        '%sep%', // Seperator
        '%site_title%', // Site Title
        '%app_title%', // App Title
        '%categories%', // Categories
        '%platforms%', // Platforms
        '%page_title%', // Page Title
        '%category_title%', // Category Title
        '%platform_title%', // Platform Title
        '%news_title%', // News Title
        '%topic_title%', // Topic Title
        '%year%', // Year
        '%month%', // Month
        '%day%', // Day
        '%month_text%', // Month (Full Textual Representation)
        '%day_text%', // Day (Full Textual Representation)
    );

    $after = array(
        "›",
        $site_title,
        $app_title,
        $categories,
        $platforms,
        $page_title,
        $category_title,
        $platform_title,
        $news_title,
        $topic_title,
        date('Y'),
        date('m'),
        date('d'),
        \Carbon\Carbon::parse($date)->translatedFormat('F'),
        \Carbon\Carbon::parse($date)->translatedFormat('l'),
    );

    return str_replace($before, $after, $title_format);
}

// Home Categories
function home_categories($site_language_code, $site_language, $language_id)
{
    $categories = pure_cache('categories', $site_language_code, $site_language, $language_id);

    $data_array = [];

    foreach ($categories as $category) {
        if ($category->home_page == '1') {

            // Apps
            if ($site_language == $site_language_code) {
                $apps = Application::
                    leftJoin('application_category', 'applications.id', '=', 'application_category.application_id')
                    ->select('applications.id', 'applications.title', 'applications.slug', 'applications.image', 'applications.developer', 'applications.created_at')
                    ->where('category_id', $category->id)
                    ->orderBy('applications.pinned', 'desc')
                    ->orderBy('applications.id', 'desc')
                    ->limit(15)
                    ->get();
            } else {
                $apps = Application::
                    leftJoin('application_category', 'applications.id', '=', 'application_category.application_id')
                    ->leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                    ->select('applications.id', 'applications.title as main_title', 'app_translations.title', 'applications.slug', 'applications.image', 'applications.developer', 'applications.created_at')
                    ->where('lang_id', $language_id)
                    ->where('category_id', $category->id)
                    ->orderBy('applications.pinned', 'desc')
                    ->orderBy('applications.id', 'desc')
                    ->limit(15)
                    ->get();
            }
            $data_array[$category->id] = $apps;
        }
    }

    return $data_array;
}

// Update Sitemap Last Update Time
function last_update($lang_id)
{
    $languages = Cache::rememberForever('languages', function () {
        return DB::table('translations')->OrderBy('sort', 'ASC')->get();
    });

    foreach ($languages as $language) {
        if ($language->id == $lang_id) {
            $last_record = Application::orderBy('updated_at', 'desc')->first();
            $last_mod = gmdate(DateTime::W3C, strtotime($last_record->updated_at));
            DB::update("update settings set value = '$last_mod' WHERE name='last_mod' AND language='$language->id'");

        } else {
            $last_record = DB::table('app_translations')->orderBy('updated_at', 'desc')->where('lang_id', $language->id)->first();
            if (isset($last_record->updated_at)) {
                $last_mod = gmdate(DateTime::W3C, strtotime($last_record->updated_at));
                DB::update("update settings set value = '$last_mod' WHERE name='last_mod' AND language='$language->id'");
            }

        }
    }

}

// Schema.org Breadcrumbs
function schema_generator($site_title, $language_prefix, $data_name_1, $data_url_1, $data_name_2 = null, $data_url_2 = null)
{
    if ($data_name_2 == null) {
        $breadcrumb_schema_data = Schema::BreadcrumbList()
            ->itemListElement([
                \Spatie\SchemaOrg\Schema::ListItem()
                    ->position(1)
                    ->name($site_title)
                    ->item(rtrim(asset($language_prefix), "/")),
                \Spatie\SchemaOrg\Schema::ListItem()
                    ->position(2)
                    ->name($data_name_1)
                    ->item($data_url_1),
            ]);
    }

    if ($data_name_2 != null) {
        $breadcrumb_schema_data = Schema::BreadcrumbList()
            ->itemListElement([
                \Spatie\SchemaOrg\Schema::ListItem()
                    ->position(1)
                    ->name($site_title)
                    ->item(rtrim(asset($language_prefix), "/")),
                \Spatie\SchemaOrg\Schema::ListItem()
                    ->position(2)
                    ->name($data_name_1)
                    ->item(asset($language_prefix . $data_url_1)),
                \Spatie\SchemaOrg\Schema::ListItem()
                    ->position(3)
                    ->name($data_name_2)
                    ->item($data_url_2),
            ]);
    }

    return $breadcrumb_schema_data;
}

// MetaTag Function
function meta_tags($title, $description, $twitter_account, $image, $width, $height, $og_type)
{
    return MetaTag::setTags([
        'title' => $title,
        'description' => $description,
        'twitter_site' => $twitter_account,
        'twitter_title' => $title,
        'twitter_card' => 'summary',
        'twitter_url' => url()->current(),
        'twitter_description' => $description,
        'twitter_image' => $image,
        'og_title' => $title,
        'og_description' => $description,
        'og_url' => url()->current(),
        'og_image' => $image, 'og_image_width' => $width, 'og_image_height' => $height,
        'og_type' => $og_type,
    ]);
}

// SVG Icons
function svg_icon($fa_icon_code, $checked = 0)
{
    $fa_icons = Cache::rememberForever('fa_icons', function () {

        $category_icons = DB::table('categories')->get()->pluck('fa_icon');
        $platform_icons = DB::table('platforms')->get()->pluck('fa_icon');
        $sytem_icons = array("youtube-footer", "heart", "up_vote", "down_vote", "fas fa-qrcode", "fas fa-user", "fab fa-facebook-f", "fab fa-twitter", "fab fa-linkedin-in", "fas fa-envelope", "fab fa-whatsapp", "fas fa-angle-left", "fas fa-angle-right", "fas fa-download", "fas fa-external-link-alt", "fas fa-star checked", "fas fa-star", "fas fa-search", "facebook-footer", "twitter-footer", "telegram-footer", "fas fa-bars", "fas fa-rss", "fas fa-tag");

        return DB::table('fa_icons')->WhereIn('icon', $category_icons)->orWhereIn('icon', $platform_icons)->orWhereIn('icon', $sytem_icons)->get();
    });

    foreach ($fa_icons as $icon) {
        $svg_icon[$icon->icon] = $icon->svg_code;
    }
    if (isset($svg_icon[$fa_icon_code])) {
        return $svg_icon[$fa_icon_code];
    }
};

function getAbsoluteUrl($url)
{

    $urlParts = parse_url($url);
    $baseParts = parse_url('https://play.google.com');
    $absoluteParts = array_merge($baseParts, $urlParts);

    $absoluteUrl = $absoluteParts['scheme'] . '://' . $absoluteParts['host'];
    if (isset($absoluteParts['path'])) {
        $absoluteUrl .= $absoluteParts['path'];
    } else {
        $absoluteUrl .= '/';
    }
    if (isset($absoluteParts['query'])) {
        $absoluteUrl .= '?' . $absoluteParts['query'];
    }
    if (isset($absoluteParts['fragment'])) {
        $absoluteUrl .= '#' . $absoluteParts['fragment'];
    }

    return $absoluteUrl;
}

/** Report Reasons */
function report_reasons()
{
    $report_reasons = array(
        '1' => __('admin.app_does_not_work'),
        '2' => __('admin.app_is_not_secure'),
        '4' => __('admin.link_is_broken'),
    );

    return $report_reasons;
}

/** Sitemap Change Frequency */
function sitemap_changefreq()
{
    $sitemap_changefreq = array(
        'Always' => 'Always',
        'Hourly' => 'Hourly',
        'Daily' => 'Daily',
        'Weekly' => 'Weekly',
        'Monthly' => 'Monthly',
        'Yearly' => 'Yearly',
        'Never' => 'Never',
    );

    return $sitemap_changefreq;
}

/** Sitemap Priority */
function sitemap_priority()
{
    $sitemap_priority = array(
        '0.0' => '0.0',
        '0.1' => '0.1',
        '0.2' => '0.2',
        '0.3' => '0.3',
        '0.4' => '0.4',
        '0.5' => '0.5',
        '0.6' => '0.6',
        '0.7' => '0.7',
        '0.8' => '0.8',
        '0.9' => '0.9',
        '1.0' => '1.0',
    );

    return $sitemap_priority;
}

/** Stars Function */
function stars($votes, $rating_as_number, $type = '1')
{
    if ($type == '1') {
        $editor_star = ' checked';
    } else {
        $editor_star = ' editor-star';
    }

    if ($rating_as_number == '1') {

        if (round($votes) == '0') {
            return svg_icon('fas fa-star');
        } else {
            return svg_icon('fas fa-star' . $editor_star) . " <span class=\"votes\">" . floatval($votes) . "</span>";
        }

    } else {

        $stars = null;

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= round($votes)) {
                $stars .= ' ' . svg_icon('fas fa-star' . $editor_star);
            } else {
                $stars .= ' ' . svg_icon('fas fa-star');
            }
        }

        return $stars;

    }

}

function query_cache($query, $cache_id, $enabled)
{
    if ($enabled == '1') {
        $query = Cache::rememberForever($cache_id, function () {
            return $query;
        });
    } else {
        $query = $query;
    }

    return $query;
}

/** Translation Check */
function translation_check($data, $column, $type, $table, $id)
{
    foreach ($data as $lang_id => $content) {
        $translation_check = DB::table($table)->where('lang_id', $lang_id)->where($type, $id)->first();
        if ($translation_check == null) {
            if ($content != null) {
                DB::table($table)->insert(
                    [
                        $type => $id,
                        'lang_id' => $lang_id,
                        $column => $content,
                        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    ]
                );
            }
        } else {

            $currentData = DB::table($table)->where('lang_id', $lang_id)->where($type, $id)->value($column);

            if ($currentData !== $content) {
                DB::table($table)
                    ->where('lang_id', $lang_id)
                    ->where($type, $id)
                    ->update([
                        $column => $content,
                        'updated_at' => now(),
                    ]);
            }

        }
    }
}

/** Reading Time Calculator */
function reading_time($text = "", $word_per_second = 2)
{
    $word_count = count(explode(" ", $text));

    $reading_time = round($word_count / $word_per_second);

    if ($reading_time < 60) {
        $second = $reading_time;
        return "$second " . __('general.sec');
    } else if ($reading_time < 3600) {
        $minute = ceil($reading_time / 60);
        return "$minute " . __('general.min');
    } else {
        $hour = floor($reading_time / 3600);
        return "$hour " . __('general.hour');
    }
}

function s3_switch($file_name, $type = 0)
{
    $driver = env('FILESYSTEM_DRIVER');

    $folders = array('images', 'images/news', 'images/topics', 'images/sliders', 'images/categories', 'images/platforms', 'screenshots', 'images/submissions');

    if ($driver == 's3') {
        return Storage::disk('s3')->url($folders[$type] . '/' . $file_name);
    } elseif ($driver == 'wasabi') {
        $wasabi_link = Storage::disk('wasabi')->url($folders[$type] . '/' . $file_name);
        $was_bunny = env('WAS_BUNNY');
        if ($was_bunny == '1') {
            $was_bunny_zone = env('WAS_BUNNY_ZONE');
            $bunny_link = str_replace("s3.wasabisys.com", "b-cdn.net", $wasabi_link);
            $bunny_link = str_replace(env('WAS_BUCKET'), env('WAS_BUNNY_ZONE'), $bunny_link);
            return $bunny_link;
        } else {
            return $wasabi_link;
        }
    } else {
        return asset($folders[$type] . '/' . $file_name);
    }
}

function file_upload($file)
{
    $driver = env('FILESYSTEM_DRIVER');

    $file_name = time() . '.' . $file->getClientOriginalExtension();

    if ($driver == 's3') {
        Storage::disk('s3')->put('/files/' . $file_name, file_get_contents($file), 'public');
        return Storage::disk('s3')->url("files/$file_name");
    } else {
        $file->move(public_path('/files'), $file_name);
        $file_name = asset('/files') . '/' . $file_name;
        return $file_name;
    }
}

function image_upload($image, $width, $height, $old_file, $quality, $extension, $type = 0)
{
    $driver = env('FILESYSTEM_DRIVER');

    $folders = array('images', 'images/news', 'images/topics', 'images/sliders', 'images/categories', 'images/platforms', 'screenshots', 'images/submissions');

    if ($extension == '1') {
        $file_name = time() . '.webp';
        $format = 'webp';
    } else {
        $file_name = time() . '.' . $image->getClientOriginalExtension();
        $format = $image->getClientOriginalExtension();
    }

    if ($driver == 's3') {
        $imageFile = \Image::make($image)->resize($width, $height)->stream($format, $quality);
        Storage::disk('s3')->put('/' . $folders[$type] . '/' . $file_name, $imageFile, 'public');

        if (!empty($old_file)) {
            Storage::disk('s3')->delete('/' . $folders[$type] . '/' . $old_file);
        }

    } elseif ($driver == 'wasabi') {
        $imageFile = \Image::make($image)->resize($width, $height)->stream($format, $quality);
        Storage::disk('wasabi')->put('/' . $folders[$type] . '/' . $file_name, $imageFile, 'public');

        if (!empty($old_file)) {
            Storage::disk('wasabi')->delete('/' . $folders[$type] . '/' . $old_file);
        }

    } else {
        Image::make($image)->resize($width, $height)->save(public_path() . '/' . $folders[$type] . '/' . $file_name, $quality);

        if (!empty($old_file)) {
            if (file_exists(public_path() . '/' . $folders[$type] . '/' . $old_file)) {
                unlink(public_path() . '/' . $folders[$type] . '/' . $old_file);
            }
        }

    }

    return $file_name;

}

function image_delete($image, $type = 0)
{
    $driver = env('FILESYSTEM_DRIVER');

    $folders = array('images', 'images/news', 'images/topics', 'images/sliders', 'images/categories', 'images/platforms', 'screenshots', 'images/submissions');

    //  Remove old image file
    if ($driver == 's3') {
        Storage::disk('s3')->delete('/' . $folders[$type] . '/' . $image);

    } else {

        if (file_exists(public_path() . '/' . $folders[$type] . '/' . $image)) {
            unlink(public_path() . '/' . $folders[$type] . '/' . $image);
        }
    }
}

function push_assets()
{
    $driver = env('FILESYSTEM_DRIVER');

    if ($driver == 's3') {
        Storage::disk('s3')->put('/images/logo.png', file_get_contents(asset('/images/logo.png')), 'public');
        Storage::disk('s3')->put('/images/pixel.png', file_get_contents(asset('/images/pixel.png')), 'public');
        Storage::disk('s3')->put('/images/favicon.png', file_get_contents(asset('/images/favicon.png')), 'public');
        Storage::disk('s3')->put('/images/no_image.png', file_get_contents(asset('/images/no_image.png')), 'public');
        Storage::disk('s3')->put('/images/default_share_image.png', file_get_contents(asset('/images/default_share_image.png')), 'public');
    }
    if ($driver == 'wasabi') {
        $imageFile = \Image::make($image)->resize($width, $height)->stream($format, $quality);
        Storage::disk('wasabi')->put('/images/logo.png', file_get_contents(asset('/images/logo.png')), 'public');
        Storage::disk('wasabi')->put('/images/pixel.png', file_get_contents(asset('/images/pixel.png')), 'public');
        Storage::disk('wasabi')->put('/images/favicon.png', file_get_contents(asset('/images/favicon.png')), 'public');
        Storage::disk('wasabi')->put('/images/no_image.png', file_get_contents(asset('/images/no_image.png')), 'public');
        Storage::disk('wasabi')->put('/images/default_share_image.png', file_get_contents(asset('/images/default_share_image.png')), 'public');
    }

}

function pure_cache($name, $site_language, $site_language_code, $language_id)
{
    // Editor's Choice
    if ($name == 'editors_choice') {

        $editors_choice = Cache::rememberForever("editors_choice_$site_language", function () use ($site_language, $site_language_code, $language_id) {

            if ($site_language == $site_language_code) {
                return Application::select('title', 'slug', 'image', 'developer', 'votes')->where('editors_choice', 1)->orderBy('id', 'desc')->limit(10)->get();
            } else {
                return Application::
                    leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                    ->select('applications.slug', 'applications.image', 'applications.title as main_title', 'app_translations.title', 'applications.developer', 'applications.votes')
                    ->where('app_translations.lang_id', $language_id)
                    ->where('editors_choice', '=', '1')
                    ->orderBy('applications.id', 'desc')->limit(10)->get();
            }

        });

        return $editors_choice;

    }

    // New Apps
    if ($name == 'new_apps') {

        $new_apps = Cache::rememberForever("new_apps_$site_language", function () use ($site_language, $site_language_code, $language_id) {

            if ($site_language == $site_language_code) {
                return Application::select('slug', 'image', 'title', 'developer', 'created_at')->orderBy('id', 'desc')->limit(12)->get();
            } else {
                return Application::
                    leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                    ->select('slug', 'image', 'applications.title as main_title', 'app_translations.title', 'developer', 'applications.created_at')
                    ->where('app_translations.lang_id', $language_id)
                    ->orderBy('applications.id', 'desc')->limit(12)->get();
            }

        });

        return $new_apps;

    }

    // Recently Updated Apps
    if ($name == 'recently_updated_apps') {

        $recently_updated_apps = Cache::rememberForever("recently_updated_apps_$site_language", function () use ($site_language, $site_language_code, $language_id) {

            if ($site_language == $site_language_code) {
                return Application::select('slug', 'image', 'title', 'developer', 'updated_at')->whereColumn('created_at', '!=', 'updated_at')->orderBy('updated_at', 'desc')->limit(9)->get();
            } else {
                return Application::
                    leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                    ->select('slug', 'image', 'applications.title as main_title', 'app_translations.title', 'developer', 'applications.created_at')
                    ->where('app_translations.lang_id', $language_id)
                    ->whereColumn('applications.created_at', '!=', 'applications.updated_at')
                    ->orderBy('applications.updated_at', 'desc')->limit(9)->get();
            }

        });

        return $recently_updated_apps;

    }

    // Featured Apps
    if ($name == 'featured_apps') {

        $featured_apps = Cache::rememberForever("featured_apps_$site_language", function () use ($site_language, $site_language_code, $language_id) {

            if ($site_language == $site_language_code) {
                return Application::select('title', 'slug', 'image')->where('featured', 1)->orderBy('applications.id', 'desc')->limit(9)->get();
            } else {
                return Application::
                    leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                    ->select('app_translations.title', 'applications.title as main_title', 'slug', 'image')
                    ->where('featured', 1)
                    ->where('app_translations.lang_id', $language_id)
                    ->orderBy('applications.id', 'desc')->limit(9)->get();
            }

        });

        return $featured_apps;

    }

    // Must-Have Apps
    if ($name == 'must_have_apps') {

        $must_have_apps = Cache::rememberForever("must_have_apps_$site_language", function () use ($site_language, $site_language_code, $language_id) {

            if ($site_language == $site_language_code) {
                return Application::select('slug', 'image', 'title', 'developer', 'created_at')->where('must_have', 1)->orderBy('applications.id', 'desc')->limit(12)->get();
            } else {
                return Application::
                    leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                    ->select('slug', 'image', 'applications.title as main_title', 'app_translations.title', 'developer', 'applications.created_at')
                    ->where('must_have', 1)
                    ->where('app_translations.lang_id', $language_id)
                    ->orderBy('applications.id', 'desc')->limit(12)->get();
            }

        });

        return $must_have_apps;
    }

    // Popular Apps in Last 24 Hours
    if ($name == 'apps_24_hours') {

        $apps_24_hours = Cache::rememberForever("apps_24_hours_$site_language", function () use ($site_language, $site_language_code, $language_id) {

            if ($site_language == $site_language_code) {
                return Application::select('title', 'slug', 'image', 'developer', 'votes')->orderBy('hits', 'desc')->limit(15)->get();
            } else {
                return Application::
                    leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                    ->select('applications.slug', 'applications.title as main_title', 'app_translations.title', 'applications.image', 'applications.developer', 'applications.votes')
                    ->where('app_translations.lang_id', $language_id)
                    ->orderBy('applications.hits', 'desc')->limit(15)->get();
            }

        });

        return $apps_24_hours;
    }

    // Popular Apps
    if ($name == 'popular_apps') {

        $popular_apps = Cache::rememberForever("popular_apps_$site_language", function () use ($site_language, $site_language_code, $language_id) {

            if ($site_language == $site_language_code) {
                $popular_apps = Application::select('title', 'slug', 'image', 'developer', 'votes')->orderBy('page_views', 'desc')->limit(10)->get();
            } else {
                $popular_apps = Application::
                    leftJoin('app_translations', 'applications.id', '=', 'app_translations.app_id')
                    ->select('applications.slug', 'applications.image', 'applications.title as main_title', 'app_translations.title', 'applications.developer', 'applications.votes')
                    ->where('app_translations.lang_id', $language_id)
                    ->orderBy('applications.page_views', 'desc')->limit(10)->get();
            }
            $data_array = [];
            foreach ($popular_apps as $row) {
                array_push($data_array, [$row->title, $row->slug, $row->image, $row->developer, $row->votes, $row->main_title]);
            }

            return $data_array;
        });

        return $popular_apps;
    }

    // Latest Topics
    if ($name == 'latest_topics') {

        $latest_topics = Cache::rememberForever("latest_topics_$site_language", function () use ($site_language, $site_language_code, $language_id) {

            if ($site_language == $site_language_code) {
                return Topic::select('slug', 'image', 'title')->orderBy('id', 'desc')->limit(3)->get();
            } else {
                return Topic::
                    leftJoin('topic_translations', 'topics.id', '=', 'topic_translations.topic_id')
                    ->select('topics.slug', 'topics.image', 'topic_translations.title')
                    ->where('topic_translations.lang_id', $language_id)
                    ->orderBy('topics.id', 'desc')->limit(3)->get();
            }

        });

        return $latest_topics;

    }

    // Latest News
    if ($name == 'latest_news') {

        $latest_news = Cache::rememberForever("latest_news_$site_language", function () use ($site_language, $site_language_code, $language_id) {

            if ($site_language == $site_language_code) {
                return News::select('slug', 'image', 'title')->orderBy('id', 'desc')->limit(2)->get();
            } else {
                return News::
                    leftJoin('news_translations', 'news.id', '=', 'news_translations.news_id')
                    ->select('news.slug', 'news.image', 'news_translations.title')
                    ->where('news_translations.lang_id', $language_id)
                    ->orderBy('news.id', 'desc')->limit(2)->get();
            }

        });

        return $latest_news;
    }

    // Sliders
    if ($name == 'sliders') {

        $sliders = Cache::rememberForever("sliders_$site_language", function () use ($site_language, $site_language_code, $language_id) {

            if ($site_language == $site_language_code) {
                return DB::table('sliders')
                    ->leftJoin('applications', 'sliders.link', '=', 'applications.id')
                    ->select('sliders.*', 'applications.slug', 'applications.image as logo', 'applications.votes')
                    ->where('active', 1)
                    ->orderBy('sliders.id', 'desc')
                    ->get();
            } else {
                return DB::table('sliders')
                    ->leftJoin('applications', 'sliders.link', '=', 'applications.id')
                    ->leftJoin('app_translations', 'sliders.link', '=', 'app_translations.app_id')
                    ->leftJoin('slider_translations', 'sliders.id', '=', 'slider_translations.slider_id')
                    ->select('sliders.image', 'applications.slug', 'sliders.title as main_title', 'slider_translations.title', 'applications.image as logo', 'applications.votes')
                    ->where('active', 1)
                    ->where('app_translations.lang_id', $language_id)
                    ->where('slider_translations.lang_id', $language_id)
                    ->orderBy('sliders.id', 'desc')
                    ->get();
            }

        });

        return $sliders;
    }

    // Categories
    if ($name == 'categories') {

        $categories = Cache::rememberForever("categories_$site_language", function () use ($site_language, $site_language_code, $language_id) {

            if ($site_language == $site_language_code) {
                return Category::orderBy('sort', 'ASC')->get();
            } else {
                return Category::
                    leftJoin('category_translations', 'categories.id', '=', 'category_translations.cat_id')
                    ->select('categories.id', 'category_translations.title', 'categories.slug', 'categories.navbar', 'categories.right_column', 'categories.footer', 'categories.home_page', 'categories.fa_icon', 'categories.type', 'categories.image', 'categories.id')
                    ->where('lang_id', $language_id)
                    ->orderBy('categories.sort', 'ASC')
                    ->get();
            }

        });

        return $categories;
    }

    // Platforms
    if ($name == 'platforms') {

        $platforms = Cache::rememberForever("platforms_$site_language", function () use ($site_language, $site_language_code, $language_id) {

            if ($site_language == $site_language_code) {
                return Platform::orderBy('sort', 'ASC')->get();
            } else {
                return Platform::
                    leftJoin('platform_translations', 'platforms.id', '=', 'platform_translations.platform_id')
                    ->select('platforms.id', 'platform_translations.title', 'platforms.slug', 'platforms.navbar', 'platforms.right_column', 'platforms.footer', 'platforms.fa_icon', 'platforms.image', 'platforms.id')
                    ->where('lang_id', $language_id)
                    ->orderBy('platforms.sort', 'ASC')
                    ->get();
            }

            $data_array = [];
            foreach ($platforms as $row) {
                array_push($data_array, [$row->title, $row->slug, $row->navbar, $row->right_column, $row->footer, $row->fa_icon, $row->image, $row->id]);
            }

            return $data_array;
        });

        return $platforms;
    }

    // Footer Pages
    if ($name == 'footer_pages') {

        $footer_pages = Cache::rememberForever("footer_pages_$site_language", function () use ($site_language, $site_language_code, $language_id) {

            if ($site_language == $site_language_code) {
                $footer_pages = Page::select('id', 'slug', 'title')->where('footer', '1')->orderBy('sort', 'ASC')->get();
            } else {
                $footer_pages = Page::
                    leftJoin('page_translations', 'pages.id', '=', 'page_translations.page_id')
                    ->select('pages.id', 'pages.slug', 'page_translations.title')
                    ->where('lang_id', $language_id)
                    ->where('pages.footer', '1')
                    ->orderBy('pages.sort', 'ASC')
                    ->get();
            }

            $data_array = [];
            foreach ($footer_pages as $row) {
                array_push($data_array, [$row->title, $row->slug]);
            }

            return $data_array;
        });

        return $footer_pages;
    }

}
