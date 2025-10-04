<header class="navbar navbar-expand-md navbar-dark bg-dark d-print-none">
    <div class="container-xl">
        <h1 class="navbar-brand pe-0 pe-md-3">
            <a href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('static/logo.svg') }}" width="110" height="32" alt="Admin Panel" class="navbar-brand-image">
            </a>
        </h1>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-nav flex-row order-md-last">
            <!-- View Website -->
            <a href="{{ url('/') }}" class="nav-link px-3" target="_blank" title="View Website">
                <i class="fas fa-external-link-alt"></i>
            </a>

            <!-- Admin User Dropdown -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                    <span class="avatar avatar-sm" style="background-image: url({{ asset('static/avatars/000m.jpg') }})"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div class="text-white">{{ Auth::user()->name }}</div>
                        <div class="mt-1 small text-muted">Administrator</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('admin.settings') }}" class="dropdown-item">Settings</a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>