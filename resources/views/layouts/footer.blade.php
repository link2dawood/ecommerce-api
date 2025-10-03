<!-- Footer Start -->
<div class="container-fluid bg-secondary text-dark mt-5 pt-5">
    <div class="row px-xl-5 pt-5">
        <div class="col-lg-4 col-md-12 mb-5 pr-3 pr-xl-5">
            <a href="{{ url('/') }}" class="text-decoration-none">
                <h1 class="mb-4 display-5 font-weight-semi-bold">
                    <span class="text-primary font-weight-bold border border-white px-3 mr-1">E</span>Shopper
                </h1>
            </a>
            <p>Best place to shop your favorite products online.</p>
            <p class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-3"></i>Your Address</p>
            <p class="mb-2"><i class="fa fa-envelope text-primary mr-3"></i>support@eshopper.com</p>
            <p class="mb-0"><i class="fa fa-phone-alt text-primary mr-3"></i>+012 345 67890</p>
        </div>
        <div class="col-lg-8 col-md-12">
            <div class="row">
                <div class="col-md-4 mb-5">
                    <h5 class="font-weight-bold text-dark mb-4">Quick Links</h5>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-dark mb-2" href="{{ url('/') }}"><i class="fa fa-angle-right mr-2"></i>Home</a>
                        <a class="text-dark mb-2" href="{{ route('shop.index') }}"><i class="fa fa-angle-right mr-2"></i>Shop</a>
                        <a class="text-dark mb-2" href="{{ route('cart.index') }}"><i class="fa fa-angle-right mr-2"></i>Shopping Cart</a>
                        <a class="text-dark mb-2" href="{{ route('checkout') }}"><i class="fa fa-angle-right mr-2"></i>Checkout</a>
                        <a class="text-dark" href="{{ route('contact') }}"><i class="fa fa-angle-right mr-2"></i>Contact Us</a>
                    </div>
                </div>
                <div class="col-md-4 mb-5">
                    <h5 class="font-weight-bold text-dark mb-4">Account</h5>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-dark mb-2" href="{{ route('profile') }}"><i class="fa fa-angle-right mr-2"></i>Profile</a>
                        <a class="text-dark mb-2" href="{{ route('wishlist') }}"><i class="fa fa-angle-right mr-2"></i>Wishlist</a>
                        <a class="text-dark" href="{{ route('orders') }}"><i class="fa fa-angle-right mr-2"></i>Orders</a>
                    </div>
                </div>
                <div class="col-md-4 mb-5">
                    <h5 class="font-weight-bold text-dark mb-4">Newsletter</h5>
                   <form action="{{ route('newsletter.subscribe') }}" method="POST">
    @csrf
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Subscribe</button>
</form>

@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif
@if($errors->any())
    <p style="color: red">{{ $errors->first('email') }}</p>
@endif
                </div>
            </div>
        </div>
    </div>
    <div class="row border-top border-light mx-xl-5 py-4">
        <div class="col-md-6 text-center text-md-left">
            <p class="mb-md-0">&copy; {{ date('Y') }} <a class="text-dark font-weight-semi-bold" href="{{ url('/') }}">Eshopper</a>. All Rights Reserved.</p>
        </div>
        <div class="col-md-6 text-center text-md-right">
            <img class="img-fluid" src="{{ asset('img/payments.png') }}" alt="">
        </div>
    </div>
</div>
<!-- Footer End -->
<form action="{{ route('newsletter.subscribe') }}" method="POST">
    @csrf
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Subscribe</button>
</form>

@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif
@if($errors->any())
    <p style="color: red">{{ $errors->first('email') }}</p>
@endif
