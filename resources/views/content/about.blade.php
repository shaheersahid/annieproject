@extends('layouts.main')

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
                    <h3 class="title title-center mb-3">Qadir Optics</h3>
                    <p class="text-center">Quality eyewear, lenses, and optical accessories selected for everyday comfort and clear vision.</p>
                </section>
            </div>
        </div>
    </main>
@endsection
