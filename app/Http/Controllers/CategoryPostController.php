<?php

namespace App\Http\Controllers;

use App\Rules\ValidateSlug;
use App\Models\CategoryPost;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryPostResource;
use App\Http\Resources\CategoryPostResourceShowAll;

class CategoryPostController extends Controller
{
    public function index()
    {
        $categories = CategoryPost::all();
        return response()->json([
            'success' => true,
            'message' => 'Show All Category Posts Success!',
            'data' => CategoryPostResourceShowAll::collection($categories)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:category_posts',
            'slug' => ['required', 'max:50', new ValidateSlug, 'unique:category_posts'],
        ]);

        $category = CategoryPost::create($request->all());

        return new CategoryPostResource($category, true, 'store category post success');
    }

    public function show($id)
    {
        $category = CategoryPost::findOrFail($id);
        return new CategoryPostResource($category, true, 'store category post success');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:category_posts,name,' . $id,
            'slug' => ['required', 'max:50', new ValidateSlug, 'unique:category_posts,slug,' . $id],
        ]);

        $category = CategoryPost::findOrFail($id);
        $category->update($request->all());

        return new CategoryPostResource($category, true, 'update category post success');
    }

    public function destroy($id)
    {
        $category = CategoryPost::findOrFail($id);
        $category->delete();
        
        return new CategoryPostResource($category, true, 'delete category post success');
    }

    public function showAllDeleted()
    {
        
        $deletedCategory = CategoryPost::onlyTrashed()->get();

        return response()->json([
            'success' => true,
            'message' => 'Show All Deleted Category Posts Success!',
            'data' => CategoryPostResourceShowAll::collection($deletedCategory)
        ]);
    }

    public function showSingleDeleted($id)
    {
        $categoryDeleted = CategoryPost::onlyTrashed()->findOrFail($id);
        return new CategoryPostResource($categoryDeleted, true, 'show deleted category post success');
    }

    public function restore($id)
    {
        $restoreCategory = CategoryPost::onlyTrashed()->findOrFail($id);
        
        $restoreCategory->restore(); 

        return new CategoryPostResource($restoreCategory, true, 'restore category post success');
    }
}
