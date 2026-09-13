@extends('layouts.main')
@section('title', 'About Smart Comfort Deals')

@section('content')
    <main class="main">
        <div class="page-header">
            <div class="container">
                <h1 class="page-title mb-0">About Us</h1>
            </div>
        </div>

        <nav class="breadcrumb-nav mb-10 pb-1">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>About Us</li>
                </ul>
            </div>
        </nav>

        <div class="page-content pb-10">
            <div class="container">
                <section class="content-title-section mb-6">
                    <h2 class="title title-center mb-4">About Smart Comfort Deals</h2>
                    <div class="mx-auto" style="max-width: 780px;">
                        <p>
                            Smart Comfort Deals helps shoppers find practical comfort upgrades for home, office, and everyday routines.
                            We focus on ergonomic accessories and useful finds — seat cushions, lumbar support, desk setup essentials,
                            and smart home items that make long sitting days and daily tasks more comfortable.
                        </p>
                        <p>
                            Every product we feature is selected for relevance, clarity, and usefulness. We look at product details,
                            buyer needs, and how well an item fits real home or workspace use before adding it to our deal pages.
                            Our goal is to make comparison easier so you can decide faster with less guesswork.
                        </p>
                        <p class="mb-0">
                            Smart Comfort Deals is an affiliate website. When you click through to Amazon, Temu, or AliExpress and make a
                            qualifying purchase, we may earn a commission at no extra cost to you. Prices and availability are set by the
                            retailer at checkout. We share clear disclosures so you always know how the site works.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection
