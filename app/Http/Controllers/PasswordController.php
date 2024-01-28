<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class PasswordController extends Controller
{
    public function changePassword(Request $request)
    {

        $validated = $request->validate([
            'old_password' => 'required|max:100',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Verifikasi password saat ini
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => "Old password isn't valid"
            ], 400);
        }
        // return 'same';

        $user->update(['password' => $request->new_password]);

        return response()->json([
            'status' => true,
            'message' => 'Password successfully changed'
        ]);
    }

    public function sendEmailForgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => __($status)])
            : response()->json(['error' => __($status)], 422);
    }

    public function getTokenResetPassword(Request $request, string $token){
        return response()->json([
            'status' => true,
            'token' => $token,
            'email' => $request->email]);
    }

    public function resetPassword(Request $request){
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);
     
                $user->save();
     
                event(new PasswordReset($user));
            }
        );
        return $status === Password::PASSWORD_RESET
        ? response()->json(['status' => __($status)])
        : response()->json(['error' => __($status)], 422);
    }
}
