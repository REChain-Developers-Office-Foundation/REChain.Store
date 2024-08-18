<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Response;

class VoteController extends Controller
{

    public function __construct()
    {
        //
    }

    /** Show */
    public function vote(Request $request)
    {

        $app_id = $request->get('app_id');
        $direction = $request->get('direction');

        if ($direction != 'up' && $direction != 'down') {
            exit;
        }

        if ($direction == 'up') {
            $vote_direction = '1';
            $vote_data = 'up_votes';
        } else {
            $vote_direction = '2';
            $vote_data = 'down_votes';
        }

        // Get user IP address
        $client_ip = $request->ip();

        // Check if user voted for the list item
        $vote_query = DB::table('votes')->where([['ip', '=', $client_ip], ['app_id', '=', $app_id]])->get();

        if (count($vote_query) == 0) {

            // Insert vote to records table
            DB::table('votes')->insert(['app_id' => $app_id, 'vote' => $vote_direction, 'ip' => $client_ip]);

            // Update total voter for the list
            DB::table('applications')->where('id', $app_id)->increment($vote_data);

            // Get up to date vote count
            $app_query = DB::table('applications')->where('id', $app_id)->first();

            // Return result
            return Response::json(['success' => true, 'vote' => number_format($app_query->$vote_data)]);
        } else {
            return Response::json(['success' => false]);
        }

    }
}
