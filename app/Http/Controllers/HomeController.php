<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function ammar(){
        return auth()->user();
    }
    public function index(Request $request)
    {
        try {
            // Logika untuk menentukan tampilan tergantung pada status login pengguna
            if ($request->user()) {
                // Jika pengguna sudah login
                $user = $request->user();

                // // Contoh mengambil postingan yang diikuti oleh pengguna atau postingan terbaru
                // $userPosts = $user->followedPosts()->orderBy('created_at', 'desc')->take(5)->get();

                // $data = [
                //     'message' => 'Welcome back, ' . $user->name . '!',
                //     'user' => $user,
                //     'posts' => $userPosts,
                //     // ... data lainnya yang relevan untuk pengguna yang sudah login
                // ];

                $publicPosts = Post::orderBy('created_at', 'desc')->take(5)->get();

                $data = [
                    'status' => 'success',
                    'message' => 'Welcome to our platform!',
                    'user' => $user,
                    'public_posts' => $publicPosts,
                    // ... data lainnya yang relevan untuk pengguna yang belum login
                ];
            } else {
                // Jika pengguna belum login
                $publicPosts = Post::orderBy('created_at', 'desc')->take(5)->get();

                $data = [
                    'status' => 'success',
                    'message' => 'Welcome to our platform!',
                    'public_posts' => $publicPosts,
                    // ... data lainnya yang relevan untuk pengguna yang belum login
                ];
            }

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again later.'], 500);
        }
    }
}
