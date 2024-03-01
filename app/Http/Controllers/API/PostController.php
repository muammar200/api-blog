<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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

        // $posts = Post::whereNotNull('published_at')->has('author')->has('category')->latest();
        // if (request()->has('search')) {
        //     $keyword = request('search');
        //     $posts = Post::whereNotNull('published_at')->has('author')->has('category')->where(function ($query) use ($keyword) {
        //         $query->where('title', 'like', "%{$keyword}%")
        //             ->orWhere('slug', 'like', "%{$keyword}%")
        //             ->orWhere('content', 'like', "%{$keyword}%")
        //             ->orWhereHas('category', function ($query) use ($keyword) {
        //                 $query->where('name', 'like', "%{$keyword}%");
        //             })->orWhereHas('author', function ($query) use ($keyword) {
        //                 $query->where('username', 'like', "%{$keyword}%")
        //                     ->orWhereHas('detailUser', function ($query) use ($keyword) {
        //                         $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$keyword}%"]);
        //                     });
        //             });
        //     })->latest()->Paginate($perPage, ['*'], 'page', $page);
        // } else {
        //     $posts = Post::latest()->Paginate($perPage, ['*'], 'page', $page);;
        // }

        // $posts = $posts->Paginate($perPage, ['*'], 'page', $page);

        $posts = Post::whereNotNull('published_at')->has('author')->has('category')->latest();
        if (request()->has('search')) {
            $keyword = request('search');
            $posts->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%")
                    ->orWhereHas('category', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%{$keyword}%");
                    })->orWhereHas('author', function ($query) use ($keyword) {
                        $query->where('username', 'like', "%{$keyword}%")
                            ->orWhereHas('detailUser', function ($query) use ($keyword) {
                                $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$keyword}%"]);
                            });
                    });
            });
        }

        $posts = $posts->Paginate($perPage, ['*'], 'page', $page);

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
            ], 404);
        }
    }
}
