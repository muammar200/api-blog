<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Resources\PublicUserResource;

class PublicUserController extends Controller
{
    public function show(User $user, Request $request)
    {
        $perPage = 10; // Jumlah postingan per halaman

        $userPostsQuery = Post::where('author_id', $user->id)
            ->whereNotNull('published_at')->orderBy('created_at', 'desc');

        if ($request->has('loadMore')) {
            // Jika ada permintaan untuk memuat lebih banyak data
            $userPosts = $userPostsQuery->paginate($perPage, ['*'], 'page', $request->input('loadMore'));
        } else {
            // Jika permintaan pertama kali tanpa nomor halaman
            $userPosts = $userPostsQuery->paginate($perPage);
        }

        // $userPosts = Post::where('author_id', $user->id)->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Show Public User Success',
            'meta' => [
                'page' => $userPosts->currentPage(),
                'perpage' => $userPosts->perPage(),
                'total_page' => $userPosts->lastPage(),
                'total_item' => $userPosts->total(),
            ],
            'data' => [
                'user_data' => new PublicUserResource($user),
                'user_posts' => PostResource::collection($userPosts),
            ]
        ]);
    }
}
