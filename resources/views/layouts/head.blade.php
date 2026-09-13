<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>@yield('title', 'Smart Comfort Deals | Premium Ergonomic Cushions, Home & Office Comfort Solutions')</title>
<meta name="keywords" content="@yield('meta-keywords', 'Smart Comfort Deals, ergonomic seat cushion, memory foam cushion, office comfort, home ergonomics, lumbar support, lifestyle accessories')">
<meta name="description" content="@yield('meta-description', "Discover Smart Comfort Deals' premium collection of high-quality ergonomic seat cushions, memory foam support, home comfort items, and lifestyle accessories.")">
<meta name="author" content="Smart Comfort Deals">
@stack('seo-head')
<!-- Favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/icons/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/icons/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/icons/favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('assets/images/icons/site.html') }}">
<link rel="mask-icon" href="{{ asset('assets/images/icons/safari-pinned-tab.svg') }}" color="#666666">
<link rel="shortcut icon" href="{{ asset('assets/images/icons/favicon.ico') }}">
<meta name="apple-mobile-web-app-title" content="Smart Comfort Deals">
<meta name="application-name" content="Smart Comfort Deals">
<meta name="msapplication-TileColor" content="#cc9966">
<meta name="msapplication-config" content="{{ asset('assets/images/icons/browserconfig.xml') }}">
<meta name="theme-color" content="#ffffff">
<meta name="p:domain_verify" content="5bff3ac02901bc5e97c8509a2d0c055a"/>

{{-- Tiny critical CSS only — unblocks first paint / LCP --}}
<link rel="stylesheet" href="{{ asset('assets/css/critical-storefront.css') }}?v={{ @filemtime(public_path('assets/css/critical-storefront.css')) ?: time() }}">

@php
    $deferredStyles = [
        asset('assets/css/bootstrap.min.css'),
        asset('assets/css/style.css'),
        asset('assets/css/skin.css'),
        asset('assets/css/main.css'),
        asset('assets/css/custom-storefront.css') . '?v=' . (@filemtime(public_path('assets/css/custom-storefront.css')) ?: time()),
        asset('assets/css/plugins/owl-carousel/owl.carousel.css'),
        asset('assets/css/plugins/magnific-popup/magnific-popup.css'),
    ];
@endphp

{{-- Full theme + plugins: preload + apply without render-blocking --}}
@foreach($deferredStyles as $stylesheet)
    <link rel="preload" href="{{ $stylesheet }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
@endforeach
<noscript>
    @foreach($deferredStyles as $stylesheet)
        <link rel="stylesheet" href="{{ $stylesheet }}">
    @endforeach
</noscript>
<script>
    /*! loadCSS rel=preload polyfill */
    (function (w) {
        "use strict";
        if (!w.document || !w.document.createElement) return;
        var links = w.document.getElementsByTagName("link");
        for (var i = 0; i < links.length; i++) {
            var link = links[i];
            if (link.rel === "preload" && link.getAttribute("as") === "style") {
                link.addEventListener("load", function () {
                    this.onload = null;
                    this.rel = "stylesheet";
                });
            }
        }
    })(window);
</script>

@stack('plugin-styles')
@stack('page-styles')
