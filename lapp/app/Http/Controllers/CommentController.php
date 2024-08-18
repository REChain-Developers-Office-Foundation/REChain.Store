<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class CommentController extends Controller
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

        // Languages
        $this->languages = DB::table('translations')->OrderBy('sort', 'ASC')->get();

        foreach ($this->languages as $language) {
            $language_title[$language->code] = $language->id;
            if ($settings['site_language'] == $language->code) {
                $this->language_id = $language_title[$settings['site_language']];
            }
        }

        foreach ($site_settings as $setting) {
            if ($setting->language == $this->language_id) {
                $settings[$setting->name] = $setting->value;
            }
        }

        // Pass data to views
        View::share(['settings' => $settings]);
    }

    /** Index */
    public function index()
    {

        $app_comments = Comment::leftJoin('applications', 'comments.content_id', '=', 'applications.id')
            ->select('comments.*', 'applications.title as app_title', 'applications.slug as slug')
            ->where('comments.type', '1');

        $news_comments = Comment::leftJoin('news', 'comments.content_id', '=', 'news.id')
            ->select('comments.*', 'news.title as app_title', 'news.slug as slug')
            ->where('comments.type', '2');

        // Merge Queries
        $comments = $app_comments->union($news_comments)->orderBy('created_at', 'desc')->paginate(15);

        // Return view
        return view('adminlte::comments.index', compact('comments'));
    }

    /** Update */
    public function update($id)
    {
        $comment = Comment::find($id);
        $user_rating = $comment->rating;
        $content_id = $comment->content_id;

        if ($comment->approval == 1) {
            $comment->update(['approval' => 0]);
        } else {
            $comment->update(['approval' => 1]);
        }

        if ($comment->approval == '1' && $comment->type == '1') {

            // Get average rating
            $rating_query = DB::table('applications')->where('id', $content_id)->first();
            $votes = $rating_query->votes;

            $total_votes = $rating_query->total_votes;

            $new_average = ($votes * $total_votes + $user_rating) / ($total_votes + 1);

            // Update total votes and votes count
            DB::table('applications')->where('id', $content_id)->update(array('votes' => $new_average));
            DB::table('applications')->where('id', $content_id)->increment('total_votes');
        }

        if ($comment->approval == '0' && $comment->type == '1') {

            // Get average rating
            $rating_query = DB::table('applications')->where('id', $content_id)->first();
            $votes = $rating_query->votes;

            $total_votes = $rating_query->total_votes;

            if ($total_votes == '1') {
                $new_average = '0';
            } else {
                $new_average = ((($votes * $total_votes) - $user_rating) / ($total_votes - 1));
            }

            // Update total votes and votes count
            DB::table('applications')->where('id', $content_id)->update(array('votes' => $new_average));
            DB::table('applications')->where('id', $content_id)->decrement('total_votes');
        }

        // Clear cache
        Cache::flush();

        // Return view
        return redirect()->route('comments.index')->with('success', __('admin.content_updated'));
    }

    /** Destroy */
    public function destroy($id)
    {
        // Retrieve comment details
        $comment = Comment::find($id);
        $user_rating = $comment->rating;
        $content_id = $comment->content_id;
        $comment_status = $comment->approval;

        if ($comment_status == '1' && $comment->type == '1') {

            // Get average rating
            $rating_query = DB::table('applications')->where('id', $content_id)->first();
            $votes = $rating_query->votes;

            $total_votes = $rating_query->total_votes;

            if ($total_votes == '1') {
                $new_average = '0';
            } else {
                $new_average = ((($votes * $total_votes) - $user_rating) / ($total_votes - 1));
            }

            // Update total votes and votes count
            DB::table('applications')->where('id', $content_id)->update(array('votes' => $new_average));
            DB::table('applications')->where('id', $content_id)->decrement('total_votes');
        }

        $comment->delete();

        // Clear cache
        Cache::flush();

        // Redirect to list of comments
        return redirect()->route('comments.index')->with('success', __('admin.content_deleted'));
    }
}
