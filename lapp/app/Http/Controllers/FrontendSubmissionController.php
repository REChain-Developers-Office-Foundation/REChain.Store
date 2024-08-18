<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Response;

class FrontendSubmissionController extends Controller
{

    public function __construct()
    {

        // Site Settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $setting_name = $setting->name;
            $this->$setting_name = $setting->value;
            $settings[$setting->name] = $setting->value;
        }

    }

    /** Store */
    public function store(Request $request)
    {

                if ($request->hasFile('image') != null) {
    $this->validate($request, [
        ]);
}

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
            'platform' => 'required',
            'developer' => 'required',
            'version' => 'required',
            'url' => 'required|url',
        ]);
        
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
        
        $file_name = '';

        // Check if the image has been uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_name = image_upload($image, '200', '200', '', $this->image_quality, $this->save_as_webp, 7);
        }

        $detailed_desc = request()->detailed_description;
        $detailed_desc = nl2br($detailed_desc);

        $client_ip = $request->ip();

        DB::table('submissions')->insert(
            [
                'name' => request()->name,
                'email' => request()->email,
                'title' => request()->title,
                'description' => request()->description,
                'category' => request()->category,
                'platform' => request()->platform,
                'developer' => request()->developer,
                'url' => request()->url,
                'license' => request()->license,
                'file_size' => request()->file_size,
                'version' => request()->version,
                'image' => $file_name,
                'details' => $detailed_desc,
                'ip' => $client_ip,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ]
        );

        return '<div class="alert alert-success mt-2 mb-2 show" role="alert">' . __('general.submission_thanks') . '</div>';
    }

}
