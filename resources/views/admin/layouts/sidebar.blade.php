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

                <li class="{{ request()->routeIs('admin.products.*') || request()->routeIs('admin.product-review.*') ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.product-review.*') ? 'active' : '' }}">
                        <i class="fa fa-box-open"></i>
                        <span>Manage Product</span>
                    </a>
                    <ul class="sub-menu {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.product-review.*') ? 'mm-show' : '' }}">
                        <li><a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.index') ? 'active' : '' }}">All Products</a></li>
                        <li><a href="{{ route('admin.products.drafts') }}" class="{{ request()->routeIs('admin.products.drafts') ? 'active' : '' }}">Draft Products</a></li>
                        <li><a href="{{ route('admin.products.stock-products') }}" class="{{ request()->routeIs('admin.products.stock-products') ? 'active' : '' }}">Stock Products</a></li>
                        <li><a href="{{ route('admin.product-review.index') }}" class="{{ request()->routeIs('admin.product-review.*') ? 'active' : '' }}">Product Review</a></li>
                    </ul>
                </li>

                <li class="{{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.attributes.*') || request()->routeIs('admin.product-attributes.*') || request()->routeIs('admin.brands.*') ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.attributes.*') || request()->routeIs('admin.product-attributes.*') || request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                        <i class="fa fa-sitemap"></i>
                        <span>Categories & Attributes</span>
                    </a>
                    <ul class="sub-menu {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.attributes.*') || request()->routeIs('admin.product-attributes.*') || request()->routeIs('admin.brands.*') ? 'mm-show' : '' }}">
                        <li><a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Category List</a></li>
                        <li><a href="{{ route('admin.product-attributes.index') }}" class="{{ request()->routeIs('admin.product-attributes.*') ? 'active' : '' }}">Attribute List</a></li>
                        <li><a href="{{ route('admin.attributes.index') }}" class="{{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}">Tag List</a></li>
                        <li><a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">Brand List</a></li>
                    </ul>
                </li>

                <li class="{{ request()->routeIs('admin.manage-inventory.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.manage-inventory.index') }}" class="{{ request()->routeIs('admin.manage-inventory.*') ? 'active' : '' }}">
                        <i class="fa fa-warehouse"></i>
                        <span>Manage Inventory</span>
                    </a>
                </li>
                @endif

                <li class="menu-title">Order Management</li>

                <li class="{{ request()->routeIs('admin.order-management.*') ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow {{ request()->routeIs('admin.order-management.*') ? 'active' : '' }}">
                        <i class="fa fa-shopping-cart"></i>
                        <span>Orders</span>
                    </a>
                    <ul class="sub-menu {{ request()->routeIs('admin.order-management.*') ? 'mm-show' : '' }}">
                        <li><a href="{{ route('admin.order-management.orders') }}" class="{{ request()->routeIs('admin.order-management.orders') ? 'active' : '' }}">All Orders</a></li>
                        <li><a href="{{ route('admin.order-management.returns-refunds') }}" class="{{ request()->routeIs('admin.order-management.returns-refunds') ? 'active' : '' }}">Return & Refund</a></li>
                        <li><a href="{{ route('admin.order-management.abandoned-carts') }}" class="{{ request()->routeIs('admin.order-management.abandoned-carts') ? 'active' : '' }}">Abandoned Cart</a></li>
                        <li><a href="{{ route('admin.order-management.transactions') }}" class="{{ request()->routeIs('admin.order-management.transactions') ? 'active' : '' }}">Transactions</a></li>
                    </ul>
                </li>
            
                {{-- ── USER MANAGEMENT ─────────────────────── --}}
                @if($u->can('admin.users.index') || $u->can('admin.users.roles.index') || $u->can('admin.sellers.index') || $u->can('admin.customers.index'))
                <li class="menu-title">User Management</li>

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
                        <i class="fas fa-store"></i>
                        <span>Roles &amp; Permissions</span>
                    </a>
                </li>
                @endif

                <!-- Sellers -->
                @if($u->can('admin.sellers.index'))
                <li class="{{ request()->routeIs('admin.sellers.*') ? 'mm-active' : '' }}">
                    <a href="{{ Route::has('admin.sellers.index') ? route('admin.sellers.index') : '#' }}"
                       class="{{ request()->routeIs('admin.sellers.*') ? 'active' : '' }}">
                        <i class="fas fa-user-friends"></i>
                        <span>Sellers</span>
                    </a>
                </li>
                @endif

                <!-- Customers -->
                @if($u->can('admin.customers.index'))
                <li class="{{ request()->routeIs('admin.customers.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.customers.index') }}"
                       class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield"></i>
                        <span>Customers</span>
                    </a>
                </li>
                @endif
                @endif

                <li class="menu-title">Reports & Analytics</li>

                <li class="{{ request()->routeIs('admin.reports-analytics.sales-reports') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.reports-analytics.sales-reports') }}"
                       class="{{ request()->routeIs('admin.reports-analytics.sales-reports') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Sales Reports</span>
                    </a>
                </li>   

                <!-- Seller performance -->
                <li class="{{ request()->routeIs('admin.reports-analytics.seller-performance') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.reports-analytics.seller-performance') }}"
                       class="{{ request()->routeIs('admin.reports-analytics.seller-performance') ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i>
                        <span>Seller performance</span>
                    </a>
                </li>

                <!-- top products and categories -->
                <li class="{{ request()->routeIs('admin.reports-analytics.top-products') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.reports-analytics.top-products') }}"
                       class="{{ request()->routeIs('admin.reports-analytics.top-products') ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i>
                        <span>Top products and categories</span>
                    </a>
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
