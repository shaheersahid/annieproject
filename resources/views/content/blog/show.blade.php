@extends('layouts.main')
@section('title', ($post->meta_title ?: $post->title) . ' | Smart Comfort Deals')
@section('meta-description', $post->meta_description ?: $post->excerpt ?: 'Read the latest comfort guide from Smart Comfort Deals.')

@push('page-styles')
    <style>
        .article-page { padding-bottom: 7rem; }
        .article-shell { max-width: 1180px; margin: 0 auto; }
        .article-header { max-width: 860px; margin: 2rem auto 3.2rem; text-align: center; }
        .article-kicker { margin-bottom: 1.2rem; color: #c96; font-size: 1.25rem; font-weight: 700; text-transform: uppercase; }
        .article-title { margin-bottom: 1.5rem; color: #202124; font-size: 4.2rem; line-height: 1.15; }
        .article-meta { color: #777; font-size: 1.4rem; }
        .article-hero { width: 100%; max-height: 560px; aspect-ratio: 16 / 8; margin-bottom: 4rem; object-fit: cover; border-radius: 6px; }
        .article-layout { display: grid; grid-template-columns: minmax(0, 760px) minmax(220px, 1fr); gap: 5rem; align-items: start; justify-content: center; }
        .article-layout.no-toc { grid-template-columns: minmax(0, 820px); }
        .article-lead { margin-bottom: 2.8rem; padding-left: 2rem; border-left: 3px solid #c96; color: #4e5155; font-size: 2rem; line-height: 1.65; }
        .article-content { color: #35383c; font-size: 1.7rem; line-height: 1.85; }
        .article-content h2 { margin: 4rem 0 1.4rem; color: #202124; font-size: 2.8rem; line-height: 1.3; scroll-margin-top: 100px; }
        .article-content h3 { margin: 3rem 0 1.2rem; color: #202124; font-size: 2.2rem; line-height: 1.35; scroll-margin-top: 100px; }
        .article-content p, .article-content ul, .article-content ol { margin-bottom: 1.8rem; }
        .article-content img { max-width: 100%; height: auto; margin: 2rem 0; border-radius: 6px; }
        .article-content blockquote { margin: 2.5rem 0; padding: 1.8rem 2.2rem; border-left: 3px solid #c96; background: #f7f7f7; color: #454545; }
        .article-toc { position: sticky; top: 95px; padding: 2.2rem; border: 1px solid #e5e5e5; border-radius: 6px; background: #fff; }
        .article-toc-title { margin-bottom: 1.4rem; color: #222; font-size: 1.5rem; font-weight: 700; }
        .article-toc ul { margin: 0; padding: 0; list-style: none; }
        .article-toc li { margin: 0 0 1rem; line-height: 1.45; }
        .article-toc li.toc-h3 { padding-left: 1.4rem; font-size: 1.35rem; }
        .article-toc a { color: #656565; }
        .article-toc a:hover { color: #c96; }
        .related-articles { margin-top: 7rem; padding-top: 4rem; border-top: 1px solid #e5e5e5; }
        .related-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 2.4rem; }
        .related-item { min-width: 0; }
        .related-item img { width: 100%; aspect-ratio: 16 / 9; object-fit: cover; margin-bottom: 1.4rem; border-radius: 4px; }
        .related-item h3 { font-size: 1.8rem; line-height: 1.4; }
        @media (max-width: 991.98px) {
            .article-title { font-size: 3.5rem; }
            .article-layout { grid-template-columns: 1fr; gap: 3rem; }
            .article-toc { position: static; grid-row: 1; }
        }
        @media (max-width: 575.98px) {
            .article-title { font-size: 3rem; }
            .article-header { margin-top: 1rem; }
            .article-hero { margin-bottom: 3rem; }
            .article-content { font-size: 1.6rem; }
            .related-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    <main class="main article-page">
        <nav aria-label="breadcrumb" class="breadcrumb-nav mb-2">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $post->title }}</li>
                </ol>
            </div>
        </nav>

        <div class="container">
            <div class="article-shell">
                <header class="article-header">
                    <p class="article-kicker">Comfort Journal</p>
                    <h1 class="article-title">{{ $post->title }}</h1>
                    <p class="article-meta">
                        By <strong>{{ $post->author?->name ?? 'Editorial Team' }}</strong>
                        <span aria-hidden="true"> &bull; </span>
                        <time datetime="{{ $post->published_at?->toIso8601String() }}">{{ $post->published_at?->format('F d, Y') }}</time>
                    </p>
                </header>

                @if($post->featured_image)
                    <img class="article-hero" src="{{ resolve_image_path($post->featured_image) }}" alt="{{ $post->title }}">
                @endif

                <div class="article-layout {{ empty($post->toc) ? 'no-toc' : '' }}">
                    <article>
                        @if($post->excerpt)
                            <p class="article-lead">{{ $post->excerpt }}</p>
                        @endif
                        <div class="article-content">
                            {!! $post->rendered_content !!}
                        </div>
                    </article>

                    @if(!empty($post->toc))
                        <aside class="article-toc" aria-label="Table of contents">
                            <p class="article-toc-title">In this article</p>
                            <ul>
                                @foreach($post->toc as $item)
                                    <li class="{{ $item['level'] === 3 ? 'toc-h3' : '' }}">
                                        <a href="#{{ $item['anchor'] }}">{{ $item['text'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </aside>
                    @endif
                </div>

                @if($relatedPosts->isNotEmpty())
                    <section class="related-articles" aria-labelledby="related-title">
                        <h2 id="related-title" class="title mb-4">More from Comfort Journal</h2>
                        <div class="related-grid">
                            @foreach($relatedPosts as $related)
                                <article class="related-item">
                                    @if($related->featured_image)
                                        <a href="{{ route('blog.show', $related->slug) }}">
                                            <img src="{{ resolve_image_path($related->featured_image) }}" alt="{{ $related->title }}" loading="lazy">
                                        </a>
                                    @endif
                                    <p class="text-muted small mb-1">{{ $related->published_at?->format('M d, Y') }}</p>
                                    <h3><a href="{{ route('blog.show', $related->slug) }}">{{ $related->title }}</a></h3>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </main>
@endsection
