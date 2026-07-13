<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlogPostRequest;
use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function __construct(protected DataTableServiceInterface $dataTable) {}

    public function index(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getBlogPostsDataTable();
        }

        return view('admin.content.blog.index');
    }

    public function create(): View
    {
        return view('admin.content.blog.create');
    }

    public function store(BlogPostRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['author_id'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        unset($data['publish_date'], $data['publish_time']);

        BlogPost::create($data);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post created.');
    }

    public function edit(BlogPost $blog): View
    {
        return view('admin.content.blog.edit', compact('blog'));
    }

    public function update(BlogPostRequest $request, BlogPost $blog): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('featured_image')) {
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        } else {
            unset($data['featured_image']);
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        unset($data['publish_date'], $data['publish_time']);

        $blog->update($data);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post updated.');
    }

    public function destroy(BlogPost $blog): RedirectResponse
    {
        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }

        $blog->delete();

        return redirect()->route('admin.blog.index')->with('success', 'Post deleted.');
    }

    public function toggleStatus(Request $request): JsonResponse
    {
        $post = BlogPost::findOrFail($request->integer('id'));

        $publish = $request->boolean('value');

        $post->update([
            'status'       => $publish ? 'published' : 'draft',
            'published_at' => $publish ? ($post->published_at ?? now()) : null,
        ]);

        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }
}
