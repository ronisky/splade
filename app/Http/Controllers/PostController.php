<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Models\Category;
use App\Models\Post;
use App\Tables\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostController extends Controller
{
    public function index()
    {
        // $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
        //     $query->where(function ($query) use ($value) {
        //         Collection::wrap($value)->each(function ($value) use ($query) {
        //             $query
        //                 ->orWhere('category_id', 'LIKE', "%{$value}%")
        //                 ->orWhere('title', 'LIKE', "%{$value}%")
        //                 ->orWhere('slug', 'LIKE', "%{$value}%");
        //         });
        //     });
        // });

        // $posts = QueryBuilder::for(Post::class)
        //         ->defaultSort('title')
        //         ->allowedSorts(['title', 'slug'])
        //         ->allowedFilters(['title', 'slug', 'category_id', $globalSearch]);

        // $categories = Category::orderBy('id')->pluck('name', 'id')->toArray();
        return view('posts.index', [
            // 'posts' => SpladeTable::for($posts)
            //     ->column('title', canBeHidden:false, sortable:true)
            //     ->withGlobalSearch(columns:['title'])
            //     ->column('slug', sortable:true)
            //     ->column('action')
            //     ->selectFilter('category_id',$categories)
            //     ->paginate(5),
            'posts' => Posts::class
        ]);
    }

    public function create()
    {
        $categories = Category::orderBy('id')->pluck('name', 'id')->toArray();
        return view('posts.create', compact('categories'));
    }

    public function store(PostStoreRequest $request)
    {
        Post::create($request->validated());
        Toast::title('New Post Created Successfully');

        return to_route('posts.index');
    }

    public function edit(Post $post)
    {
        $categories = Category::orderBy('id')->pluck('name', 'id')->toArray();
        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(PostStoreRequest $request, Post $post)
    {
        $post->update($request->validated());
        Toast::title('Post Updated Successfully');

        return to_route('posts.index');
    }
    public function destroy(Post $post)
    {
        $post->delete();
        Toast::title('Post Deleted Successfully');

        return redirect()->back();

    }

}
