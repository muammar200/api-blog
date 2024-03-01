<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\PublicUserResource;
use App\Http\Resources\SearchUserResource;

class PublicUserController extends Controller
{
    public function show(User $user)
    {
        return new PublicUserResource($user);
    }

    public function search(Request $request)
    {
        if ($request->input('search')) {
            $perPage = 10;
            $page = 1;
            if ($request->has('loadMore')) {
                $number = $request->input('loadMore');
                $page = $number;
            }

            $search = $request->input('search');
            $users = User::where('username', 'LIKE', '%' . $search . '%')->orWhereHas('detailUser', function ($query) use ($search) {
                $query->where('firstname', 'like', "%{$search}%")->orWhere('lastname', 'like', "%{$search}%");
            })->paginate($perPage, ['*'], 'page', $page);;

            return response()->json([
                'meta' => [
                    'page' => $users->currentPage(),
                    'perpage' => $users->perPage(),
                    'total_page' => $users->lastPage(),
                    'total_item' => $users->total(),
                ],
                'data' => SearchUserResource::collection($users),
            ]);
        }
    }
}
