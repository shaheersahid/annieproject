<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlogPostRequest;
use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

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

    public function store(BlogPostRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $data['author_id'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->storeFeaturedImage($request->file('featured_image'));
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        unset($data['publish_date'], $data['publish_time']);

        BlogPost::create($data);

        return $this->savedResponse($request, 'Blog post created.');
    }

    public function edit(BlogPost $blog): View
    {
        return view('admin.content.blog.edit', compact('blog'));
    }

    public function update(BlogPostRequest $request, BlogPost $blog): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        $oldImage = $blog->featured_image;

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->storeFeaturedImage($request->file('featured_image'));
        } else {
            unset($data['featured_image']);
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        unset($data['publish_date'], $data['publish_time']);

        $blog->update($data);

        if (isset($data['featured_image']) && $oldImage && $oldImage !== $data['featured_image']) {
            Storage::disk('public')->delete($oldImage);
        }

        return $this->savedResponse($request, 'Blog post updated.');
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

    private function storeFeaturedImage(UploadedFile $image): string
    {
        try {
            $path = $image->store('blog', 'public');
        } catch (Throwable $exception) {
            report($exception);
            $path = false;
        }

        if (! $path) {
            throw ValidationException::withMessages([
                'featured_image' => 'Image could not be saved. Check storage permissions and try again.',
            ]);
        }

        return $path;
    }

    private function savedResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('admin.blog.index'),
            ]);
        }

        return redirect()->route('admin.blog.index')->with('success', $message);
    }
}
