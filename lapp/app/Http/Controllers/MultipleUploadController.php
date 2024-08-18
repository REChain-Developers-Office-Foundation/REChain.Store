<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class MultipleUploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Site Settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $setting_name = $setting->name;
            $this->$setting_name = $setting->value;
        }
    }

    /** Index */
    public function index()
    {
        return view('adminlte::multiple_file_upload');
    }

    /** Delete */
    public function delete(Request $request)
    {

        if (!empty($request->image_name)) {
            image_delete($request->image_name, 6);
        }

        $screenshot_query = DB::table('applications')->where('id', '=', $request->app_id)->pluck('screenshots');

        $screenshot_query = explode(',', $screenshot_query[0]);

        $screenshots = array_diff($screenshot_query, array($request->image_name));

        $comma_separated = implode(",", $screenshots);
        DB::table('applications')->where('id', $request->app_id)->update(array('screenshots' => $comma_separated));

        // Clear cache
        Cache::flush();
    }

    /** Upload */
    public function upload(Request $request)
    {
        $this->validate($request, [
            'file' => 'required',
            'file.*' => 'image',
        ]);

        $driver = env('FILESYSTEM_DRIVER');

        $image_code = '';
        $images = $request->file('file');
        foreach ($images as $image) {

            if ($this->save_as_webp == '1') {
                $new_name = time() . rand(1111111, 9999999) . '.webp';
                $format = 'webp';
            } else {
                $new_name = time() . '.' . $image->getClientOriginalExtension();
                $format = $image->getClientOriginalExtension();
            }

            $location = public_path('screenshots/' . $new_name);

            if ($driver == 's3') {
                $imageFile = \Image::make($image)->heighten('400')->stream($format, $this->image_quality);
                Storage::disk('s3')->put('screenshots/' . $new_name, $imageFile, 'public');
            } else {
                Image::make($image)->heighten(400)->save($location);
            }

            $image_code .= "$new_name,";
            $sql_query = "UPDATE applications set screenshots = IF(`screenshots` = '','$new_name',CONCAT(screenshots, ',', '$new_name')) WHERE find_in_set('$new_name',screenshots) = 0 AND id = $request->app_id";
            $result = DB::update($sql_query);
        }

        $screenshot_query = DB::table('applications')->where('id', '=', $request->app_id)->pluck('screenshots');

        $mysplit = explode(',', $screenshot_query[0]);
        $screenshot_data = array_reverse($mysplit);

        $image_code_s = '';
        foreach ($screenshot_data as $screenshot) {
            $file_name = s3_switch($screenshot);
            $image_code_s .= '<div class="col-md-2 mb-1 text-center"><a href="' . s3_switch($screenshot, 6) . '" data-toggle="lightbox"><img src="' . s3_switch($screenshot, 6) . '" class="img-thumbnail mt-3" /></a><button type="button" data-name="' . $screenshot . '" data-app-id="' . $request->app_id . '" class="btn btn-danger mt-3 remove_screenshot">Delete</button></div>';
        }

        $output = array(
            'success' => true,
            'image' => $image_code_s,
        );

        // Clear cache
        Cache::flush();

        return response()->json($output);
    }

}
