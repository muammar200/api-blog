<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;

use App\Models\PostImage;
use App\Models\DetailUser;
use App\Rules\ValidateSlug;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
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

        $posts = Post::where('author_id', $user->id)->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'message' => 'Show All Posts Based User Dashboard Success!',
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
            'message' => 'Show One Post Based User Dashboard Success!',
            'data' => new DashboardPostResource($post),
        ]);
    }

    public function store(Request $request, User $user)
    {
        // return $request->all();

        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required',
            'slug' => ['required', 'max:100', new ValidateSlug, 'unique:posts,slug'],
            'content' => 'required',
            // 'image.*' => 'image'
            'image' => 'image|max:2000',
            'published_at' => 'date_format:Y-m-d H:i:s'
        ]);

        $validatedData['author_id'] = $user->id;

        // store to table posts
        $post = Post::create($validatedData);

        if ($request->hasFile('image')) {

            // foreach ($request->file('image') as $file) {
            $randomName = Str::random(30);
            // $extension = $file->getClientOriginalExtension();
            $extension = $request->file('image')->getClientOriginalExtension();
            $newName = $randomName . '.' . $extension;

            Storage::putFileAs('images/posts', $request->file('image'), $newName);

            $postImage = new PostImage([
                'post_id' => $post->id,
                'image' => $newName,
            ]);

            $postImage->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Create Post Success!',
            'data' => new DashboardPostResource($post),
        ]);
    }

    public function update(Request $request, User $user, Post $post)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required',
            'slug' => ['required', 'max:100', new ValidateSlug, 'unique:posts,slug,' . $post->id],
            'content' => 'required',
            'image' => 'image|max:2000',
            'published_at' => 'date_format:Y-m-d H:i:s'
        ]);

        if ($request->hasFile('image')) {
            //kondisi untuk menghapus gambar pada post jika sudah ada gambar sebelumnya
            if (count($post->postImages) > 0) {
                $oldImage = $post->postImages[0]['image'];
                // foreach ($post->postImages as $image) {
                $oldImagePath = 'images/posts/' . $oldImage;

                Storage::delete($oldImagePath);
                // }
            }
            $randomName = Str::random(30);
            $extension = $request->file('image')->getClientOriginalExtension();
            $newName = $randomName . '.' . $extension;

            Storage::putFileAs('images/posts/', $request->file('image'), $newName);

            if ($post->postImages->isNotEmpty()) {
                $postImage = $post->postImages->first(); 
                $postImage->update(['image' => $newName]);
            } else {
                $postImage = new PostImage([
                    'post_id' => $post->id,
                    'image' => $newName,
                ]);

                $postImage->save();
            }
        }

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
        if ($post->postImages->isNotEmpty()) {
            $image = $post->postImages->first();
            $imagePath = 'images/user/avatar/' . $image->image;
            Storage::delete($imagePath);
            $image->delete(); // Hapus record gambar dari database
        }

        // Hapus post dari database
        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Delete Post Success!',
            'data' => new DashboardPostResource($post),
        ]);
    }

    public function showAllDeleted(User $user)
    {
        $perPage = request()->get('perpage', 10); 
        $page = request()->get('page', 1); 

        $posts = Post::where('author_id', $user->id)->onlyTrashed()->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'message' => 'Show All Posts Based User Dashboard Success!',
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
            'message' => 'Show Post Deleted Success!',
            'data' => new DashboardPostResource($postDeleted),
        ]);
    }

    public function restore(User $user, $slug)
    {
        $restorePost = Post::onlyTrashed()->where('slug', $slug)->first();
        $restorePost->restore(); 
        return response()->json([
            'status' => 'success',
            'message' => 'Restore Post Success!',
            'data' => new DashboardPostResource($restorePost),
        ]);
    }

    
}
