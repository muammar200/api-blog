<?php

namespace App\Http\Controllers\API;

use App\Rules\ValidateSlug;
use App\Models\CategoryPost;
use Illuminate\Http\Request;
use App\Http\Resources\TesResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryPostResource;
use App\Http\Resources\CategoryPostResourceAll;
use App\Http\Resources\Collection\CategoryResource;

class CategoryPostController extends Controller
{
    public function index()
    {
        $perPage = request()->get('perpage', 10);
        $page = request()->get('page', 1);

        $categories = CategoryPost::query();
        if(request()->has('search')){
            $keyword = request('search');
            $categories->where(function ($query) use ($keyword){
                $query->where('name', 'like', "%{$keyword}%")
                ->orWhere('slug', 'like', "%{$keyword}%");
            });
        }

        $categories = $categories->Paginate($perPage, ['*'], 'page', $page);
        
        return response()->json([
            'status' => true,
            'message' => 'List Categories',
            'meta' => [
                'page' => $categories->currentPage(),
                'perpage' => $categories->perPage(),
                'total_page' => $categories->lastPage(),
                'total_item' => $categories->total()
            ],
            'data' =>  CategoryPostResource::collection($categories),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:category_posts',
            'slug' => ['required', 'max:50', new ValidateSlug, 'unique:category_posts'],
        ]);

        $category = CategoryPost::create($request->all());

        return response()->json([
            'status' => true,
            'message' => "Category has been created",
            'data' => new CategoryPostResource($category)
        ]);
    }

    public function show($id)
    {
        $category = CategoryPost::findOrFail($id);
        
        return response()->json([
            'status' => true,
            'message' => "Show Category By Id",
            'data' => new CategoryPostResource($category)
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:category_posts,name,' . $id,
            'slug' => ['required', 'max:50', new ValidateSlug, 'unique:category_posts,slug,' . $id],
        ]);

        $category = CategoryPost::findOrFail($id);
        $category->update($request->all());

        return response()->json([
            'status' => true,
            'message' => "Category has been updated",
            'data' => new CategoryPostResource($category)
        ]);
    }

    public function destroy($id)
    {
        $category = CategoryPost::findOrFail($id);
        $category->delete();

        return response()->json([
            'status' => true,
            'message' => "Category has been deleted",
            'data' => new CategoryPostResource($category)
        ]);
    }

}
