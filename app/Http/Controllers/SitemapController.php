<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect();

        foreach ($this->staticPages() as $page) {
            $urls->push($page);
        }

        Category::query()
            ->active()
            ->ordered()
            ->get(['id', 'slug', 'updated_at'])
            ->each(function (Category $category) use ($urls): void {
                $urls->push([
                    'loc' => route('product-list', ['category' => $category->slug]),
                    'lastmod' => optional($category->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ]);
            });

        Product::query()
            ->published()
            ->orderByDesc('updated_at')
            ->get(['id', 'slug', 'updated_at'])
            ->each(function (Product $product) use ($urls): void {
                $urls->push([
                    'loc' => route('product-detail', $product),
                    'lastmod' => optional($product->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ]);
            });

        BlogPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->get(['id', 'slug', 'updated_at', 'published_at'])
            ->each(function (BlogPost $post) use ($urls): void {
                $urls->push([
                    'loc' => route('blog.show', $post),
                    'lastmod' => optional($post->updated_at ?: $post->published_at)->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ]);
            });

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function staticPages(): array
    {
        return [
            [
                'loc' => route('home'),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('product-list'),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => route('latest-deals'),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => route('blog.index'),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('about'),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'loc' => route('contact'),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
        ];
    }
}
