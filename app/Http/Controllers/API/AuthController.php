<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\DetailUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $userRegister = DB::transaction(function () use ($request, &$user, &$token) {
            // store to table users
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password
            ]);

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
            return true;
        });

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
        
        }
        return new UserResource($user, $token, true, 'User registered successfully!. Please Check Your Email for Verification');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email:rfc,dns',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // SANCTUM
        // $user = User::where('email', $request->email)->first();
        // return $user->createToken('token_user')->plainTextToken;

        // JWT
        $loginValue = $request->only('email', 'password');

        if (Auth::attempt($loginValue)) {
        
            $token = Auth::attempt($loginValue);
            // $token = Auth::attempt();
            $user = Auth::user();
            return new UserResource($user, $token, true, 'User login successfully!');
        } else {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Email or password is incorrect'
            ], 401);
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
        return response()->json([
            'status' => true,
            'message' => 'Successfully Logout',
            'data' => $user
        ], 200);
    }

    public function refreshToken()
    {

        $token = Auth::refresh();
        $user =  Auth::user();

        return response()->json([
            'status' => true,
            'message' => "Refresh Token Successfully",
            'user' => $user,
            'Authorization' => [
                'token' => $token,
                'type'  => 'Bearer'
            ]
        ], 200);
    }
}
