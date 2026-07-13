@extends('layouts.main')
@section('title', 'Comfort Guides & Buying Advice | Smart Comfort Deals')
@section('meta-description', 'Practical comfort guides, product comparisons and buying advice from Smart Comfort Deals.')

@push('page-styles')
    <style>
        .blog-page { padding-bottom: 7rem; }
        .blog-page .page-header { min-height: 260px; display: flex; align-items: center; background-position: center; background-size: cover; }
        .blog-page .page-title { color: #fff; text-shadow: 0 2px 14px rgba(0, 0, 0, .35); }
        .blog-page .page-title span { color: #fff; opacity: .88; }
        .blog-intro { max-width: 680px; margin: 0 auto 3.5rem; text-align: center; color: #666; font-size: 1.6rem; line-height: 1.75; }
        .blog-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 2.4rem; }
        .blog-card { display: flex; flex-direction: column; min-width: 0; height: 100%; border: 1px solid #e7e7e7; border-radius: 6px; overflow: hidden; background: #fff; transition: transform .2s ease, box-shadow .2s ease; }
        .blog-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(30, 35, 40, .1); }
        .blog-card-media { display: block; aspect-ratio: 16 / 9; overflow: hidden; background: #f2f3f4; }
        .blog-card-media img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
        .blog-card:hover .blog-card-media img { transform: scale(1.025); }
        .blog-card-body { display: flex; flex-direction: column; flex: 1; padding: 2.2rem; }
        .blog-card-meta { margin-bottom: 1rem; color: #888; font-size: 1.25rem; text-transform: uppercase; }
        .blog-card-title { margin: 0 0 1.2rem; font-size: 2rem; line-height: 1.35; }
        .blog-card-title a { color: #222; }
        .blog-card-title a:hover { color: #c96; }
        .blog-card-excerpt { margin-bottom: 1.8rem; color: #666; line-height: 1.7; }
        .blog-card-link { margin-top: auto; color: #222; font-weight: 600; }
        .blog-card-link i { margin-left: .6rem; font-size: 1.1rem; }
        .blog-empty { padding: 7rem 2rem; text-align: center; border: 1px solid #e7e7e7; border-radius: 6px; }
        @media (max-width: 991.98px) { .blog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 575.98px) {
            .blog-page .page-header { min-height: 210px; }
            .blog-grid { grid-template-columns: 1fr; gap: 1.8rem; }
            .blog-card-body { padding: 1.8rem; }
        }
    </style>
@endpush

@section('content')
    <main class="main blog-page">
        <div class="page-header text-center" style="background-image: url('{{ asset('assets/images/page-header-bg.jpg') }}')">
            <div class="container">
                <h1 class="page-title">Comfort Journal<span>Practical guides for smarter choices</span></h1>
            </div>
        </div>

        <nav aria-label="breadcrumb" class="breadcrumb-nav mb-5">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Blog</li>
                </ol>
            </div>
        </nav>

        <div class="container">
            <p class="blog-intro">Buying advice, comfort tips and practical product guides written to help you choose with confidence.</p>

            @if($posts->isNotEmpty())
                <div class="blog-grid">
                    @foreach($posts as $post)
                        <article class="blog-card">
                            @if($post->featured_image)
                                <a class="blog-card-media" href="{{ route('blog.show', $post->slug) }}" aria-label="Read {{ $post->title }}">
                                    <img src="{{ resolve_image_path($post->featured_image) }}" alt="{{ $post->title }}" loading="lazy">
                                </a>
                            @endif
                            <div class="blog-card-body">
                                <div class="blog-card-meta">
                                    <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('M d, Y') }}</time>
                                    <span aria-hidden="true"> &bull; </span>
                                    <span>{{ $post->author?->name ?? 'Editorial Team' }}</span>
                                </div>
                                <h2 class="blog-card-title">
                                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                </h2>
                                @if($post->excerpt)
                                    <p class="blog-card-excerpt">{{ $post->excerpt }}</p>
                                @endif
                                <a href="{{ route('blog.show', $post->slug) }}" class="blog-card-link">
                                    Read article <i class="icon-long-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-5">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="blog-empty">
                    <h2 class="h3 mb-2">New guides coming soon</h2>
                    <p class="text-muted mb-0">Check back for practical comfort advice and buying guides.</p>
                </div>
            @endif
        </div>
    </main>
@endsection
