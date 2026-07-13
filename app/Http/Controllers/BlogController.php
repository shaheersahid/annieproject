<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Contracts\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->with('author')
            ->published()
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('content.blog.index', compact('posts'));
    }

    public function show(BlogPost $post): View
    {
        abort_if(
            $post->status !== 'published' ||
            ! $post->published_at ||
            $post->published_at->isFuture(),
            404
        );

        $post->load('author');

        $relatedPosts = BlogPost::query()
            ->published()
            ->whereKeyNot($post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('content.blog.show', compact('post', 'relatedPosts'));
    }
}
