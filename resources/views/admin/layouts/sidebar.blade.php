<!-- ========== Left Sidebar Start ========== -->
<div class="sidebar-left">
    <div data-simplebar class="h-100">
        <!--- Sidebar-menu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="left-menu list-unstyled" id="side-menu">
                @php $u = auth()->user(); @endphp

                {{-- ── MAIN ─────────────────────────────────── --}}
                <li class="menu-title">Main</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="{{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-desktop"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if($u->can('admin.notifications.index'))
                <li class="{{ request()->routeIs('admin.notifications.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.notifications.index') }}"
                       class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                        <i class="fa fa-bell"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                @endif

                @if($u->can('admin.audit-logs.index'))
                <li class="{{ request()->routeIs('admin.audit-logs.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.audit-logs.index') }}"
                       class="{{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Audit Logs</span>
                    </a>
                </li>
                @endif

                {{-- ── CATALOG ──────────────────────────────── --}}
                @if($u->can('admin.products.index') || $u->can('admin.categories.index') || $u->can('admin.brands.index'))
                <li class="menu-title">Product Management</li>

                <li class="{{ request()->routeIs('admin.products.*') ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="fa fa-box-open"></i>
                        <span>Products</span>
                    </a>
                    <ul class="sub-menu {{ request()->routeIs('admin.products.*') ? 'mm-show' : '' }}">
                        <li><a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.index') ? 'active' : '' }}">All Products</a></li>
                        <li><a href="{{ route('admin.products.drafts') }}" class="{{ request()->routeIs('admin.products.drafts') ? 'active' : '' }}">Drafts</a></li>
                        <li><a href="{{ route('admin.products.create') }}" class="{{ request()->routeIs('admin.products.create') ? 'active' : '' }}">Add Product</a></li>
                    </ul>
                </li>

                <li class="{{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.attributes.*') || request()->routeIs('admin.product-attributes.*') || request()->routeIs('admin.brands.*') ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.attributes.*') || request()->routeIs('admin.product-attributes.*') || request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                        <i class="fa fa-sitemap"></i>
                        <span>Categories & Attributes</span>
                    </a>
                    <ul class="sub-menu {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.attributes.*') || request()->routeIs('admin.product-attributes.*') || request()->routeIs('admin.brands.*') ? 'mm-show' : '' }}">
                        <li><a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Categories</a></li>
                        <li><a href="{{ route('admin.product-attributes.index') }}" class="{{ request()->routeIs('admin.product-attributes.*') ? 'active' : '' }}">Attributes</a></li>
                        <li><a href="{{ route('admin.attributes.index') }}" class="{{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}">Tags</a></li>
                        <li><a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">Brands</a></li>
                    </ul>
                </li>
                @endif

                {{-- ── USER MANAGEMENT ─────────────────────── --}}
                @if($u->can('admin.users.index') || $u->can('admin.users.roles.index'))
                <li class="menu-title">Admin Users</li>

                @if($u->can('admin.users.index'))
                <li class="{{ request()->routeIs('admin.users.index') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.users.index') }}"
                       class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                @endif

                @if($u->can('admin.users.roles.index'))
                <li class="{{ request()->routeIs('admin.users.roles.*') ? 'mm-active' : '' }}">
                    <a href="{{ Route::has('admin.users.roles.index') ? route('admin.users.roles.index') : '#' }}"
                       class="{{ request()->routeIs('admin.users.roles.*') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt"></i>
                        <span>Roles &amp; Permissions</span>
                    </a>
                </li>
                @endif
                @endif

                {{-- ── ANALYTICS ────────────────────────────── --}}
                <li class="menu-title">Analytics</li>

                <li class="{{ request()->routeIs('admin.reports-analytics.top-products') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.reports-analytics.top-products') }}"
                       class="{{ request()->routeIs('admin.reports-analytics.top-products') ? 'active' : '' }}">
                        <i class="fas fa-fire"></i>
                        <span>Top Products</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.reports-analytics.categories') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.reports-analytics.categories') }}"
                       class="{{ request()->routeIs('admin.reports-analytics.categories') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Category Stats</span>
                    </a>
                </li>

                {{-- ── CONTENT ──────────────────────────────── --}}
                <li class="menu-title">Content</li>

                <li class="{{ request()->routeIs('admin.blog.*') ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
                        <i class="fas fa-blog"></i>
                        <span>Blog</span>
                    </a>
                    <ul class="sub-menu {{ request()->routeIs('admin.blog.*') ? 'mm-show' : '' }}">
                        <li><a href="{{ Route::has('admin.blog.index') ? route('admin.blog.index') : '#' }}" class="{{ request()->routeIs('admin.blog.index') ? 'active' : '' }}">All Posts</a></li>
                        <li><a href="{{ Route::has('admin.blog.create') ? route('admin.blog.create') : '#' }}" class="{{ request()->routeIs('admin.blog.create') ? 'active' : '' }}">Write Post</a></li>
                    </ul>
                </li>

                {{-- ── MARKETING ────────────────────────────── --}}
                @if($u->can('admin.newsletter.index') || $u->can('admin.contact.index'))
                <li class="menu-title">Marketing</li>

                @if($u->can('admin.newsletter.index'))
                <li class="{{ request()->routeIs('admin.newsletter.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.newsletter.index') }}"
                       class="{{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}">
                        <i class="fa fa-envelope"></i>
                        <span>Newsletter</span>
                    </a>
                </li>
                @endif

                @if($u->can('admin.contact.index'))
                <li class="{{ request()->routeIs('admin.contact.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.contact.index') }}"
                       class="{{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">
                        <i class="fa fa-envelope-open-text"></i>
                        <span>Contact Messages</span>
                    </a>
                </li>
                @endif
                @endif

                {{-- ── SYSTEM ──────────────────────────────── --}}
                @if($u->can('admin.settings.index'))
                <li class="menu-title">System</li>
                <li class="{{ request()->routeIs('admin.settings.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.settings.index') }}"
                       class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fa fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
                @endif

                {{-- ── ACCOUNT ──────────────────────────────── --}}
                <li class="menu-title">Account</li>
                <li class="{{ request()->routeIs('admin.profile.*') ? 'mm-active' : '' }}">
                    <a href="{{ Route::has('admin.profile.edit') ? route('admin.profile.edit') : '#' }}"
                       class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                        <i class="fa fa-user-circle"></i>
                        <span>My Profile</span>
                    </a>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
