<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\DetailUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\MessageResource;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|max:100',
            'lastname' => 'max:100',
            'username' => 'required|unique:users|max:20',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required|min:8|max:255|confirmed'
        ]);

        DB::transaction(function () use ($request, &$user, &$token) {
            // store to table users
            $user = User::create($request->all());

            // store to table detail_users
            $detailUser = DetailUser::create([
                'user_id' => $user->id,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname
            ]);

            // SANCTUM
            // $token = $user->createToken('token_user')->plainTextToken;

            // JWT
            $token = Auth::login($user);
        });

        // return $tess;

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
        
        }
        return new AuthResource (true, 'User registered successfully!. Check Email for Verification', $user, $token,);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email:rfc,dns',
            'password' => 'required',
        ]);

        // SANCTUM
        // $user = User::where('email', $request->email)->first();
        // if (! $user || ! Hash::check($request->password, $user->password)) {
        //     throw ValidationException::withMessages([
        //         'email' => ['The provided credentials are incorrect.'],
        //     ]);
        // }
        // return $user->createToken('token_user')->plainTextToken;

        // JWT
        if (Auth::attempt($credentials)) {
            $token = Auth::attempt($credentials);
            $user = Auth::user();

            return new AuthResource (true, 'User login successfully!', $user, $token,);
        } 
        else {
            return new MessageResource (false, 'Email or password is incorrect', 401);
        }
    }

    public function logout(Request $request)
    {
        // SANCTUM
        // $request->user()->tokens()->delete();
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Logout successful!'
        // ]);

        // JWT
        $user = Auth::user();
        Auth::logout();
        return new AuthResource (true, 'User logout successfully!', $user, null);
    }

    public function refreshToken()
    {
        $token = Auth::refresh();
        $user =  Auth::user();

        return new AuthResource (true, 'Refresh Token successfully!', $user, $token);
    }
}
