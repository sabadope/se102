<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Hash;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function register()
    {
        // Get all roles including Admin but excluding Super Admin
        $role = DB::table('role_type_users')
            ->whereNotIn('role_type', ['Super Admin'])
            ->get();
        return view('auth.register',compact('role'));
    }
    public function storeUser(Request $request)
    {
        try {
            // Basic input sanitization
            $name = trim(filter_var($request->name, FILTER_SANITIZE_STRING));
            $email = filter_var($request->email, FILTER_SANITIZE_EMAIL);
            $role_name = trim(filter_var($request->role_name, FILTER_SANITIZE_STRING));
            $password = trim($request->password);

            $validator = Validator::make($request->all(), [
                'name'      => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-zA-Z\s]+$/' // Only letters and spaces
                ],
                'email'     => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users',
                    'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/' // Strict email format
                ],
                'role_name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-zA-Z\s]+$/', // Only letters and spaces
                    'exists:role_type_users,role_type', // Validate role exists
                    'not_in:Admin' // Prevent admin role selection
                ],
                'password'  => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
                ],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                
                // Check each password requirement
                $passwordErrors = [];
                
                if (strlen($password) < 8) {
                    $passwordErrors[] = 'Password must be at least 8 characters long.';
                }
                if (!preg_match('/[A-Z]/', $password)) {
                    $passwordErrors[] = 'Password must contain at least one uppercase letter.';
                }
                if (!preg_match('/[a-z]/', $password)) {
                    $passwordErrors[] = 'Password must contain at least one lowercase letter.';
                }
                if (!preg_match('/[0-9]/', $password)) {
                    $passwordErrors[] = 'Password must contain at least one number.';
                }
                if (!preg_match('/[@$!%*?&]/', $password)) {
                    $passwordErrors[] = 'Password must contain at least one special character (@$!%*?&).';
                }
                
                // Add password errors to the session
                if (!empty($passwordErrors)) {
                    session()->flash('password_errors', $passwordErrors);
                }
                
                // Add other validation errors
                foreach ($errors->all() as $error) {
                    Toastr::error($error, 'Validation Error');
                }
                
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();
            
            $dt = Carbon::now();
            $todayDate = $dt->toDayDateTimeString();
            
            // Create user with sanitized data
            $user = new User;
            $user->name         = $name;
            $user->email        = $email;
            $user->role_name    = $role_name;
            $user->password     = Hash::make($password);
            $user->join_date    = $todayDate;
            $user->status       = 'Active';
            $user->avatar       = 'photo_defaults.jpg';
            $user->save();

            DB::commit();
            Toastr::success('Account created successfully! You can now login.','Success');
            return redirect()->route('login');
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('Registration failed. Please try again.','Error');
            return redirect()->back();
        }
    }
}
