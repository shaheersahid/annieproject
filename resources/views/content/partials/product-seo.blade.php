@php
    $seoTitle = $product->getSeoTitle();
    $seoDescription = $product->getSeoDescription();
    $seoImage = $product->getSeoImage();
    $seoUrl = $product->getSeoUrl();
    $ogTitle = $product->seoField('og_fields', 'title', $seoTitle);
    $ogType = $product->seoField('og_fields', 'type', 'product');
    $ogDescription = $product->seoField('og_fields', 'description', $seoDescription);
    $ogImage = $product->seoField('og_fields', 'image', $seoImage);
    $ogUrl = $product->seoField('og_fields', 'url', $seoUrl);
    $twitterCard = $product->seoField('twitter_fields', 'card', 'summary_large_image');
    $twitterSite = $product->seoField('twitter_fields', 'site');
    $twitterTitle = $product->seoField('twitter_fields', 'title', $ogTitle);
    $twitterDescription = $product->seoField('twitter_fields', 'description', $ogDescription);
    $twitterImage = $product->seoField('twitter_fields', 'image', $ogImage);
@endphp

<link rel="canonical" href="{{ $product->getSeoCanonical() }}">
<meta name="robots" content="{{ $product->getSeoRobots() }}">
<meta property="og:site_name" content="Smart Comfort Deals">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ $ogUrl }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta name="twitter:card" content="{{ $twitterCard }}">
@if($twitterSite)
    <meta name="twitter:site" content="{{ $twitterSite }}">
@endif
<meta name="twitter:title" content="{{ $twitterTitle }}">
<meta name="twitter:description" content="{{ $twitterDescription }}">
<meta name="twitter:image" content="{{ $twitterImage }}">
<script type="application/ld+json">
{!! json_encode($product->resolvedSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
