<header class="header header-7">
    <div class="header-top">
        <div class="container-fluid d-flex justify-content-center">
            <p class="top-message mb-0 py-2"><strong>Affiliate Disclosure:</strong> We may earn when you buy through Amazon or Temu links.</p>
        </div><!-- End .container-fluid -->
    </div><!-- End .header-top -->

    <div class="header-middle qadir-header-main sticky-header">
        <div class="container-fluid">
            <div class="header-left">
                <button class="mobile-menu-toggler">
                    <span class="sr-only">Toggle mobile menu</span>
                    <i class="icon-bars"></i>
                </button>

                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" height="100"
                        width="200">
                </a>
            </div><!-- End .header-left -->

            <div class="header-right">
                <div class="header-search header-search-extended header-search-visible">
                    <a href="#" class="search-toggle" role="button"><i class="icon-search"></i></a>
                    <form action="{{ route('product-list') }}" method="get">
                        <div class="header-search-wrapper search-wrapper-wide">
                            <label for="q" class="sr-only">Search</label>
                            <input type="search" class="form-control" name="q" id="q"
                                placeholder="Search eyewear deals ..." required>
                            <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
                        </div><!-- End .header-search-wrapper -->
                    </form>
                </div><!-- End .header-search -->

                <a href="{{ route('product-list') }}" class="btn btn-primary">View Deals</a>
            </div><!-- End .header-right -->
        </div><!-- End .container-fluid -->
    </div><!-- End .header-middle -->

    <div class="header-bottom qadir-menu-bar">
        <div class="container-fluid">
            <nav class="main-nav">
                <ul class="menu sf-arrows">
                    <li class="active">
                        <a href="{{ url('/') }}">Home</a>
                    </li>
                    <li>
                        <a href="{{ route('product-list') }}">All Deals</a>
                    </li>
                    @foreach ($frontendNavCategories ?? collect() as $category)
                        <li>
                            <a href="{{ route('product-list', ['category' => $category->slug]) }}"
                                @class(['sf-with-ul' => $category->children->isNotEmpty()])>{{ $category->name }}</a>
                            @if ($category->children->isNotEmpty())
                                <ul>
                                    @foreach ($category->children as $child)
                                        <li>
                                            <a
                                                href="{{ route('product-list', ['category' => $child->slug]) }}">{{ $child->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                    <li>
                        <a href="{{ route('about') }}">About</a>
                    </li>
                    <li>
                        <a href="{{ url('/contact') }}">Contact Us</a>
                    </li>
                </ul><!-- End .menu -->
            </nav><!-- End .main-nav -->
        </div><!-- End .container-fluid -->
    </div><!-- End .header-bottom -->
</header><!-- End .header -->
