<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Models\Category;
use App\Tables\Categories;
use Illuminate\Http\Request;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index', [
            // 'categories' => SpladeTable::for(Category::class)
            //     ->column('name', canBeHidden:false, sortable:true)
            //     ->withGlobalSearch(columns:['name'])
            //     ->column('slug', sortable:true)
            //     ->column('action')
            //     ->export()
            //     ->paginate(5),
            'categories' => Categories::class,
        ]);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(CategoryStoreRequest $request)
    {
        Category::create($request->validated());
        Toast::title('New Category Created Successfully');

        return redirect()->route('categories.index');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(CategoryStoreRequest $request, Category $category)
    {
        $category->update($request->validated());
        Toast::title('Category Updated Successfully');

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        Toast::title('Category Deleted Successfully');

        return redirect()->back();

    }

}
