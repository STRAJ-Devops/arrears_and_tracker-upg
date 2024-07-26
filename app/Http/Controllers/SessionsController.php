<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store()
    {
        $attributes = request()->validate([
            'username'=>'required',
            'password'=>'required'
        ]);

        if (Auth::guard('officer')->attempt($attributes)) {
            session()->regenerate();
            return redirect('dashboard');
        }
        else{
            //look for the user in the database
            $officer = Officer::where('username', request('username'))->first();

            if ($officer) {
                //is password correct, just compare 2 strings
                if (request('password') == $officer->un_hashed_password) {
                    Auth::guard('officer')->login($officer);
                    session()->regenerate();
                    return redirect('dashboard');
                } else {
                    return back()->withErrors(['password'=>'Email or password invalid.']);
                }
            }

            return back()->withErrors(['email'=>'Email or password invalid.']);
        }
    }

    public function destroy()
    {

        Auth::logout();

        return redirect('/login')->with(['success'=>'You have been logged out.']);
    }
}
