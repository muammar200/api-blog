<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;

use App\Models\PostImage;
use App\Models\DetailUser;
use App\Rules\ValidateSlug;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\returnSelf;
use App\Http\Resources\DashboardPostResource;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class DashboardPostController extends Controller
{
    public function index(User $user)
    {
        $perPage = request()->get('perpage', 10);
        $page = request()->get('page', 1);

        if (request()->has('search')) {
            $keyword = request('search');
            $posts = Post::where('author_id', $user->id)->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%");
            })->orderByDesc('published_at')->paginate($perPage, ['*'], 'page', $page);
        } else {
            $posts = Post::where('author_id', $user->id)->orderByDesc('published_at')->paginate($perPage, ['*'], 'page', $page);
        }

        // return DashboardPostResource::collection($posts);
        return response()->json([
            'status' => 'success',
            'message' => 'Displayed all posts based on user dashboard!',
            'meta' => [
                'page' => $posts->currentPage(),
                'perpage' => $posts->perPage(),
                'total_page' => $posts->lastPage(),
                'total_item' => $posts->total(),
            ],
            'data' => DashboardPostResource::collection($posts)
        ]);
    }

    public function show(User $user, Post $post)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Show One Post Based User Dashboard!',
            'data' => new DashboardPostResource($post),
        ]);
    }

    public function store(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required',
            'slug' => ['required', 'max:100', new ValidateSlug, 'unique:posts,slug'],
            'content' => 'required',
            'image.*' => 'image|max:2000',
            // 'image' => 'image|max:2000',
            'published_at' => 'nullable|date'
        ]);

        $validatedData['slug'] .= '-' .  time() . rand();
        $validatedData['published_at'] = $request->published_at ? Carbon::parse($request->published_at) : null;
        $validatedData['author_id'] = $user->id;

        // store to table posts
        $post = Post::create($validatedData);

        if ($request->hasFile('image')) {
            // foreach ($request->file('image') as $file) {
            // $randomName = Str::random(30);
            // // $extension = $file->getClientOriginalExtension();
            // $extension = $request->file('image')->getClientOriginalExtension();
            // $newName = $randomName . '.' . $extension;

            // Storage::putFileAs('images/posts', $request->file('image'), $newName);
            // upload to S3
            foreach ($request->file('image') as $file) {
                // $file = request()->file('image');
                $path = $file->store('public/images/posts');
                $filename = basename($path);

                $postImage = new PostImage([
                    'post_id' => $post->id,
                    'image' => $filename,
                ]);

                $postImage->save();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Create Post!',
            'data' => new DashboardPostResource($post),
        ]);
    }

    public function update(Request $request, User $user, Post $post)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required',
            'slug' => ['required', 'max:100', 'unique:posts,slug,' . $post->id],
            'content' => 'required',
            // 'image' => 'image|max:2000',
            'published_at' => 'date_format:Y-m-d H:i:s'
        ]);

        // Update slug jika judul berubah
        if ($request->title !== $post->title) {
            $validatedData['slug'] .=  '-' . time() . rand();
        }

        if($request->title == $post->title){
            $validatedData['slug'] = $post->slug;
        }
        // if ($request->hasFile('image')) {
        //     //kondisi untuk menghapus gambar pada post jika sudah ada gambar sebelumnya
        //     if (count($post->postImages) > 0) {
        //         $oldImage = $post->postImages[0]['image'];
        //         // foreach ($post->postImages as $image) {
        //         $oldImagePath = 'images/posts/' . $oldImage;

        //         Storage::delete($oldImagePath);
        //         // }
        //     }
        //     $randomName = Str::random(30);
        //     $extension = $request->file('image')->getClientOriginalExtension();
        //     $newName = $randomName . '.' . $extension;

        //     Storage::putFileAs('images/posts/', $request->file('image'), $newName);

        //     if ($post->postImages->isNotEmpty()) {
        //         $postImage = $post->postImages->first(); 
        //         $postImage->update(['image' => $newName]);
        //     } else {
        //         $postImage = new PostImage([
        //             'post_id' => $post->id,
        //             'image' => $newName,
        //         ]);

        //         $postImage->save();
        //     }
        // }

        $post->update($validatedData);
        $post = Post::where('slug', $post->slug)->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Update Post Success!',
            'data' => new DashboardPostResource($post),
        ]);
    }

    public function destroy(User $user, Post $post)
    {
        // Hapus gambar terkait post jika ada
        // if ($post->postImages->isNotEmpty()) {
        //     foreach ($post->postImages as $image){

        //     }
        //     $image = $post->postImages->first();
        //     $imagePath = 'images/user/avatar/' . $image->image;
        //     Storage::delete($imagePath);
        //     $image->delete(); // Hapus record gambar dari database
        // }

        // Hapus post dari database
        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Delete Post!',
            'data' => new DashboardPostResource($post),
        ]);
    }

    public function showAllDeleted(User $user)
    {
        $perPage = request()->get('perpage', 10);
        $page = request()->get('page', 1);

        $posts = Post::where('author_id', $user->id)->onlyTrashed()->orderByDesc('deleted_at')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'message' => 'Displayed all deleted posts based on user dashboard!',
            'meta' => [
                'page' => $posts->currentPage(),
                'perpage' => $posts->perPage(),
                'total_page' => $posts->lastPage(),
                'total_item' => $posts->total(),
            ],
            'data' => DashboardPostResource::collection($posts)
        ]);
    }

    public function showSingleDeleted(User $user, $slug)
    {
        $postDeleted = Post::onlyTrashed()->where('slug', $slug)->firstOrFail();

        return response()->json([
            'status' => 'success',
            'message' => 'Show Post Deleted!',
            'data' => new DashboardPostResource($postDeleted),
        ]);
    }

    public function restore(User $user, $slug)
    {
        $restorePost = Post::onlyTrashed()->where('slug', $slug)->first();
        $restorePost->restore();
        return response()->json([
            'status' => 'success',
            'message' => 'Restore Post!',
            'data' => new DashboardPostResource($restorePost),
        ]);
    }
}
