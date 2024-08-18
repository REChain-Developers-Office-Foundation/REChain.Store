<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FrontendCommentController extends Controller
{

    public function __construct()
    {

        // Site Settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $settings[$setting->name] = $setting->value;
        }

        $this->auto_comment_approval = $settings['auto_comment_approval'];
    }

    /** Show */
    public function store(Request $request)
    {

        $this->validate($request, [
            'content_id' => 'required',
            'name' => 'required',
            'title' => 'required',
            'email' => 'required',
            'comment' => 'required',
            'type' => 'required',
            'user_rating' => 'nullable',
        ]);

        if ($this->auto_comment_approval == '1') {
            $approval = '1';

            if (request()->type == '1') {
                // Get average rating
                $rating_query = DB::table('applications')->where('id', request()->content_id)->first();
                $votes = $rating_query->votes;

                $total_votes = $rating_query->total_votes;

                $new_average = ($votes * $total_votes + request()->user_rating) / ($total_votes + 1);

                // Update total votes and votes count
                DB::table('applications')->where('id', request()->content_id)->update(array('votes' => $new_average));
                DB::table('applications')->where('id', request()->content_id)->increment('total_votes');
            }

            // Clear cache
            Cache::flush();

        } else {
            $approval = '0';
        }

        $client_ip = $request->ip();

        if (isset(request()->user_rating)) {
            $rating = request()->user_rating;
        } else {
            $rating = 0;
        }

        DB::table('comments')->insert(
            [
                'content_id' => request()->content_id,
                'name' => request()->name,
                'title' => request()->title,
                'email' => request()->email,
                'comment' => request()->comment,
                'type' => request()->type,
                'rating' => $rating,
                'approval' => $approval,
                'ip' => $client_ip,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ]
        );

        return '<div class="alert alert-success mt-3 mb-2 show" role="alert">' . __('general.comment_thanks') . '</div>';
    }

}
