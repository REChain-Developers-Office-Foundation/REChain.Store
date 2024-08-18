<?php

namespace App\Http\Controllers;

use App\Mail\Contact;
use DB;
use Illuminate\Http\Request;
use Mail;
use Purifier;
use Response;
use Validator;

class ContactFormController extends Controller
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
        $rules = array(
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required|min:25|max:1000',
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
                $name = $request->get('name');
                $email = $request->get('email');
                $form_subject = $request->get('subject');
                $form_message = nl2br($request->get('message'));
                $form_message = Purifier::clean($form_message, 'titles');
                $ip_address = $request->ip();
                $site_title = $this->site_title;
                $admin_email = $this->admin_email;
                $mail_from = $_ENV['MAIL_FROM_ADDRESS'];

                Mail::to($admin_email)->send(new Contact($name, $email, $form_subject, $form_message, $ip_address, $site_title, $mail_from));

                // Return success message
                return '<div class="alert alert-success mt-3 show" role="alert">' . __('general.contact_form_thanks') . '</div>';
            }

        }
    }
}
