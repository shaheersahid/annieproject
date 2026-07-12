@extends('layouts.main')

@section('content')
    <main class="main">
        <!-- Page Header -->
        <div class="page-header text-center" style="background-image: url('{{ asset('assets/images/page-header-bg.jpg') }}')">
            <div class="container">
                <h1 class="page-title">My Account<span>Signature By RaiMal's</span></h1>
            </div>
        </div>

        <!-- Breadcrumbs -->
        <x-breadcrumb :items="['My Account' => null]" />

        <!-- Page Content -->
        <div class="page-content">
            <div class="dashboard">
                <div class="container">
                    <div class="row">
                        <!-- Account Navigation Sidebar -->
                        <aside class="col-md-4 col-lg-3">
                            <ul class="nav nav-dashboard flex-column mb-3 mb-md-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('account.dashboard') ? 'active' : '' }}" 
                                       href="{{ route('account.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('account.orders') ? 'active' : '' }}" 
                                       href="{{ route('account.orders') }}">Orders</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('account.notifications') ? 'active' : '' }}" 
                                       href="{{ route('account.notifications') }}">Notifications</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('account.profile') ? 'active' : '' }}" 
                                       href="{{ route('account.profile') }}">Account Details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" 
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign Out</a>
                                </li>
                            </ul>
                            
                            <!-- Secure Logout Form -->
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </aside>

                        <!-- Sub-Page Dynamic Content Area -->
                        <div class="col-md-8 col-lg-9">
                            <div class="tab-content" style="border: none; padding: 0;">
                                @yield('account-content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
