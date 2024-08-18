<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpenaiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Site Settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $settings[$setting->name] = $setting->value;
            $setting_name = $setting->name;
            $this->$setting_name = $setting->value;
        }

    }

    /** Regenerate */
    public function regenerate(Request $request)
    {
        $content = request()->content;

        $apiUrl = 'https://api.openai.com/v1/engines/text-davinci-003/completions';
        $apiKey = env('OPENAI_KEY');

        $openai_command = str_replace("%content%", "$content", $this->openai_regenerate_command);

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

        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            $decodedResponse = $response;
        }

        curl_close($ch);

        return json_decode($response);
    }

    /** Generate */
    public function generate(Request $request)
    {
        $content = request()->content;

        $apiUrl = 'https://api.openai.com/v1/engines/text-davinci-003/completions';
        $apiKey = env('OPENAI_KEY');

        $data = array(
            'prompt' => $content,
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

        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            $decodedResponse = $response;
        }

        curl_close($ch);

        return json_decode($response);
    }

}
