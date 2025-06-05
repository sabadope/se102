<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use DB;
use Auth;
use Session;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
    * Where to redirect users after login.
    *
    * @var string
    */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout',
            'locked',
            'unlock'
        ]);
    }
    /** index page login */
    public function login()
    {
        return view('auth.login');
    }

    /** login with databases */
    public function authenticate(Request $request)
    {
        try {
            $email = filter_var($request->email, FILTER_SANITIZE_EMAIL);
            $password = trim($request->password);

            $validator = Validator::make($request->all(), [
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255'
                ],
                'password' => [
                    'required',
                    'string',
                    'min:6'
                ],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                if ($errors->has('email')) {
                    Toastr::error('Please enter a valid email address.','Error');
                } elseif ($errors->has('password')) {
                    Toastr::error('Password must be at least 6 characters.','Error');
                }
                return redirect()->back()->withInput();
            }

            if ($this->hasTooManyLoginAttempts($request)) {
                $seconds = $this->limiter()->availableIn(
                    $this->throttleKey($request)
                );
                Toastr::error('Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.','Error');
                return redirect()->back();
            }

            if (Auth::attempt(['email' => $email, 'password' => $password], $request->filled('remember'))) {
                $this->clearLoginAttempts($request);
                $user = Auth::User();
                
                if (!$user || $user->status !== 'Active') {
                    Auth::logout();
                    Toastr::error('Your account is not active. Please contact administrator.','Error');
                    return redirect('login');
                }
                
                // Set session data only if values exist
                $sessionData = [
                    'name' => $user->name ?? '',
                    'email' => $user->email ?? '',
                    'user_id' => $user->user_id ?? '',
                    'join_date' => $user->join_date ?? '',
                    'phone_number' => $user->phone_number ?? '',
                    'status' => $user->status ?? '',
                    'role_name' => $user->role_name ?? '',
                    'avatar' => $user->avatar ?? '',
                    'position' => $user->position ?? '',
                    'department' => $user->department ?? ''
                ];

                foreach ($sessionData as $key => $value) {
                    if ($value !== null) {
                        Session::put($key, $value);
                    }
                }

                Toastr::success('Login successful! Welcome back, ' . $user->name,'Success');
                return redirect()->route('home');
            } else {
                $this->incrementLoginAttempts($request);
                Toastr::error('Invalid email or password. Please try again.','Error');
                return redirect('login');
            }
           
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('An error occurred during login. Please try again.','Error');
            return redirect()->back();
        }
    }

    /** logout */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->flush();
        Toastr::success('Logout successful!','Success');
        return redirect('login');
    }

}
