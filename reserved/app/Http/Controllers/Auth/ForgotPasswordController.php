<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        try {
            $request->validate([
                'email' => [
                    'required',
                    'email',
                    'exists:users,email',
                    'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
                ]
            ]);

            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                Toastr::error('We cannot find a user with that email address.','Error');
                return redirect()->back();
            }

            // Generate reset token
            $token = Str::random(64);
            $expiresAt = Carbon::now()->addHours(24);

            // Store token in database
            DB::table('password_resets')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]
            );

            // Send email
            $resetLink = route('password.reset', ['token' => $token, 'email' => $request->email]);
            
            // You can customize this email template
            Mail::send('emails.reset-password', ['resetLink' => $resetLink], function($message) use($request) {
                $message->to($request->email);
                $message->subject('Reset Password Notification');
            });

            Toastr::success('We have emailed your password reset link!','Success');
            return redirect()->back();

        } catch(\Exception $e) {
            Toastr::error('Something went wrong. Please try again.','Error');
            return redirect()->back();
        }
    }

    public function showResetForm(Request $request, $token)
    {
        $email = $request->email;
        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => [
                    'required',
                    'email',
                    'exists:users,email',
                    'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
                ],
            ]);

            $reset = DB::table('password_resets')
                ->where('email', $request->email)
                ->where('token', $request->token)
                ->first();

            if (!$reset) {
                Toastr::error('Invalid password reset token.','Error');
                return redirect()->back();
            }

            // Check if token is expired (24 hours)
            if (Carbon::parse($reset->created_at)->addHours(24)->isPast()) {
                DB::table('password_resets')->where('email', $request->email)->delete();
                Toastr::error('Password reset token has expired.','Error');
                return redirect()->back();
            }

            // Update password
            $user = User::where('email', $request->email)->first();
            $user->password = bcrypt($request->password);
            $user->save();

            // Delete the token
            DB::table('password_resets')->where('email', $request->email)->delete();

            Toastr::success('Your password has been reset successfully!','Success');
            return redirect()->route('login');

        } catch(\Exception $e) {
            Toastr::error('Something went wrong. Please try again.','Error');
            return redirect()->back();
        }
    }
}
