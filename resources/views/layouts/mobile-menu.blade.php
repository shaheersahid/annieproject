<!-- Mobile Menu -->
<div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

<div class="mobile-menu-container">
    <div class="mobile-menu-wrapper">
        <span class="mobile-menu-close"><i class="icon-close"></i></span>

        <form action="{{ route('product-list') }}" method="get" class="mobile-search">
            <label for="mobile-search" class="sr-only">Search</label>
            <input type="search" class="form-control" name="mobile-search" id="mobile-search" placeholder="Search in..." required>
            <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
        </form>
        
        <nav class="mobile-nav">
            <ul class="mobile-menu">
                <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    <a href="{{ url('/') }}">Home</a>
                </li>
                @foreach(($frontendNavCategories ?? collect()) as $category)
                    <li>
                        <a href="{{ route('product-list', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                        @if($category->children->isNotEmpty())
                            <ul>
                                @foreach($category->children as $child)
                                    <li>
                                        <a href="{{ route('product-list', ['category' => $child->slug]) }}">{{ $child->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
                <li>
                    <a href="{{ route('product-list') }}">All Deals</a>
                </li>
                <li class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">
                    <a href="{{ route('blog.index') }}">Blog</a>
                </li>
                <li>
                    <a href="{{ route('about') }}">About</a>
                </li>
                <li>
                    <a href="{{ route('contact') }}">Contact Us</a>
                </li>
            </ul>
        </nav><!-- End .mobile-nav -->

        <div class="social-icons">
            <a href="#" class="social-icon" target="_blank" title="Facebook"><i class="icon-facebook-f"></i></a>
            <a href="#" class="social-icon" target="_blank" title="Twitter"><i class="icon-twitter"></i></a>
            <a href="#" class="social-icon" target="_blank" title="Instagram"><i class="icon-instagram"></i></a>
            <a href="#" class="social-icon" target="_blank" title="Youtube"><i class="icon-youtube"></i></a>
        </div><!-- End .social-icons -->
    </div><!-- End .mobile-menu-wrapper -->
</div><!-- End .mobile-menu-container -->
