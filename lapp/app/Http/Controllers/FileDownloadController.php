<?php

namespace App\Http\Controllers;

use DB;

class FileDownloadController extends Controller
{

    public function __construct()
    {
        //
    }

    /** Show */
    public function show()
    {
        // Retrieve application details
        $download_query = DB::table('versions')->where('id', request()->id)->first();

        // Return 404 page if application not found
        if ($download_query == null) {
            abort(404);
        }

        // Update download count
        DB::table('versions')->where('id', request()->id)->increment('counter');

        // Redirect to file or page
        return redirect()->away($download_query->url);
    }

}
