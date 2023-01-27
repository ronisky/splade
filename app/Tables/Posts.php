<?php

namespace App\Tables;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class Posts extends AbstractTable
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the user is authorized to perform bulk actions and exports.
     *
     * @return bool
     */
    public function authorize(Request $request)
    {
        return true;
    }

    /**
     * The resource or query builder.
     *
     * @return mixed
     */
    public function for()
    {
        return Post::query();
    }

    /**
     * Configure the given SpladeTable.
     *
     * @param \ProtoneMedia\Splade\SpladeTable $table
     * @return void
     */
    public function configure(SpladeTable $table)
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('category_id', 'LIKE', "%{$value}%")
                        ->orWhere('title', 'LIKE', "%{$value}%")
                        ->orWhere('slug', 'LIKE', "%{$value}%");
                });
            });
        });

        $posts = QueryBuilder::for(Post::class)
                ->defaultSort('title')
                ->allowedSorts(['title', 'slug'])
                ->allowedFilters(['title', 'slug', 'category_id', $globalSearch]);

        $categories = Category::orderBy('id')->pluck('name', 'id')->toArray();

        $table
            ->withGlobalSearch(columns: ['title'])
            ->column('id', sortable: true)
            ->column('title', canBeHidden:false, sortable:true)
            ->column('slug', sortable:true)
            ->column('updated_at', sortable:true)
            ->column('action', exportAs:false)
            ->selectFilter('category_id',$categories)
            ->export(label:'Posts Excel',)
            ->bulkAction(
                label: 'Touch timestamp',
                each: fn (Post $post) => $post->touch(),
                before: fn () => info('Touching the selected projects'),
                after: fn () => Toast::info('Timestamps updated!')
            )
            ->bulkAction(
                label: 'Delete Posts',
                each: fn (Post $post) => $post->delete(),
                after: fn () => Toast::info('posts deleted!')
            )
            ->paginate(5);
            // ->searchInput()
            // ->selectFilter()
            // ->withGlobalSearch()

            // ->bulkAction()
            // ->export()
    }
}
