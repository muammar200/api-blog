<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    public function store(Request $request) 
    {
        $validated = $request->validate([
            'post_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Post::where('id', $value)->whereNull('deleted_at')->first();
                    if (!$exists) {
                        $fail('The selected post is invalid.');
                    }
                },
            ],
            'comments_content' => 'required',
        ]);
        
        $request['user_id'] = Auth::user()->id;

        $comment = Comment::create($request->all());
        return new CommentResource(true, 'Create Comment Success!',$comment);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'comments_content' => 'required',
        ]);          

        $comment = Comment::findOrFail($id);

        $comment->update($request->only('comments_content'));
        return new CommentResource(true, 'Update Comment Success!',$comment);
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return new CommentResource(true, 'Delete Comment Success', $comment);
    }
}
