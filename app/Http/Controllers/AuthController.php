<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\DetailUser;
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
            'password' => 'required|min:8|max:255'
        ]);

        // return response()->json($request->username);

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

        return new UserResource($user->loadMissing('detailUser'), true, 'User registered successfully!');
        // return new UserResource($user->loadMissing(['detailUser']), true, 'User registered successfully!');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // if (! $user->email_verified_at) {
        //     throw ValidationException::withMessages([
        //         'email' => ['Email is not verified.'],
        //     ]);
        // }

        return $user->createToken('token_user')->plainTextToken;
        // return response()->json(['token' => $user->createToken('token_user')->plainTextToken]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful!'
        ]);
    }
}
