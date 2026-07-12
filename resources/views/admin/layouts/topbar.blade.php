<!-- Start topbar -->
<header id="page-topbar">
    <div class="navbar-header">

        <!-- Logo -->

        <!-- Start Navbar-Brand -->
        <div class="navbar-logo-box">
            <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="{{ asset('assets/images/qadeer-logo.webp') }}" alt="Qadeer Opticals Logo" style="max-height: 30px; width: auto;">
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('assets/images/qadeer-logo.webp') }}" alt="Qadeer Opticals Logo" style="max-height: 45px; width: auto; object-fit: contain;">
                </span>
            </a>

            <button type="button" class="btn btn-sm top-icon sidebar-btn" id="sidebar-btn">
                <i class="mdi mdi-menu-open align-middle fs-19"></i>
            </button>
        </div>
        <!-- End navbar brand -->

        <!-- Start menu -->
        <div class="d-flex justify-content-end menu-sm px-3 ms-auto">
            <div class="d-flex align-items-center gap-2">
                <!-- Start Notification -->
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn btn-sm top-icon" id="page-header-notifications-dropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell align-middle"></i>
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <span class="btn-marker"><i class="marker marker-dot text-danger"></i><span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-md dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3 bg-info">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="text-white m-0"><i class="far fa-bell me-2"></i> Notifications </h6>
                                </div>
                                <div class="col-auto">
                                    <a href="#!"
                                        class="badge bg-info-subtle text-info">{{ auth()->user()->unreadNotifications->count() }}
                                        New</a>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 350px;">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                @php
                                    $data = $notification->data;
                                    $typeClass = $notification->type;
                                    $order = isset($data['order_id']) ? \App\Models\Order::select('id', 'fulfillment_type')->find($data['order_id']) : null;
                                    $info = match($typeClass) {
                                        'App\Notifications\OrderStatusChanged' => ['icon' => 'mdi-cart-variant', 'color' => 'success', 'url' => $order?->adminRoute() ?? '#'],
                                        'App\Notifications\NewReviewPending' => ['icon' => 'mdi-star-outline', 'color' => 'warning', 'url' => route('admin.reviews.index')],
                                        'App\Notifications\LowStockAlert' => ['icon' => 'mdi-alert-outline', 'color' => 'danger', 'url' => route('admin.manage-inventory.index')],
                                        default => ['icon' => 'mdi-file-document-outline', 'color' => 'primary', 'url' => '#'],
                                    };
                                @endphp
                                <a href="{{ $info['url'] }}" class="text-reset notification-item">
                                    <div class="d-flex">
                                        <div class="avatar avatar-xs avatar-label-{{ $info['color'] }} me-3">
                                            <span class="rounded fs-16">
                                                <i class="mdi {{ $info['icon'] }}"></i>
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <h6 class="mb-1">{{ $data['title'] ?? 'Notification' }}</h6>
                                            <p class="fs-12 text-muted mb-1">{{ $data['message'] ?? '' }}</p>
                                            <div class="fs-12 text-muted">
                                                <p class="mb-0"><i class="mdi mdi-clock-outline"></i>
                                                    {{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <i class="mdi mdi-chevron-right align-middle ms-2"></i>
                                    </div>
                                </a>
                            @empty
                                <div class="p-4 text-center">
                                    <p class="text-muted mb-0">No new notifications</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-2 border-top">
                            <div class="d-grid">
                                <a class="btn btn-sm btn-link font-size-14 text-center"
                                    href="{{ route('admin.notifications.markAllRead') }}">
                                    <i class="mdi mdi-check-all me-1"></i> Mark All as Read
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Notification -->

                <!-- Start Profile -->
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn btn-sm top-icon p-0 d-flex align-items-center justify-content-center" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 40px; height: 40px;">
                        <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px; line-height: 1;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated overflow-hidden py-0">
                        <div class="card border-0">
                            <div class="card-header bg-primary rounded-0">
                                <div class="rich-list-item w-100 p-0">
                                    <div class="rich-list-prepend">
                                        <div class="avatar avatar-label-light avatar-circle">
                                            <div class="avatar-display"><i class="fa fa-user-alt"></i></div>
                                        </div>
                                    </div>
                                    <div class="rich-list-content">
                                        <h3 class="rich-list-title text-white">{{ auth()->user()->name }}</h3>
                                        <span class="rich-list-subtitle text-white">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer card-footer-bordered rounded-0">
                                <a href="{{ route('logout') }}" class="btn btn-label-danger"
                                    onclick="event.preventDefault(); document.getElementById('form-logout').submit();">Sign
                                    out
                                </a>
                                <form id="form-logout" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Profile -->
            </div>
        </div>
        <!-- End menu -->
    </div>
</header>
<!-- End topbar -->
