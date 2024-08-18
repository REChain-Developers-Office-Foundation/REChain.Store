<?php
namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Redirect;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Site Settings
        $site_settings = DB::table('settings')->get();

        foreach ($site_settings as $setting) {
            $setting_name = $setting->name;
            $this->settings[$setting->name] = $setting->value;
        }

        // Pass data to views
        View::share(['settings' => $this->settings]);
    }

    /** Index */
    public function index(Request $request)
    {
        // List of reports
        $rows = Report::leftJoin('applications', 'reports.app_id', '=', 'applications.id')
            ->select('reports.*', 'applications.title', 'applications.slug')
            ->orderBy('id', 'DESC')
            ->paginate(15);

        foreach (report_reasons() as $key => $report_reason) {
            $reason_label[$key] = $report_reason;
        }

        // Return view
        return view('adminlte::reports.index')->with('rows', $rows)->with('reason_label', $reason_label);
    }

    /** Update */
    public function update($id)
    {
        // Retrieve report details
        $row = Report::find($id);

        if ($row->solved == 1) {
            $row->solved = 0;
            $row->save();
        } else {
            Report::where('app_id', $row->app_id)->where('reason', $row->reason)->update(['solved' => 1]);
        }

        // Redirect to list of reports
        return redirect()->back()->with('success', __('admin.status_updated'));
    }

}
