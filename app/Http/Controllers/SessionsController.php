<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store()
    {
        $attributes = request()->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::guard('officer')->attempt($attributes)) {

            $officer = Officer::where('username', request('username'))->first();

            // —— NEW: flag expired or never-changed passwords ——
            if (
                is_null($officer->password_last_changed)
                || Carbon::parse($officer->password_last_changed)->addDays(30)->isPast()
            ) {
                $officer->force_password_reset = true;
                $officer->save();
                $officer->refresh();
            }

            // Check if user needs to change password
            if ($officer->force_password_reset) {
                return redirect()->route('force-password-change');
            }



            session()->regenerate();
            return redirect('dashboard');
        } else {
            //look for the user in the database
            $officer = Officer::where('username', request('username'))->first();

            if ($officer) {
                //is password correct, just compare 2 strings
                if (request('password') == $officer->un_hashed_password) {

                    dd('using fallback');

                    // —— NEW: flag expired or never-changed passwords ——
                    if (
                        is_null($officer->password_last_changed)
                        || Carbon::parse($officer->password_last_changed)->addDays(30)->isPast()
                    ) {
                        $officer->force_password_reset = true;
                        $officer->save();
                        $officer->refresh();
                    }

                    // Check if user needs to change password
                    if ($officer->force_password_reset) {
                        return redirect()->route('force-password-change');
                    }



                    Auth::guard('officer')->login($officer);
                    session()->regenerate();
                    return redirect('dashboard');
                } else {
                    return back()->withErrors(['password' => 'Email or password invalid new impl IN AUTH GUARD.']);
                }
            }

            return back()->withErrors(['email' => 'Email or password invalid new impl.']);
        }
    }

    public function forcePasswordChange(Request $request)
    {
        $request->validate([
            // 'password' => 'required|string|min:8|confirmed',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/'
            ],

        ]);

        /** @var \App\Models\Officer $officer */
        $officer = auth('officer')->user();
        $officer->password = bcrypt($request->password);
        $officer->un_hashed_password = $request->password;
        $officer->password_last_changed = now();
        $officer->force_password_reset = false;
        $officer->save();

        return redirect()->route('dashboard')->with('success', 'Password changed successfully.');
    }




    public function destroy()
    {

        Auth::logout();

        return redirect('/login')->with(['success' => 'You have been logged out.']);
    }
}
