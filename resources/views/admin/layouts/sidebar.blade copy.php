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

                {{-- ── CATALOG ──────────────────────────────── --}}
                @if($u->can('admin.products.index') || $u->can('admin.categories.index') || $u->can('admin.brands.index'))
                <li class="menu-title">Catalog</li>

                @if($u->can('admin.products.index'))
                <li class="{{ request()->routeIs('admin.products.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.products.index') }}"
                       class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="fa fa-drumstick-bite"></i>
                        <span>Products</span>
                    </a>
                </li>
                @endif

                @if($u->can('admin.categories.index'))
                <li class="{{ request()->routeIs('admin.categories.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}"
                       class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="fa fa-shapes"></i>
                        <span>Categories</span>
                    </a>
                </li>
                @endif

                @if($u->can('admin.brands.index'))
                <li class="{{ request()->routeIs('admin.brands.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.brands.index') }}"
                       class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                        <i class="fa fa-copyright"></i>
                        <span>Brands</span>
                    </a>
                </li>
                @endif
                @endif

                {{-- ── INVENTORY & STOCK ────────────────────── --}}
                @if(Route::has('admin.inventory.index') && $u->can('admin.products.index'))
                <li class="menu-title">Inventory &amp; Stock</li>

                <li class="{{ request()->routeIs('admin.inventory.index') ? 'mm-active' : '' }}">
                    <a href="{{ Route::has('admin.inventory.index') ? route('admin.inventory.index') : '#' }}"
                       class="{{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}">
                        <i class="fa fa-boxes"></i>
                        <span>Inventory</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.inventory.stockMovements') ? 'mm-active' : '' }}">
                    <a href="{{ Route::has('admin.inventory.stockMovements') ? route('admin.inventory.stockMovements') : '#' }}"
                       class="{{ request()->routeIs('admin.inventory.stockMovements') ? 'active' : '' }}">
                        <i class="fa fa-exchange-alt"></i>
                        <span>Stock Movements</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.inventory.lowStock') ? 'mm-active' : '' }}">
                    <a href="{{ Route::has('admin.inventory.lowStock') ? route('admin.inventory.lowStock') : '#' }}"
                       class="{{ request()->routeIs('admin.inventory.lowStock') ? 'active' : '' }}">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span>Low Stock</span>
                    </a>
                </li>
                @endif

                {{-- ── SALES ────────────────────────────────── --}}
                @if($u->can('admin.orders.index'))
                <li class="menu-title">Sales</li>

                <li class="{{ request()->routeIs('admin.orders.index') ? 'mm-active' : '' }}">
                    <a href="{{ Route::has('admin.orders.index') ? route('admin.orders.index') : '#' }}"
                       class="{{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                        <i class="fa fa-shopping-cart"></i>
                        <span>Orders</span>
                    </a>
                </li>

                @if(Route::has('admin.sales-returns.index'))
                <li class="{{ request()->routeIs('admin.sales-returns.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.sales-returns.index') }}"
                       class="{{ request()->routeIs('admin.sales-returns.*') ? 'active' : '' }}">
                        <i class="fa fa-undo"></i>
                        <span>Sales Returns</span>
                    </a>
                </li>
                @endif
                @endif

                {{-- ── FINANCE ──────────────────────────────── --}}
                @if($u->can('admin.expenses.index'))
                <li class="menu-title">Finance</li>

                <li class="{{ request()->routeIs('admin.expenses.*') ? 'mm-active' : '' }}">
                    <a href="{{ Route::has('admin.expenses.index') ? route('admin.expenses.index') : '#' }}"
                       class="{{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                        <i class="fa fa-receipt"></i>
                        <span>Expenses</span>
                    </a>
                </li>

                @if(Route::has('admin.reports.sales'))
                <li class="{{ request()->routeIs('admin.reports.*') ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);"
                       class="has-arrow {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="fa fa-chart-line"></i>
                        <span>Reports</span>
                    </a>
                    <ul class="sub-menu {{ request()->routeIs('admin.reports.*') ? 'mm-show' : '' }}" aria-expanded="false">
                        <li><a href="{{ route('admin.reports.sales') }}" class="{{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}">Sales</a></li>
                        <li><a href="{{ Route::has('admin.reports.inventory') ? route('admin.reports.inventory') : '#' }}" class="{{ request()->routeIs('admin.reports.inventory') ? 'active' : '' }}">Inventory</a></li>
                        <li><a href="{{ Route::has('admin.reports.profit-loss') ? route('admin.reports.profit-loss') : '#' }}" class="{{ request()->routeIs('admin.reports.profit-loss') ? 'active' : '' }}">Profit &amp; Loss</a></li>
                    </ul>
                </li>
                @endif
                @endif

                {{-- ── USER MANAGEMENT ─────────────────────── --}}
                @if($u->can('admin.users.index') || $u->can('admin.users.roles.index'))
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
                        <i class="fas fa-user-shield"></i>
                        <span>Roles &amp; Permissions</span>
                    </a>
                </li>
                @endif
                @endif

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
