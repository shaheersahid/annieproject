@extends('layouts.main')
@section('title', 'Contact Smart Comfort Deals')

@section('content')
    <main class="main">
        <div class="page-header">
            <div class="container">
                <h1 class="page-title mb-0">Contact Us</h1>
            </div>
        </div>

        <nav class="breadcrumb-nav mb-10 pb-1">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Contact Us</li>
                </ul>
            </div>
        </nav>

        <div class="page-content contact-us">
            <div class="container">
                <section class="content-title-section mb-8">
                    <h2 class="title title-center mb-3">Get in Touch</h2>
                    <p class="text-center mb-0">
                        Questions about a deal, affiliate disclosure, or product recommendation?
                        Email us or send a message below — we typically reply within 1–2 business days.
                    </p>
                </section>

                <section class="contact-information-section mb-8">
                    <div class="row justify-content-center">
                        <div class="col-md-6 col-lg-4">
                            <div class="icon-box text-center icon-box-primary">
                                <span class="icon-box-icon icon-email">
                                    <i class="w-icon-envelop-closed"></i>
                                </span>
                                <div class="icon-box-content">
                                    <h3 class="icon-box-title">Support Email</h3>
                                    <p class="mb-0">
                                        <a href="mailto:support@smartcomfortdeals.store">support@smartcomfortdeals.store</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <hr class="divider mb-8">

                <section class="contact-section">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <h3 class="title mb-3">Send Us a Message</h3>

                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <form class="form contact-us-form" action="{{ route('contact.store') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Your Name</label>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="email">Your Email</label>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="subject">Subject <span class="text-muted">(optional)</span></label>
                                    <input type="text" id="subject" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}">
                                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="message">Your Message</label>
                                    <textarea id="message" name="message" cols="30" rows="6" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                                    @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <button type="submit" class="btn btn-dark btn-rounded">Send Message</button>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection
