<?php

use Illuminate\Support\Facades\Route;

$settings = DB::table('settings')->whereIn('name', ['site_language', 'root_language'])->pluck('value', 'name');
$languages = DB::table('translations')->get();

foreach ($languages as $language) {
    $lang_codes[] = $language->code;
    $lang_id[$language->code] = $language->id;
}

if (in_array(request()->segment(1), $lang_codes)) {
    $site_language_prefix = request()->segment(1);
    $site_language_code = request()->segment(1);
} else {
    $site_language_prefix = null;
    $site_language_code = $settings['site_language'];
}

$language_id = $lang_id[$site_language_code];

Route::group(['prefix' => $site_language_prefix, 'middleware' => ['language:' . $site_language_code . '']], function () use ($language_id) {
$settings = DB::table('settings')->where('language', $language_id)->pluck('value', 'name');
Route::get('/', 'App\Http\Controllers\SiteController@index');
Route::get("/$settings[app_base]/{slug}", 'App\Http\Controllers\SiteController@app');
Route::get("/$settings[page_base]/{slug}", 'App\Http\Controllers\SiteController@page');
Route::get("/$settings[category_base]/{slug}", 'App\Http\Controllers\SiteController@category');
Route::get("/$settings[platform_base]/{slug}", 'App\Http\Controllers\SiteController@platform');
Route::post('/comment', 'App\Http\Controllers\FrontendCommentController@store');
Route::post('/report', 'App\Http\Controllers\SiteController@report');
Route::post('/json-search', 'App\Http\Controllers\SiteController@json_search');
Route::get("/$settings[tag_base]/{slug}", 'App\Http\Controllers\SiteController@tags');
Route::match(array('GET', 'POST'), '/search', 'App\Http\Controllers\SiteController@search');
Route::get("/$settings[page_base]/{slug}", 'App\Http\Controllers\SiteController@page');
Route::get("/$settings[topic_base]", 'App\Http\Controllers\SiteController@topic');
Route::get("/$settings[topic_base]/{slug}", 'App\Http\Controllers\SiteController@topic_item');
Route::get("/$settings[news_base]/{slug}", 'App\Http\Controllers\SiteController@category_news');
Route::get("/$settings[news_base]", 'App\Http\Controllers\SiteController@all_news');
Route::get("/$settings[read_base]/{slug}", 'App\Http\Controllers\SiteController@news');
Route::get('/new-apps', 'App\Http\Controllers\SiteController@new_apps');
Route::get('/recently-updated-apps', 'App\Http\Controllers\SiteController@recently_updated_apps');
Route::get('/favorites', 'App\Http\Controllers\SiteController@favorites');
Route::get('/browse-history', 'App\Http\Controllers\SiteController@browse_history');
Route::get('/featured-apps', 'App\Http\Controllers\SiteController@featured_apps');
Route::get('/popular-apps', 'App\Http\Controllers\SiteController@popular_apps');
Route::get('/popular-apps-24-hours', 'App\Http\Controllers\SiteController@popular_apps_24_hours');
Route::get('/editors-choice', 'App\Http\Controllers\SiteController@editors_choice');
Route::get('/must-have-apps', 'App\Http\Controllers\SiteController@must_have_apps');
Route::get("/$settings[contact_slug]", 'App\Http\Controllers\SiteController@contact');
Route::post('/contact-form', 'App\Http\Controllers\ContactFormController@store');
Route::get('/redirect/{slug}/{id}', 'App\Http\Controllers\SiteController@redirect');
Route::get('/download/{id}', 'App\Http\Controllers\FileDownloadController@show');
Route::get('/submit-app', 'App\Http\Controllers\SiteController@submission');
Route::post('/submission', 'App\Http\Controllers\FrontendSubmissionController@store');
Route::post('/vote', 'App\Http\Controllers\VoteController@vote');
Route::get('/crawler/{slug}', 'App\Http\Controllers\AppCrawlerController@index');
Route::get("/all-categories", 'App\Http\Controllers\SiteController@all_categories');
Route::get("/all-platforms", 'App\Http\Controllers\SiteController@all_platforms');
Route::get('/rss', 'App\Http\Controllers\RSSController@apps');
Route::get('/rss/category/{slug}', 'App\Http\Controllers\RSSController@category_apps');
Route::get('/rss/platform/{slug}', 'App\Http\Controllers\RSSController@platform_apps');
Route::get('/news-rss', 'App\Http\Controllers\RSSController@news');
Route::get('/news-rss/category/{slug}', 'App\Http\Controllers\RSSController@category_news');
});

