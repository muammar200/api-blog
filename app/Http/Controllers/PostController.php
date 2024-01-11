<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostDetailResource;


class PostController extends Controller
{
    public function index()
    {
        $perPage = request()->get('perpage', 10); // Mengambil jumlah item per halaman dari request, defaultnya 10
        $page = request()->get('page', 1); // Mengambil nomor halaman dari request, defaultnya halaman 1

        $posts = Post::whereNotNull('published_at')->has('author')->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        // return $posts;
        return response()->json([
            'status' => 'success',
            'message' => 'Show Posts Success!',
            'meta' => [
                'page' => $posts->currentPage(),
                'perpage' => $posts->perPage(),
                'total_page' => $posts->lastPage(),
                'total_item' => $posts->total(),
            ],
            'data' => PostResource::collection($posts),
        ]);
    }

    public function show(Post $post)
    {
        if ($post->published_at !== null) {
            return response()->json([
                'status' => 'success',
                'message' => 'Show Category Posts Success!',
                'data' => new PostResource($post)
            ]);
        } else {
            return response()->json([
                'error' => 'data not found',
            ]);
        }
    }
}
