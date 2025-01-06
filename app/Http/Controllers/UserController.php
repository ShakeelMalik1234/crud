<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use DB;
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

    public function dataEnter()
    {
        $totalAprilAmount = DB::table('orders')
            ->whereMonth('order_date', 4) 
            ->whereYear('order_date', '2023')
            ->sum('net_amount');

        $totalMayAmount = DB::table('orders')
            ->whereMonth('order_date', 5) 
            ->whereYear('order_date', '2023')
            ->sum('net_amount');

        $totalJunAmount = DB::table('orders')
            ->whereMonth('order_date', 6) 
            ->whereYear('order_date', '2023')
            ->sum('net_amount');
        return view('data_enter', compact('totalAprilAmount', 'totalMayAmount', 'totalJunAmount'));
    }

    public function postData()
    {
        $totalNetAmount = 0;

        for ($i = 0; $i < 100; $i++) { 
            if ($totalNetAmount >= 705.3) {
                break; 
            }

            $order = [
                'bill_no' => 'BILPR-'.strtoupper(Str::random(4)),
                'customer_id' => 0,
                'customer_name' => 'Walk-in-Customer',
                'customer_address' => '123123123',
                'customer_phone' => '212312312',
                'date_time' => strtotime(date('Y-m-d H:i:s', rand(strtotime('2023-06-01'), strtotime('2023-06-30')))),
                'gross_amount' => 0, 
                'service_charge_rate' => 0,
                'service_charge' => 0,
                'vat_charge_rate' => 0,
                'vat_charge' => 0,
                'total_servicecharges' => 0,
                'amount_tendered' => 0, 
                'net_amount' => 0, 
                'discount' => 0,
                'paid_status' => 2,
                'user_id' => 1,
                'deliver_to' => '',
                'deliver_from' => '',
                'address' => '',
                'order_type' => 1,
                'order_name' => '',
                'pos_order_type' => 2,
                'table_number' => '',
                'last_item_id' => 0,
                'order_date' => date('Y-m-d H:i:s', rand(strtotime('2023-06-01'), strtotime('2023-06-30')))
            ];
            $orderId = DB::table('orders')->insertGetId($order);

            $itemCount = rand(1, 5);
            $totalAmount = 0;
            
            for ($a = 0; $a < $itemCount; $a++) {
                $random = rand(1, 134);
                $rand_quantity = rand(1, 5);
                $product = DB::table('products')->where('id', $random)->first();

                $hasOptions = rand(0, 1) === 1;
                $itemOptions = $hasOptions ? json_encode([
                    'type' => 'addon',
                    'group_id' => rand(1, 5),
                    'group_name' => 'Pizza Type',
                    'option_id' => rand(1, 5),
                    "option_name" => "Klein",
                    "option_price" => rand(1, 5),
                    "object" => "954"
                ]) : ''; 

                
                $item = array(
                    'is_new_for_kitchen' => 0,
                    'order_id' => $orderId,
                    'product_id' => $product->id,
                    'qty' => $rand_quantity,
                    'rate' => $product->price * $rand_quantity,
                    'amount' => $product->price * $rand_quantity,
                    'discount' => 0,
                    'discount_type' => 'percentage',
                    'options' => $itemOptions,
                ); 

                $totalAmount += $product->price * $rand_quantity;
                DB::table('orders_item')->insert($item);
            }

            DB::table('orders')->where('id', $orderId)->update([
                'gross_amount' => $totalAmount,
                'amount_tendered' => $totalAmount,
                'net_amount' => $totalAmount,
            ]);

            $totalNetAmount += $totalAmount;

            if ($totalNetAmount >= 705.3) {
                break;
            }
        }
        return redirect()->back();
    }
}
