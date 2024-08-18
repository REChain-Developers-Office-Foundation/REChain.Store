<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Category;
use App\Models\Platform;
use Goutte\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class AppCrawlerController extends Controller
{
    public function __construct()
    {
        // Site Settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $settings[$setting->name] = $setting->value;
            $setting_name = $setting->name;
            $this->$setting_name = $setting->value;
        }

        $allowed_categories = array();

        foreach (json_decode($this->crawler_categories_google) as $crawler_name => $crawler_id) {
            if (!empty($crawler_id)) {
                $allowed_categories[$crawler_name] = $crawler_id;
            }
        }

        $this->allowed_categories = $allowed_categories;
        $this->android_platform_id = $this->submission_platform_google;

    }

    /** Index */
    public function index($slug)
    {
        // Check if cronjob code is valid
        $cronjob_check = DB::table('settings')->where('value', $slug)->orwhere('value', $slug)->first();

        // Return 404 page if code not found
        if ($cronjob_check == null) {
            abort(404);
        }

        if ($this->auto_submission_gps == '1') {

            $now = \Carbon\Carbon::now();

            $driver = env('FILESYSTEM_DRIVER');

            // Google Play Store
            if ($cronjob_check->name == 'submission_cronjob_code_google') {

                // Update last run time
                DB::update("update settings set value = '$now' WHERE name = 'google_cronjob_last_run'");

                $app_query = DB::table('versions')
                    ->leftJoin('applications', 'versions.app_id', '=', 'applications.id')
                    ->inRandomOrder()->where('versions.url', 'LIKE', 'https://play.google.com/store/apps/details?id=%')
                    ->limit(1)
                    ->get();

                if (isset($app_query[0]->title)) {
                    $searchquery = $app_query[0]->title;
                } else {
                    $searchquery = 'WhatsApp';
                }

                $client = new Client();
                $crawler = $client->request('GET', "https://play.google.com/store/search?q=$searchquery&hl=$this->google_play_default_language&gl=$this->google_play_default_country");

                $crawler_array = $crawler->filter('.VfPpkd-aGsRMb')->each(function ($node) {
                    $app = array();
                    $app['url'] = getAbsoluteUrl($node->filter('a.Si6A0c')->attr('href'));
                    $app['id'] = substr($app['url'], strpos($app['url'], '=') + 1);
                    $app['title'] = $node->filter('.ubGTjb')->text();
                    $app['image'] = getAbsoluteUrl($node->filter('.stzEZd')->attr('src'));
                    $app['image'] = str_replace("=s64", "=s128", $app['image']);
                    $app['author'] = $node->filter('.wMUdtb')->text();
                    $ratingNode = $node->filter('.aCy7Gf [aria-label]');
                    $app['rating'] = '0.0';
                    $app['price'] = null;

                    return $app;
                });

                $crawler_head = $crawler->filter('.ipRz4')->each(function ($node) {
                    $app = array();
                    $app['url'] = getAbsoluteUrl($node->filter('a.Qfxief')->attr('href'));
                    $app['id'] = substr($app['url'], strpos($app['url'], '=') + 1);
                    $app['title'] = $node->filter('.vWM94c')->text();
                    $app['image'] = getAbsoluteUrl($node->filter('.KvQfUd')->attr('src'));
                    $app['image'] = str_replace("=s52", "=s128", $app['image']);
                    $app['author'] = $node->filter('.LbQbAe')->text();
                    $ratingNode = $node->filter('.aCy7Gf');
                    $app['rating'] = '0.0';
                    $app['price'] = null;

                    return $app;
                });

                if (isset($crawler_head[0])) {
                    array_unshift($crawler_array, $crawler_head[0]);
                }

                $apps = $crawler_array;

                $count = count($apps);

                $exit = 0;

                for ($x = 0; $x <= $count - 1; $x++) {

                    if ($exit == '1') {
                        // Clear cache
                        Cache::flush();

                        return response()->json([
                            'success' => true,
                            'id' => $last_id,
                            'title' => $app['title'],
                            'category' => $this->allowed_categories[$categories],
                            'platform' => $this->android_platform_id,
                        ], 200);

                    }

                    $app_id = $apps[$x]['id'];

                    $app_count = DB::table('versions')
                        ->leftJoin('applications', 'versions.app_id', '=', 'applications.id')
                        ->inRandomOrder()->where('versions.url', 'LIKE', "%$app_id")
                        ->count();

                    if ($app_count == '0') {
                        $gplay = new \Nelexa\GPlay\GPlayApps($defaultLocale = $this->google_play_default_language, $defaultCountry = $this->google_play_default_country);
                        $app = $gplay->getAppInfo($app_id);

                        $categories = $app->getCategory()->getId();

                        if (array_key_exists($categories, $this->allowed_categories)) {

                            $screenshots = implode(',', $app->getScreenshots());

                            // Explode description into two pieces, first paragraph for description and rest of it for detailed description
                            $first_p = explode("\n", $app->getDescription(), 2);

                            // Set first paragraph as description
                            $description = strip_tags($first_p[0]);

                            // If not empty use second part as detailed description
                            if (isset($first_p[1])) {
                                $detailed_description = $first_p[1];
                            } else {
                                $detailed_description = null;
                            }

                            // Remove if first line of detailed description contains <br>
                            $detailed_description = str_replace("\n", '<br>', $detailed_description);
                            $detailed_description = @preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $detailed_description);

                            if ($this->openai_auto_submission == '1') {
                                $apiUrl = 'https://api.openai.com/v1/engines/text-davinci-003/completions';
                                $apiKey = env('OPENAI_KEY');

                                $openai_command = str_replace("%content%", $first_p[1], $this->openai_auto_regenerate_command);

                                $data = array(
                                    'prompt' => $openai_command,
                                    'max_tokens' => intval($this->openai_max_tokens),
                                    'temperature' => intval($this->openai_temperature),
                                );

                                $headers = array(
                                    'Content-Type: application/json',
                                    'Authorization: Bearer ' . $apiKey,
                                );

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $apiUrl);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                                $response = curl_exec($ch);
                                $response_data = json_decode($response, true);

                                if (curl_errno($ch)) {
                                    echo 'Error: ' . curl_error($ch);
                                } else {
                                    $decodedResponse = $response;
                                }

                                curl_close($ch);

                                $get_response = json_decode($response);

                                if (!isset($response_data["error"])) {
                                    $detailed_description = $get_response->choices[0]->text;
                                }

                            }

                            $max_screenshots = $this->screenshot_count;

                            if ($max_screenshots != '0') {

                                $explode_screenshots = explode(",", $screenshots);
                                $total_screenshots = count($explode_screenshots);

                                if ($total_screenshots >= $max_screenshots) {
                                    $total_screenshots = $max_screenshots;
                                }

                                $ss_array = array();

                                for ($x = 0; $x <= $total_screenshots - 1; $x++) {
                                    if ($this->save_as_webp == '1') {
                                        $new_name = time() . rand(1111111, 9999999) . '.webp';
                                        $format = 'webp';
                                    } else {
                                        $new_name = time() . '.' . $image->getClientOriginalExtension();
                                        $format = $image->getClientOriginalExtension();
                                    }

                                    $location = public_path('screenshots/' . $new_name);

                                    if ($driver == 's3') {
                                        $imageFile = \Image::make($explode_screenshots[$x])->heighten('400')->stream($format, $this->image_quality);
                                        Storage::disk('s3')->put('screenshots/' . $new_name, $imageFile, 'public');
                                    } else {
                                        Image::make($explode_screenshots[$x])->heighten(400)->save($location);
                                    }

                                    array_push($ss_array, $new_name);

                                }

                                $screenshots_list = implode(',', $ss_array);

                            } else {
                                $screenshots_list = '';
                            }

                            if (is_null($app->getPriceText())) {
                                $app_price = __('admin.free');
                            } else {
                                $app_price = $app->getPriceText();
                            }

                            $image = $app->getIcon();

                            if ($this->save_as_webp == '1') {
                                $file_name = time() . rand(1111111, 9999999) . '.webp';
                                $format = 'webp';
                            } else {
                                $file_name = time() . '.' . $image->getClientOriginalExtension();
                                $format = $image->getClientOriginalExtension();
                            }

                            $location = public_path('images/' . $file_name);

                            if ($driver == 's3') {
                                $imageFile = \Image::make($image)->resize(200, 200)->stream($format, $this->image_quality);
                                Storage::disk('s3')->put('images/' . $file_name, $imageFile, 'public');
                            } else {
                                Image::make($image)->resize(200, 200)->save($location);
                            }

                            $last_id = DB::table('applications')->insertGetId(array(
                                'title' => $app->getName(),
                                'description' => $description,
                                'details' => $detailed_description,
                                'image' => $file_name,
                                'license' => $app_price,
                                'developer' => $app->getDeveloper()->getName(),
                                'package_name' => $app->getId(),
                                'type' => '2',
                                'slug' => rand(1111111, 22222222222),
                                'screenshots' => $screenshots_list,
                                'created_at' => \Carbon\Carbon::now(),
                            ));

                            DB::table('versions')->insert(
                                [
                                    'app_id' => $last_id,
                                    'version' => $app->getAppVersion(),
                                    'url' => $app->getUrl(),
                                    'created_at' => \Carbon\Carbon::now(),
                                    'updated_at' => \Carbon\Carbon::now(),
                                ]);

                            DB::table('application_category')->insert(
                                [
                                    'application_id' => $last_id,
                                    'category_id' => $this->allowed_categories[$categories],
                                ]);

                            DB::table('application_platform')->insert(
                                [
                                    'application_id' => $last_id,
                                    'platform_id' => $this->android_platform_id,
                                ]);

                            $app = Application::find($last_id);
                            $app->slug = null;
                            $app->save();
                            $app->update(['title' => $app->title]);

                            $exit = 1;

                        }
                    }
                }
            }
        } else {
            abort(404);
        }

        if ($this->auto_submission_aas == '1') {

            // Apple App Store
            if ($cronjob_check->name == 'submission_cronjob_code_apple') {

                // Update last run time
                DB::update("update settings set value = '$now' WHERE name = 'apple_cronjob_last_run'");

                $app_query = DB::table('versions')
                    ->leftJoin('applications', 'versions.app_id', '=', 'applications.id')
                    ->select('versions.url')
                    ->inRandomOrder()->where('versions.url', 'LIKE', 'https://apps.apple.com/%')
                    ->limit(1)
                    ->get();
                    
                if (isset($app_query[0]->url)) {
                    $searchquery = $app_query[0]->url;
                } else {
                    $searchquery = "https://apps.apple.com/".$this->apple_app_store_country."/app/whatsapp-messenger/id310633997";
                }

                $client = new Client();
                $crawler = $client->request('GET', $searchquery . '?see-all=customers-also-bought-apps');

                $content = $crawler->html();

                $crawler_explode = explode('<div class="we-truncate we-truncate--single-line', $content);
                preg_match('/href="(.*)"/iUs', $crawler_explode[1], $link);

                for ($x = 1; $x <= count($crawler_explode) - 2; $x++) {

                    $app = array();
                    preg_match('/href="(.*)"/iUs', $crawler_explode[$x], $link);

                    $app_count = DB::table('versions')
                        ->leftJoin('applications', 'versions.app_id', '=', 'applications.id')
                        ->inRandomOrder()->where('versions.url', 'LIKE', $link[1])
                        ->count();

                    $exit = 0;

                    if ($app_count == '0') {

                        $client = new Client();
                        $crawler = $client->request('GET', $link[1]);

                        $app = array();

                        preg_match('/<link rel="canonical" href="(.*)"/iUs', $crawler->html(), $link);
                        $app['url'] = $link[1];

                        $app['image'] = $crawler->filter('h2')->text();
                        $app['developer'] = $crawler->filter('h2')->text();
                        $app['file_size'] = $crawler->filter('.information-list__item__definition')->eq(1)->text();
                        $app['category'] = $crawler->filter('.information-list__item__definition')->eq(2)->text();
                        $app['price'] = $crawler->filter('.app-header__list__item--price')->text();
                        $screenshots = $crawler->filter('.we-screenshot-viewer__screenshots-list')->html();

                        $screenshots_explode = explode('<source srcset="', $screenshots);

                        foreach ($screenshots_explode as $screenshot_data) {
                            preg_match('/https:\/\/(.*) /iUs', $screenshot_data, $screenshot);
                            if (isset($screenshot[1])) {
                                if (str_contains($screenshot[1], '.webp')) {
                                    $result_str[] = 'https://' . $screenshot[1];
                                }
                            }
                        }

                        $app['screenshots'] = implode(",", $result_str);

                        preg_match('/"name":"(.*)"/iUs', $crawler->html(), $title);
                        preg_match('/<source srcset="(.*) /iUs', $crawler->filter('.we-artwork')->html(), $image);
                        preg_match('/data-test-bidi>(.*)<\/p>/iUs', $crawler->html(), $content);
                        preg_match('/versionDisplay\\\":\\\"(.*)\\\",\\\"releaseNotes/iUs', $crawler->html(), $version);

                        $app['title'] = $title[1];
                        $app['image'] = $image[1];
                        $app['content'] = $content[1];
                        $app['version'] = $version[1];

                        $app_content = str_replace('\r', '', $app['content']);
                        $app_content = str_replace('\t', '', $app_content);

                        // Explode description into two pieces, first paragraph for description and rest of it for detailed description
                        $first_p = explode('<br><br>', $app_content, 2);

                        // Set first paragraph as description
                        $description = strip_tags($first_p[0]);

                        // If not empty use second part as detailed description
                        if (isset($first_p[1])) {
                            $detailed_description = $first_p[1];
                        } else {
                            $detailed_description = null;
                        }

                        // List of categories
                        $categories = Category::where('type', '1')->orderBy('title', 'ASC')->get()->pluck('title', 'id');

                        $crawler_categories = json_decode($this->crawler_categories_apple);
                        $item_category = null;
                        foreach ($crawler_categories as $value => $key) {
                        $value = str_replace("_", " ", $value);
                        $value = str_replace("&", " & ", $value);
                            if ($app['category'] == $value) {
                                $item_category = $key;
                            }
                        }

                        $item_platform = $this->submission_platform_apple;

                        // Remove if first line of detailed description contains <br>
                        $detailed_description = str_replace('\n', '<br>', $detailed_description);
                        $detailed_description = @preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $detailed_description);

                        if ($this->openai_auto_submission == '1') {
                            $apiUrl = 'https://api.openai.com/v1/engines/text-davinci-003/completions';
                            $apiKey = env('OPENAI_KEY');

                            $openai_command = str_replace("%content%", $first_p[1], $this->openai_auto_regenerate_command);

                            $data = array(
                                'prompt' => $openai_command,
                                'max_tokens' => intval($this->openai_max_tokens),
                                'temperature' => intval($this->openai_temperature),
                            );

                            $headers = array(
                                'Content-Type: application/json',
                                'Authorization: Bearer ' . $apiKey,
                            );

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $apiUrl);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                            $response = curl_exec($ch);
                            $response_data = json_decode($response, true);

                            if (curl_errno($ch)) {
                                echo 'Error: ' . curl_error($ch);
                            } else {
                                $decodedResponse = $response;
                            }

                            curl_close($ch);

                            $get_response = json_decode($response);

                            if (!isset($response_data["error"])) {
                                $detailed_description = $get_response->choices[0]->text;
                            }

                        }

                        $max_screenshots = $this->screenshot_count;

                        if ($max_screenshots != '0') {

                            $explode_screenshots = explode(",", $app['screenshots']);
                            $total_screenshots = count($explode_screenshots);

                            if ($total_screenshots >= $max_screenshots) {
                                $total_screenshots = $max_screenshots;
                            }

                            $ss_array = array();

                            for ($x = 0; $x <= $total_screenshots - 1; $x++) {
                                if ($this->save_as_webp == '1') {
                                    $new_name = time() . rand(1111111, 9999999) . '.webp';
                                    $format = 'webp';
                                } else {
                                    $new_name = time() . '.' . $image->getClientOriginalExtension();
                                    $format = $image->getClientOriginalExtension();
                                }

                                $location = public_path('screenshots/' . $new_name);

                                if ($driver == 's3') {
                                    $imageFile = \Image::make($explode_screenshots[$x])->heighten('400')->stream($format, $this->image_quality);
                                    Storage::disk('s3')->put('screenshots/' . $new_name, $imageFile, 'public');
                                } else {
                                    Image::make($explode_screenshots[$x])->heighten(400)->save($location);
                                }

                                array_push($ss_array, $new_name);

                            }

                            $screenshots_list = implode(',', $ss_array);

                        } else {
                            $screenshots_list = '';
                        }

                        if (is_null($app['price'])) {
                            $app_price = __('admin.free');
                        } else {
                            $app_price = $app['price'];
                        }

                        $image = $app['image'];

                        if ($this->save_as_webp == '1') {
                            $file_name = time() . rand(1111111, 9999999) . '.webp';
                            $format = 'webp';
                        } else {
                            $file_name = time() . '.' . $image->getClientOriginalExtension();
                            $format = $image->getClientOriginalExtension();
                        }

                        $location = public_path('images/' . $file_name);

                        if ($driver == 's3') {
                            $imageFile = \Image::make($image)->resize(200, 200)->stream($format, $this->image_quality);
                            Storage::disk('s3')->put('images/' . $file_name, $imageFile, 'public');
                        } else {
                            Image::make($image)->resize(200, 200)->save($location);
                        }

                        $last_id = DB::table('applications')->insertGetId(array(
                            'title' => $app['title'],
                            'description' => $description,
                            'details' => $detailed_description,
                            'image' => $file_name,
                            'license' => $app_price,
                            'developer' => $app['developer'],
                            'type' => '2',
                            'slug' => rand(1111111, 22222222222),
                            'screenshots' => $screenshots_list,
                            'created_at' => \Carbon\Carbon::now(),
                        ));

                        DB::table('versions')->insert(
                            [
                                'app_id' => $last_id,
                                'version' => $app['version'],
                                'url' => $app['url'],
                                'file_size' => $app['file_size'],
                                'created_at' => \Carbon\Carbon::now(),
                                'updated_at' => \Carbon\Carbon::now(),
                            ]);
                            
                        if(!isset($item_category)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'There is no suitable category for the retrieved application.',
                            'category' => $app['category'],
                        ], 200);
}
                        DB::table('application_category')->insert(
                            [
                                'application_id' => $last_id,
                                'category_id' => $item_category,
                            ]);

                        DB::table('application_platform')->insert(
                            [
                                'application_id' => $last_id,
                                'platform_id' => $item_platform,
                            ]);

                        $app = Application::find($last_id);
                        $app->slug = null;
                        $app->save();
                        $app->update(['title' => $app->title]);

                        $exit = 1;

                    }

                    if ($exit == '1') {
                        // Clear cache
                        Cache::flush();

                        return response()->json([
                            'success' => true,
                            'id' => $last_id,
                            'title' => $app['title'],
                            'category' => $item_category,
                            'platform' => $item_platform,
                        ], 200);

                    }

                }

            }

        } else {
            abort(404);
        }

    }

}
