<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Redirect;

class AccountController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Index */
    public function index()
    {
        return view('admin');
    }

    /** Account Settings Form */
    public function accountsettingsform()
    {
        return view('adminlte::settings.account');
    }

    /** Account Settings */
    public function accountsettings(Request $request)
    {
        $id = Auth::user()->id;
        $this->validate($request, [
            'email' => "unique:users,email,$id|required|email",
        ]);

        if ($request->input('email') != Auth::user()->email) {
            if ((Hash::check($request->input('current-password'), Auth::user()->password))) {
                //Change E-mail
                $user = Auth::user();
                $user->email = $request->input('email');
                $user->save();
                return redirect()->back()->with("success", __('admin.email_changed'));
            }
        }

        if (!(Hash::check($request->input('current-password'), Auth::user()->password))) {
            // The passwords matches
            return Redirect::back()->withErrors(__('admin.password_match_problem'));
        }

        if (strcmp($request->input('current-password'), $request->input('new-password')) == 0) {
            //Current password and new password are same
            return Redirect::back()->withErrors(__('admin.new_old_password_same'));
        }

        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|alpha_num|min:8|confirmed',
        ]);

        //Change Password
        $user = Auth::user();
        $user->password = Hash::make($request->input('new-password'));
        $user->save();
        return redirect()->back()->with("success", __('admin.password_changed'));
    }

}
