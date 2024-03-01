<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Like;
use App\Models\User;
use App\Models\Comment;
use App\Models\PostImage;
use App\Models\CategoryPost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'category_id', 'slug', 'content', 'author_id', 'published_at'];
    protected $with = ['category', 'author', 'postImages', 'comments', 'likes'];
    protected $dates = ['published_at'];


    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function postImages(): HasMany
    {
        return $this->hasMany(PostImage::class, 'post_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryPost::class, 'category_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'post_id', 'id');
    }

}
