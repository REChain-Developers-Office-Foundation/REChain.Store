<?php
namespace App\Http\Controllers;

use App;
use App\Models\Application;
use App\Models\Category;
use App\Models\Platform;
use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageManagerStatic as Image;
use Redirect;

class GoogleScraperController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // List of categories
        $categories = Category::where('type', '1')->orderBy('title', 'ASC')->get()->pluck('title', 'id');

        // List of Platforms
        $platforms = Platform::orderBy('title', 'ASC')->get()->pluck('title', 'id');

        // Site Settings
        $site_settings = DB::table('settings')->get();
        
        $item_platform = '';

        foreach ($site_settings as $setting) {
            $settings[$setting->name] = $setting->value;
            $setting_name = $setting->name;
            $this->$setting_name = $setting->value;
        }

        foreach ($platforms as $key => $platform) {
            if ($platform == 'Android') {
                $item_platform = $key;
            }
        }

        // Pass data to views
        View::share(['platforms' => $platforms, 'categories' => $categories, 'settings' => $settings, 'item_platform' => $item_platform]);
    }

    /**  Display a listing of the resource. */
    public function index()
    {
        $searchquery = request()->post('term');

        if ($searchquery == null) {
            // If search term is null than use "free" as search term
            return redirect(env('ADMIN_URL').'/google-scraper?term=whatsapp');
        } else {
            $searchquery = request()->post('term');

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
                $app['rating'] = $node->filter('.w2kbF')->text('0.0');

                $ratingNode = $node->filter('.aCy7Gf [aria-label]');
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
                $app['rating'] = preg_replace('#[^0-9\.,]#', '', $node->filter('.TT9eCd')->text());
                $ratingNode = $node->filter('.aCy7Gf');
                $app['price'] = null;

                return $app;
            });

            if (isset($crawler_head[0])) {
                array_unshift($crawler_array, $crawler_head[0]);
            }

            $apps = $crawler_array;

            // Return view
            return view('adminlte::scraper.google', compact('apps'));
        }
    }

    /** Show */
    public function show($id)
    {
        // Get app details from Google Play Store

        $gplay = new \Nelexa\GPlay\GPlayApps($defaultLocale = $this->google_play_default_language, $defaultCountry = $this->google_play_default_country);
        $app = $gplay->getAppInfo($id);

        // Unfortunately, publishers do not use a standard format for description, so we need to format it
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

        $crawler_categories_google = json_decode($this->crawler_categories_google);
        $item_category = null;
        foreach ($crawler_categories_google as $value => $key) {
            if ($app->getCategory()->getId() == $value) {
                $item_category = $key;
            }
        }

        // Remove if first line of detailed description contains <br>
        $detailed_description = str_replace("\n", '<br>', $detailed_description);
        $detailed_description = @preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $detailed_description);

        // App Price
        if (is_null($app->getPriceText())) {
            $app_price = __('admin.free');
        } else {
            $app_price =
            $app->getPriceText();
        }

        // Return view
        return view('adminlte::scraper.google_create', compact('app'))->with('description', $description)->with('detailed_description', $detailed_description)->with('item_category', $item_category)->with('app_price', $app_price);
    }

    /** Store */
    public function store(Request $request)
    {

        $slug_check = Application::where('slug', $request->get('slug'))->first();

        if ($slug_check != null) {
            return Redirect::back()->withErrors(__('admin.slug_in_use'));
        }

        $app = new Application;

        // Check if the file has been uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $original_name = $file->getClientOriginalName();
            $request->file->move(public_path('/files'), $original_name);
            $app->url = asset('/files') . '/' . $original_name;
            $request->merge([
                'url' => $app->url,
            ]);
        }

        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required|max:755',
            'custom_title' => 'nullable|max:255',
            'custom_description' => 'nullable|max:255',
            'category' => 'required',
            'platform' => 'required',
            'counter' => 'required',
            'url' => 'required',
            'type' => 'required',
        ]);

        // Check if the picture has been uploaded
        if ($request->hasFile('different_image')) {
            $image = $request->file('different_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images/' . $filename);
            Image::make($image)->resize(200, 200)->save($location);
            $app->image = $filename;

        } else {

            $image = $request->get('image');
            $path = $image;

            if ($this->save_as_webp == '1') {
                $file_name = time() . '.webp';
            } else {
                $file_name = rand(1111111, 9999199) . '.png';
            }
            // Download image from Google Play Store to temp folder
            Image::make($path)->save(public_path('images/temp/' . $file_name));
            $tempimage = "images/temp/$file_name";
            $location = "images/$file_name";

            Image::make($tempimage)->resize(200, 200)->save($location, $this->image_quality); // Resize image
            $app->image = $file_name;

            unlink($tempimage); // Unlink temp image
        }

        $max_screenshots = $this->screenshot_count;

        $screenshots = $request->get('screenshots');

        if ($max_screenshots != '0') {

            $explode_screenshots = explode(",", $screenshots);
            $total_screenshots = count($explode_screenshots);

            if ($total_screenshots >= $max_screenshots) {
                $total_screenshots = $max_screenshots;
            }

            $ss_array = array();

            for ($x = 0; $x <= $total_screenshots - 1; $x++) {

                if ($this->save_as_webp == '1') {
                    $ss_filename = floor(microtime(true) * 1000) . rand(1111111, 9999999) . '.webp';
                } else {
                    $ss_filename = floor(microtime(true) * 1000) . rand(1111111, 9999999) . '.jpg';
                }

                // Download screenshots from Google Play Store to temp folder
                Image::make($explode_screenshots[$x])->save(public_path('screenshots/temp/' . $ss_filename));
                $ss_tempimage = "screenshots/temp/$ss_filename";
                $ss_location = "screenshots/$ss_filename";

                Image::make($ss_tempimage)->save($ss_location, $this->image_quality); // Save image
                unlink($ss_tempimage); // Unlink temp image
                array_push($ss_array, $ss_filename);
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

        if ($request->get('featured') == null) {
            $app->featured = '0';
        } else {
            $app->featured = '1';
        }

        if ($request->get('pinned') == null) {
            $app->pinned = '0';
        } else {
            $app->pinned = '1';
        }

        if ($request->get('editors_choice') == null) {
            $app->editors_choice = '0';
        } else {
            $app->editors_choice = '1';
        }

        if ($request->get('must_have') == null) {
            $app->must_have = '0';
        } else {
            $app->must_have = '1';
        }

        $app->slug = $request->get('slug');
        $app->title = $request->get('title');
        $app->description = $request->get('description');
        $app->custom_title = $request->get('custom_title');
        $app->custom_description = $request->get('custom_description');
        $app->counter = $request->get('counter');
        $app->category = $request->get('category');
        $app->file_size = $request->get('file_size');
        $app->url = $request->get('url');
        $app->buy_url = $request->get('buy_url');
        $app->license = $request->get('license');
        $app->developer = $request->get('developer');
        $app->details = $request->get('details');
        $app->platform = $request->get('platform');
        $app->screenshots = $screenshots_list;
        $app->type = $request->get('type');
        $app->package_name = $request->get('package_name');

        $app->save();

        $tags = explode(",", $request->get('tags'));
        $app->tag($tags);

        if ($request->get('slug') == null) {
            $app->slug = null;
            $app->update(['title' => $app->title]);
        }

        // Clear cache
        Cache::flush();

        // Redirect to application edit page
        return redirect()
            ->route('apps.edit', $app->id)
            ->with('success', __('admin.data_added'));
    }

}
