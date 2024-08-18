<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class AdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Index */
    public function index()
    {
        // Retrieve list of ads
        $rows = Ad::orderBy('title', 'asc')->get();

        // Return view
        return view('adminlte::ads.index', compact('rows'));
    }

    /** Edit */
    public function edit($id)
    {
        // Retrieve ad details
        $row = Ad::find($id);

        // Return 404 page if ad not found
        if ($row == null) {
            abort(404);
        }

        // Return view
        return view('adminlte::ads.edit', compact('row', 'id'));
    }

    /** Update */
    public function update(Request $request, $id)
    {
        // Retrieve ad details
        $row = Ad::find($id);

        $ad_code = $request->get('code');

        $row->code = $ad_code;
        $row->save();

        // Clear cache
        Cache::flush();

        // Redirect to ad edit page
        return redirect()->route('ads.edit', $row->id)->with('success', 'Data Updated');
    }

}
