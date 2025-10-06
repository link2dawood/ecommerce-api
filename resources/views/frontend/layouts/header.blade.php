<!-- Topbar Start -->
<div class="container-fluid">
    <div class="row align-items-center py-3 px-xl-5">
        <div class="col-lg-3 d-none d-lg-block">
            <a href="{{ route('home') }}" class="text-decoration-none">
                <h1 class="m-0 display-5 font-weight-semi-bold">
                    <span class="text-primary font-weight-bold border px-3 mr-1">E</span>Shopper
                </h1>
            </a>
        </div>
        <div class="col-lg-6 col-6 text-left">
            <form action="{{ route('shop.search') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search for products" value="{{ request('q') }}">
                    <div class="input-group-append">
                        <button class="input-group-text bg-transparent text-primary border-0" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-3 col-6 text-right">
            <a href="{{ route('wishlist.index') }}" class="btn border">
                <i class="fas fa-heart text-primary"></i>
                <span class="badge">{{ auth()->check() ? auth()->user()->wishlists()->count() : 0 }}</span>
            </a>
            <a href="{{ route('cart.index') }}" class="btn border">
                <i class="fas fa-shopping-cart text-primary"></i>
                <span class="badge">{{ session('cart_count', 0) }}</span>
            </a>
        </div>
    </div>
</div>
<!-- Topbar End -->

<!-- Navbar Start -->
<div class="container-fluid mb-5">
    <div class="row border-top px-xl-5">
        <div class="col-lg-3 d-none d-lg-block">
            <a class="btn shadow-none d-flex align-items-center justify-content-between bg-primary text-white w-100"
               data-toggle="collapse" href="#navbar-vertical" style="height: 65px; margin-top: -1px; padding: 0 30px;">
                <h6 class="m-0">Categories</h6>
                <i class="fa fa-angle-down text-dark"></i>
            </a>
            <nav class="collapse @if(request()->routeIs('home')) show @endif navbar navbar-vertical navbar-light align-items-start p-0 border border-top-0 border-bottom-0" id="navbar-vertical">
                <div class="navbar-nav w-100 overflow-hidden" style="height: 410px">
                    @forelse($categories ?? [] as $category)
                        @if($category->children && $category->children->count() > 0)
                            <div class="nav-item dropdown">
                                <a href="{{ route('category.show', $category->slug) }}" class="nav-link" data-toggle="dropdown">
                                    {{ $category->name }} <i class="fa fa-angle-down float-right mt-1"></i>
                                </a>
                                <div class="dropdown-menu position-absolute bg-secondary border-0 rounded-0 w-100 m-0">
                                    @foreach($category->children as $subcategory)
                                        <a href="{{ route('category.show', $subcategory->slug) }}" class="dropdown-item">
                                            {{ $subcategory->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ route('category.show', $category->slug) }}" class="nav-item nav-link">
                                {{ $category->name }}
                            </a>
                        @endif
                    @empty
                        <a href="#" class="nav-item nav-link text-muted">No categories available</a>
                    @endforelse
                </div>
            </nav>
        </div>
        <div class="col-lg-9">
            <nav class="navbar navbar-expand-lg bg-light navbar-light py-3 py-lg-0 px-0">
                <a href="{{ route('home') }}" class="text-decoration-none d-block d-lg-none">
                    <h1 class="m-0 display-5 font-weight-semi-bold">
                        <span class="text-primary font-weight-bold border px-3 mr-1">E</span>Shopper
                    </h1>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                    <div class="navbar-nav mr-auto py-0">
                        <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                            Home
                        </a>
                        <a href="{{ route('shop.index') }}" class="nav-item nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}">
                            Shop
                        </a>
                        <a href="{{ route('products.show', 1) }}" class="nav-item nav-link {{ request()->routeIs('products.show') ? 'active' : '' }}">
                            Shop Detail
                        </a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs('cart.*') || request()->routeIs('checkout') ? 'active' : '' }}" data-toggle="dropdown">
                                Pages
                            </a>
                            <div class="dropdown-menu rounded-0 m-0">
                                <a href="{{ route('cart.index') }}" class="dropdown-item">Shopping Cart</a>
                                <a href="{{ route('checkout.index') }}" class="dropdown-item">Checkout</a>
                            </div>
                        </div>
                        <a href="{{ route('contact') }}" class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                            Contact
                        </a>
                    </div>
                    <div class="navbar-nav ml-auto py-0">
                        @guest
                            <a href="{{ route('login') }}" class="nav-item nav-link {{ request()->routeIs('login') ? 'active' : '' }}">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="nav-item nav-link {{ request()->routeIs('register') ? 'active' : '' }}">
                                Register
                            </a>
                        @else
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu rounded-0 m-0">
                                    <a href="{{ route('profile.index') }}" class="dropdown-item">My Profile</a>
                                    <a href="{{ route('orders.index') }}" class="dropdown-item">My Orders</a>
                                    <a href="{{ route('wishlist.index') }}" class="dropdown-item">My Wishlist</a>
                                    <a href="{{ route('logout') }}" class="dropdown-item"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                </div>
                            </div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @endguest
                    </div>
                </div>
            </nav>

            @if(request()->routeIs('home'))
                <!-- Carousel for Homepage -->
                <div id="header-carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active" style="height: 410px;">
                            <img class="img-fluid" src="{{ asset('img/carousel-1.jpg') }}" alt="Image">
                            <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                                <div class="p-3" style="max-width: 700px;">
                                    <h4 class="text-light text-uppercase font-weight-medium mb-3">10% Off Your First Order</h4>
                                    <h3 class="display-4 text-white font-weight-semi-bold mb-4">Fashionable Dress</h3>
                                    <a href="{{ route('shop.index') }}" class="btn btn-light py-2 px-3">Shop Now</a>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item" style="height: 410px;">
                            <img class="img-fluid" src="{{ asset('img/carousel-2.jpg') }}" alt="Image">
                            <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                                <div class="p-3" style="max-width: 700px;">
                                    <h4 class="text-light text-uppercase font-weight-medium mb-3">10% Off Your First Order</h4>
                                    <h3 class="display-4 text-white font-weight-semi-bold mb-4">Reasonable Price</h3>
                                    <a href="{{ route('shop.index') }}" class="btn btn-light py-2 px-3">Shop Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#header-carousel" data-slide="prev">
                        <div class="btn btn-dark" style="width: 45px; height: 45px;">
                            <span class="carousel-control-prev-icon mb-n2"></span>
                        </div>
                    </a>
                    <a class="carousel-control-next" href="#header-carousel" data-slide="next">
                        <div class="btn btn-dark" style="width: 45px; height: 45px;">
                            <span class="carousel-control-next-icon mb-n2"></span>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
<!-- Navbar End -->