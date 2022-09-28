<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Session;

class UserController extends Controller
{
    public function userStore(Request $request)
    {
        $request->validate([
            'uname' => 'required|min:5',
            'email' => 'required|unique:users,email|email:rfc,dns',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:20',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'cpassword' => 'required|required_with:password|same:password|min:6',
            'phone_number' => 'required|min:10|max:11',
            'address' => 'required',
        ],[ 
            'uname.required' => 'Please enter your name',
            'uname.min' => 'Your name must be at least 5 characters',
            'email.required' => 'Please enter your email address',
            'password.required' => 'Please enter your password',
            'password.regex' => 'You must be used a strong password',
            'cpassword.required' => 'Please confirm the password',
            'cpassword.same' => 'Password must be match',
            'phone_number.required' => 'Please enter your phone number',
            'phone_number.min' => 'Phone number must be at least 10 characters',
            'phone_number.max' => 'Phone number must be greater than 12 characters',
            'address.required' => 'Please enter your address'
        ]);

        
        
        $user = new User();
        $user->uname = $request->uname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone_number = $request->phone_number;
        $user->address = $request->address;
        $user->save();
        if(Auth::check()){
            {
                return redirect()->route('user-dashboard');
            }
            return redirect()->route('user-ragistration');
        }
        Auth::login($user);
        
        return redirect()->route('user-dashboard')->with('success', 'Your form has been submitted.');
        
    }
    public function formLogin()
    {
        return view('loginform');
    }
    public function userLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|'
        ],[
            'email.required' => 'Please enter your email address',
            'email.exists' => 'your email is not registered',
            'password.required' => 'Please enter your password',
            'password.exists' => 'wrong password',

        ]);


        $userdata = $request->only('email', 'password');
        if(Auth::check()){
            {
                return redirect()->route('user-dashboard');
            }
        }elseif (Auth::attempt($userdata)) {
           // Authentication passed...
           return redirect()->route('user-dashboard');
        }
        return redirect()->back()->withseccess('Login details are not valid');
    }
    public function userDashboard()
    {
        if(Auth::check()){
            {
                return view('dashboard');
            }
        }
  
        return redirect('/login')->withSuccess('You are not allowed to access');
    }
    public function userSignout() {
        Session::flush();
        Auth::logout();
  
        return Redirect()->route('form-login');
    }
}
