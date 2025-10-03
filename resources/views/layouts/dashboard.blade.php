<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce Dashboard</title>

    <!-- Bootstrap + Tabler -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="page">
    <!-- Navbar -->
    <header class="navbar navbar-expand-md navbar-light d-print-none">
        <div class="container-xl">
            <!-- Logo -->
            <h1 class="navbar-brand pe-0 pe-md-3">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('static/logo.svg') }}" width="110" height="32" alt="Ecommerce API"
                         class="navbar-brand-image">
                </a>
            </h1>

            <!-- Navbar toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Right side (Cart, Wishlist, User) -->
            <div class="navbar-nav flex-row order-md-last">
                <!-- Wishlist -->
                <a href="{{ route('wishlist') }}" class="nav-link px-3 position-relative">
                    <i class="fas fa-heart"></i>
                    <span class="badge bg-red position-absolute top-0 start-100 translate-middle">
                        {{ session('wishlist_count', 0) }}
                    </span>
                </a>
                <!-- Cart -->
                <a href="{{ route('cart.index') }}" class="nav-link px-3 position-relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge bg-blue position-absolute top-0 start-100 translate-middle">
                        {{ session('cart_count', 0) }}
                    </span>
                </a>

                <!-- User Dropdown -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                        <span class="avatar avatar-sm"
                              style="background-image: url({{ asset('static/avatars/000m.jpg') }})"></span>
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ Auth::user()->name ?? 'Guest User' }}</div>
                            <div class="mt-1 small text-muted">Customer</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        @guest
                            <a href="{{ route('login') }}" class="dropdown-item">Login</a>
                            <a href="{{ route('register') }}" class="dropdown-item">Register</a>
                        @else
                            <a href="{{ route('profile') }}" class="dropdown-item">Profile</a>
                            <a href="{{ route('orders') }}" class="dropdown-item">My Orders</a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('logout') }}" class="dropdown-item"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @endguest
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="collapse navbar-collapse" id="navbar-menu">
                <form class="d-flex ms-md-4 w-50" action="{{ route('search') }}" method="GET">
                    <input type="text" name="q" class="form-control" placeholder="Search for products">
                    <button class="btn btn-primary ms-2" type="submit">Search</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Sidebar + Content -->
    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                                    <i class="fas fa-home me-2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('shop.index') }}">
                                    <i class="fas fa-store me-2"></i> Shop
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('wishlist') }}">
                                    <i class="fas fa-heart me-2"></i> Wishlist
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('orders') }}">
                                    <i class="fas fa-box me-2"></i> Orders
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('contact') }}">
                                    <i class="fas fa-envelope me-2"></i> Contact
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile') }}">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Main Content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
</body>
</html>
