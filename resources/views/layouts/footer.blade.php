<footer class="footer footer-2">
    <div class="footer-middle">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-lg-4">
                    <div class="widget widget-about">
                        <img src="{{ asset('assets/images/logo.png') }}" class="footer-logo" alt="Logo" width="200" height="100">
                        <p>Smart Comfort Deals helps shoppers compare ergonomic cushions, home comfort items, and lifestyle accessories from Amazon, Temu and AliExpress affiliate listings.</p>
                        
                        <div class="widget-about-info">
                            <div class="row">
                                <div class="col-sm-6 col-md-4">
                                    <span class="widget-about-title">Got Question? Call us 24/7</span>
                                    <a href="tel:123456789">+0123 456 789</a>
                                </div><!-- End .col-sm-6 -->
                                <div class="col-sm-6 col-md-8">
                                    <span class="widget-about-title">Affiliate Disclosure</span>
                                    <p class="mb-0">We may earn a commission from qualifying retailer purchases.</p>
                                </div><!-- End .col-sm-6 -->
                            </div><!-- End .row -->
                        </div><!-- End .widget-about-info -->
                    </div><!-- End .widget about-widget -->
                </div><!-- End .col-sm-12 col-lg-4 -->

                <div class="col-sm-4 col-lg-2">
                    <div class="widget">
                        <h4 class="widget-title">Useful links</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="{{ route('about') }}">About Smart Comfort Deals</a></li>
                            <li><a href="{{ route('product-list') }}">All Deals</a></li>
                            <li><a href="{{ route('product-list', ['platform' => 'amazon']) }}">Amazon Picks</a></li>
                            <li><a href="{{ route('product-list', ['platform' => 'temu']) }}">Temu Picks</a></li>
                            <li><a href="{{ route('contact') }}">Contact us</a></li>
                            <li><a href="{{ route('login') }}">Log in</a></li>
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-4 col-lg-2 -->

                <div class="col-sm-4 col-lg-2">
                    <div class="widget">
                        <h4 class="widget-title">Affiliate Info</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="#">Affiliate Disclosure</a></li>
                            <li><a href="#">Price & Availability Notice</a></li>
                            <li><a href="#">Amazon Associate Notice</a></li>
                            <li><a href="#">Temu Partner Notice</a></li>
                            <li><a href="#">Terms and conditions</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-4 col-lg-2 -->

                <div class="col-sm-4 col-lg-2">
                    <div class="widget">
                        <h4 class="widget-title">Admin</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="{{ route('login') }}">Sign In</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-4 col-lg-2 -->

                <div class="col-sm-6 col-lg-2">
                    <div class="widget widget-newsletter">
                        <h4 class="widget-title">Sign up to newsletter</h4><!-- End .widget-title -->

                        <p>Get comfort deal updates and buying guides.</p>
                        
                        <form action="#">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Enter your Email Address" aria-label="Email Adress" required>
                                <div class="input-group-append">
                                    <button class="btn btn-dark" type="submit"><i class="icon-long-arrow-right"></i></button>
                                </div><!-- .End .input-group-append -->
                            </div><!-- .End .input-group -->
                        </form>
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-6 col-lg-2 -->
            </div><!-- End .row -->
        </div><!-- End .container-fluid -->
    </div><!-- End .footer-middle -->

    <div class="footer-bottom">
        <div class="container-fluid">
            <p class="footer-copyright">Copyright © {{ date('Y') }} Smart Comfort Deals. All Rights Reserved.</p><!-- End .footer-copyright -->
            <ul class="footer-menu">
                <li><a href="#">Terms Of Use</a></li>
                <li><a href="#">Privacy Policy</a></li>
            </ul><!-- End .footer-menu -->

            <div class="social-icons social-icons-color">
                <span class="social-label">Social Media</span>
                <a href="#" class="social-icon social-facebook" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                <a href="#" class="social-icon social-twitter" title="Twitter" target="_blank"><i class="icon-twitter"></i></a>
                <a href="#" class="social-icon social-instagram" title="Instagram" target="_blank"><i class="icon-instagram"></i></a>
                <a href="#" class="social-icon social-youtube" title="Youtube" target="_blank"><i class="icon-youtube"></i></a>
                <a href="#" class="social-icon social-pinterest" title="Pinterest" target="_blank"><i class="icon-pinterest"></i></a>
            </div><!-- End .soial-icons -->
        </div><!-- End .container-fluid -->
    </div><!-- End .footer-bottom -->
</footer><!-- End .footer -->
