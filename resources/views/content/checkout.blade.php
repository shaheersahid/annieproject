@extends('layouts.main')

@section('content')
<main class="main">
    <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
        <div class="container">
            <h1 class="page-title">Checkout<span>Shop</span></h1>
        </div>
    </div>

    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart') }}">Shopping Cart</a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
            </ol>
        </div>
    </nav>

    <div class="page-content">
        <div class="checkout">
            <div class="container">
                <div class="checkout-discount">
                    <form action="#">
                        <input type="text" class="form-control" required id="checkout-discount-input" placeholder="Coupon code">
                        <button type="submit" class="btn btn-outline-primary-2">Apply coupon</button>
                    </form>
                </div>

                <form action="#" method="post">
                    @csrf
                    <div class="row">
                        <!-- Billing Details -->
                        <div class="col-lg-9">
                            <h2 class="checkout-title">Billing Details</h2>

                            <div class="row">
                                <div class="col-sm-6">
                                    <label>First Name *</label>
                                    <input type="text" class="form-control" name="first_name" required>
                                </div>
                                <div class="col-sm-6">
                                    <label>Last Name *</label>
                                    <input type="text" class="form-control" name="last_name" required>
                                </div>
                            </div>

                            <label>Company Name (Optional)</label>
                            <input type="text" class="form-control" name="company">

                            <label>Country / Region *</label>
                            <select class="form-control" name="country" required>
                                <option value="" disabled selected>Select a country...</option>
                                <option value="PK">Pakistan</option>
                                <option value="US">United States</option>
                                <option value="GB">United Kingdom</option>
                                <option value="AE">UAE</option>
                                <option value="CA">Canada</option>
                                <option value="AU">Australia</option>
                            </select>

                            <label>Street Address *</label>
                            <input type="text" class="form-control" name="address1" placeholder="House number and street name" required>
                            <input type="text" class="form-control mt-2" name="address2" placeholder="Apartment, suite, unit etc. (optional)">

                            <label>Town / City *</label>
                            <input type="text" class="form-control" name="city" required>

                            <label>State / County *</label>
                            <input type="text" class="form-control" name="state" required>

                            <label>Postcode / ZIP *</label>
                            <input type="text" class="form-control" name="postcode" required>

                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Phone *</label>
                                    <input type="tel" class="form-control" name="phone" required>
                                </div>
                                <div class="col-sm-6">
                                    <label>Email Address *</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>

                            <div class="custom-control custom-checkbox mt-3 mb-3">
                                <input type="checkbox" class="custom-control-input" id="checkout-create-acc">
                                <label class="custom-control-label" for="checkout-create-acc">Create an account?</label>
                            </div>

                            <div class="custom-control custom-checkbox mb-5">
                                <input type="checkbox" class="custom-control-input" id="checkout-diff-address">
                                <label class="custom-control-label" for="checkout-diff-address">Ship to a different address?</label>
                            </div>

                            <label>Order Notes (Optional)</label>
                            <textarea class="form-control" name="order_notes" cols="30" rows="4" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
                        </div>

                        <!-- Order Summary -->
                        <aside class="col-lg-3">
                            <div class="summary">
                                <h3 class="summary-title">Your Order</h3>

                                <table class="table table-summary">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="summary-subtotal">
                                            <td>Subtotal:</td>
                                            <td>PKR 140.00</td>
                                        </tr>
                                        <tr class="summary-shipping">
                                            <td>Shipping:</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr class="summary-shipping-row">
                                            <td>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="free-shipping-2" name="shipping" class="custom-control-input" checked>
                                                    <label class="custom-control-label" for="free-shipping-2">Free shipping</label>
                                                </div>
                                            </td>
                                            <td>FREE</td>
                                        </tr>
                                        <tr class="summary-shipping-row">
                                            <td>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="standard-shipping-2" name="shipping" class="custom-control-input">
                                                    <label class="custom-control-label" for="standard-shipping-2">Standard:</label>
                                                </div>
                                            </td>
                                            <td>PKR 10.00</td>
                                        </tr>
                                        <tr class="summary-shipping-row">
                                            <td>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="express-shipping-2" name="shipping" class="custom-control-input">
                                                    <label class="custom-control-label" for="express-shipping-2">Express:</label>
                                                </div>
                                            </td>
                                            <td>PKR 20.00</td>
                                        </tr>
                                        <tr class="summary-total">
                                            <td>Total:</td>
                                            <td>PKR 140.00</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="accordion-summary" id="accordion-payment">
                                    <div class="card">
                                        <div class="card-header" id="heading-payment-1">
                                            <h2 class="card-title">
                                                <a role="button" data-toggle="collapse" href="#payment-1" aria-expanded="true" aria-controls="payment-1">
                                                    Direct bank transfer
                                                </a>
                                            </h2>
                                        </div>
                                        <div id="payment-1" class="collapse show" aria-labelledby="heading-payment-1" data-parent="#accordion-payment">
                                            <div class="card-body">
                                                Make your payment directly into our bank account. Your order will not be shipped until the funds have cleared.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header" id="heading-payment-2">
                                            <h2 class="card-title">
                                                <a class="collapsed" role="button" data-toggle="collapse" href="#payment-2" aria-expanded="false" aria-controls="payment-2">
                                                    Cash on delivery
                                                </a>
                                            </h2>
                                        </div>
                                        <div id="payment-2" class="collapse" aria-labelledby="heading-payment-2" data-parent="#accordion-payment">
                                            <div class="card-body">
                                                Pay with cash upon delivery.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header" id="heading-payment-3">
                                            <h2 class="card-title">
                                                <a class="collapsed" role="button" data-toggle="collapse" href="#payment-3" aria-expanded="false" aria-controls="payment-3">
                                                    Credit / Debit Card
                                                </a>
                                            </h2>
                                        </div>
                                        <div id="payment-3" class="collapse" aria-labelledby="heading-payment-3" data-parent="#accordion-payment">
                                            <div class="card-body">
                                                <figure class="footer-payments mt-2 mb-3">
                                                    <img src="assets/images/payments.png" alt="Payment methods" width="272" height="20">
                                                </figure>
                                                <div class="form-group">
                                                    <label for="card-number">Card Number *</label>
                                                    <input type="text" class="form-control" id="card-number" placeholder="**** **** **** ****">
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label for="card-expiry">Expiry Date *</label>
                                                        <input type="text" class="form-control" id="card-expiry" placeholder="MM / YY">
                                                    </div>
                                                    <div class="col-6">
                                                        <label for="card-cvv">CVV *</label>
                                                        <input type="text" class="form-control" id="card-cvv" placeholder="CVV">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-outline-primary-2 btn-order btn-block mt-4">
                                    <span class="btn-text">Place Order</span>
                                    <span class="btn-hover-text">Proceed to Checkout</span>
                                </button>
                            </div>
                        </aside>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
