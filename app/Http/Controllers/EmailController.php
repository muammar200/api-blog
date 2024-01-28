<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailController extends Controller
{
    public function verify($id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return response()->json([
                'status' => false,
                'message' => 'verifikasi email gagal'
            ], 400);
        }

        $user = User::find($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json([
            'status' => true,
            'message' => 'Email verification successfull'
        ]);
    }

    public function emailVerified(Request $request)
    {
        $status = session()->get('status', true);
        return $status;
        if($status !== true){
            return response()->json([
            'status' => false,
            'message' => 'Data not found'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Email verification successfull.'
        ]);
    }
    

    public function resend()
    {

        if (Auth::user()->hasVerifiedEmail()) {
            return response()->json([
                'status' => true,
                'message' => 'Your email has been verified'
            ]);
        }

        Auth::user()->sendEmailVerificationNotification();
        return response()->json([
            'status' => true,
            'message' => 'Email verification link has been sent to your email.'
        ]);
    }
}