Route::group(['prefix' => env('ADMIN_URL'), 'middleware' => ['auth', 'isAdmin']], function () {
Route::get('/', [App\Http\Controllers\ApplicationController::class, 'index'])->name('home');
Route::post('/apps/bulk-destroy', 'App\Http\Controllers\ApplicationController@bulk_destory');
Route::resource('/apps', 'App\Http\Controllers\ApplicationController');
Route::resource('/versions', 'App\Http\Controllers\VersionController');
Route::get('/search', 'App\Http\Controllers\SearchController@index');
Route::post('/multiple-file-upload/upload', 'App\Http\Controllers\MultipleUploadController@upload')->name('upload');
Route::post('/multiple-file-upload/delete', 'App\Http\Controllers\MultipleUploadController@delete')->name('delete');
Route::match(array('GET', 'POST'), '/categories/sort', 'App\Http\Controllers\CategoryController@sort');
Route::match(array('GET', 'POST'), '/news-categories/sort', 'App\Http\Controllers\NewsCategoryController@sort');
Route::resource('/categories', 'App\Http\Controllers\CategoryController');
Route::resource('/news-categories', 'App\Http\Controllers\NewsCategoryController');
Route::match(array('GET', 'POST'), '/platforms/sort', 'App\Http\Controllers\PlatformController@sort');
Route::resource('/platforms', 'App\Http\Controllers\PlatformController');
Route::match(array('GET', 'POST'), '/pages/sort', 'App\Http\Controllers\PageController@sort');
Route::resource('/pages', 'App\Http\Controllers\PageController');
Route::resource('/news', 'App\Http\Controllers\NewsController');
Route::resource('/ads', 'App\Http\Controllers\AdController');
Route::get('/account_settings', 'App\Http\Controllers\AccountController@accountsettingsform');
Route::post('/account_settings', 'App\Http\Controllers\AccountController@accountsettings')->name('accountsettings');
Route::resource('/sliders', 'App\Http\Controllers\SliderController');
Route::post('/topics/details', 'App\Http\Controllers\TopicController@details');
Route::resource('/topic', 'App\Http\Controllers\TopicItemController');
Route::resource('/topics', 'App\Http\Controllers\TopicController');
Route::get('/general_settings/clear_cache', 'App\Http\Controllers\SettingController@clear_cache');
Route::get('/general_settings', 'App\Http\Controllers\SettingController@index');
Route::post('/general_settings', 'App\Http\Controllers\SettingController@store');
Route::get('/seo_settings/{id}', 'App\Http\Controllers\SettingController@seo_settings_language');
Route::post('/seo_settings/{id}', 'App\Http\Controllers\SettingController@seo_settings_update');
Route::get('/seo_settings', 'App\Http\Controllers\SettingController@seo_settings');
Route::get('/pwa_settings', 'App\Http\Controllers\SettingController@pwa_settings');
Route::post('/pwa_settings', 'App\Http\Controllers\SettingController@pwa_settings_update');
Route::get('/sitemap_settings', 'App\Http\Controllers\SettingController@sitemap_settings');
Route::post('/sitemap_settings', 'App\Http\Controllers\SettingController@sitemap_settings_update');
Route::get('/openai_settings', 'App\Http\Controllers\SettingController@openai_settings');
Route::post('/openai_settings', 'App\Http\Controllers\SettingController@openai_settings_update');
Route::get('/cdn_settings', 'App\Http\Controllers\SettingController@cdn_settings');
Route::post('/cdn_settings', 'App\Http\Controllers\SettingController@cdn_settings_update');
Route::get('/account_settings', 'App\Http\Controllers\AccountController@accountsettingsform');
Route::post('/account_settings', 'App\Http\Controllers\AccountController@accountsettings')->name('accountsettings');
Route::resource('/reports', 'App\Http\Controllers\ReportController');
Route::resource('/translations', 'App\Http\Controllers\TranslationController');
Route::resource('/error_handling', 'App\Http\Controllers\ErrorHandlingController');
Route::resource('/comments', 'App\Http\Controllers\CommentController');
Route::resource('/google-scraper', 'App\Http\Controllers\GoogleScraperController');
Route::resource('/apple-scraper', 'App\Http\Controllers\AppleScraperController');
Route::resource('/submissions', 'App\Http\Controllers\SubmissionController');
Route::get('/scraper_categories_google', 'App\Http\Controllers\CrawlerController@index_google');
Route::post('/scraper_categories_google', 'App\Http\Controllers\CrawlerController@update_google');
Route::get('/scraper_categories_apple', 'App\Http\Controllers\CrawlerController@index_apple');
Route::post('/scraper_categories_apple', 'App\Http\Controllers\CrawlerController@update_apple');
Route::post('/translate', 'App\Http\Controllers\GoogleTranslateController@translate');
Route::post('/openai-generate', 'App\Http\Controllers\OpenaiController@generate');
Route::post('/openai-regenerate', 'App\Http\Controllers\OpenaiController@regenerate');
});

Route::get(env('ADMIN_LOGIN_URL'), 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post(env('ADMIN_LOGIN_URL'), 'App\Http\Controllers\Auth\LoginController@login');
Route::get('/cronjob/{slug}', 'App\Http\Controllers\SiteController@cronjob');
Route::get('/hourly-cronjob/{slug}', 'App\Http\Controllers\SiteController@hourly_cronjob');
Route::get('/sitemap.xml', 'App\Http\Controllers\SitemapController@index');
Route::get("/{slug}-sitemap.xml", 'App\Http\Controllers\SitemapController@sitemap');

Auth::routes([
    'login' => false,
    'register' => false,
    'reset' => true,
    'verify' => false,
]);

Route::get(env('ADMIN_URL').'/random_key', function () {
    define('AES_256_CBC', 'aes-256-cbc');
    $encryption_key = openssl_random_pseudo_bytes(32);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
    $data = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 32);
    return openssl_encrypt($data, AES_256_CBC, $encryption_key, 0, $iv);
})->middleware('auth');