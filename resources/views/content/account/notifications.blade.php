@extends('content.account.layout')

@section('account-content')
    <div class="tab-pane fade show active" id="tab-notifications" role="tabpanel">
        <h3 class="title mb-3 text-dark font-weight-bold">Notifications</h3>

        @if(isset($notifications) && count($notifications) > 0)
            <div class="notifications-list">
                @foreach($notifications as $notification)
                    <div class="card mb-2" style="border: 1px solid #ebebeb; border-radius: 4px; box-shadow: none;">
                        <div class="card-body p-4 d-flex align-items-start">
                            <div class="mr-3 mt-1">
                                <i class="icon-info-circle text-primary" style="font-size: 2.2rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-muted float-right" style="font-size: 1.1rem;">{{ $notification->created_at->diffForHumans() }}</span>
                                <h4 class="font-weight-bold text-dark mb-1" style="font-size: 1.5rem;">{{ $notification->title }}</h4>
                                <p class="text-muted mb-0" style="font-size: 1.3rem;">{{ $notification->message }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Elegant Welcome / Static Notifications to present premium UX -->
            <div class="notifications-list">
                <!-- Welcome Notice -->
                <div class="card mb-3" style="border: 1px solid #ebebeb; border-radius: 4px; background-color: #fafafa; box-shadow: none;">
                    <div class="card-body p-4 d-flex align-items-start">
                        <div class="mr-4 mt-1">
                            <div style="background-color: rgba(201, 153, 96, 0.15); color: #c96; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="icon-gift" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <span class="text-muted float-right" style="font-size: 1.1rem; letter-spacing: 0;">Just now</span>
                            <h4 class="font-weight-bold text-dark mb-1" style="font-size: 1.5rem;">Welcome to Signature By RaiMal's!</h4>
                            <p class="text-muted mb-0" style="font-size: 1.3rem; line-height: 1.6;">
                                Thank you for creating your account with us. As a welcome gesture, get **10% OFF** your first order by applying the coupon code <span class="font-weight-bold text-primary">WELCOME10</span> at checkout!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Launch Notice -->
                <div class="card mb-3" style="border: 1px solid #ebebeb; border-radius: 4px; box-shadow: none;">
                    <div class="card-body p-4 d-flex align-items-start">
                        <div class="mr-4 mt-1">
                            <div style="background-color: rgba(37, 37, 34, 0.08); color: #252522; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="icon-star" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <span class="text-muted float-right" style="font-size: 1.1rem; letter-spacing: 0;">1 day ago</span>
                            <h4 class="font-weight-bold text-dark mb-1" style="font-size: 1.5rem;">Luxury Summer Lawn Collection Live!</h4>
                            <p class="text-muted mb-0" style="font-size: 1.3rem; line-height: 1.6;">
                                Our highly anticipated Luxury Summer Lawn Collection is now officially live! Explore a premium palette of embroidered luxury ensembles and breathtaking unstitched suits.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
