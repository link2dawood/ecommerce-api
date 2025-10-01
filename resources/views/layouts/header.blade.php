<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce API</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

  <body>
    <div class="wrapper">
      <header class="navbar navbar-expand-md navbar-light d-print-none">
        <div class="container-xl">
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
          </button>
          
          <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{ url('/') }}">
              <img src="{{ asset('static/logo.svg') }}" width="110" height="32" alt="Ecommerce API" class="navbar-brand-image">
            </a>
          </h1>

          <div class="navbar-nav flex-row order-md-last">
            <div class="nav-item d-none d-md-flex me-3">
              <div class="btn-list">
                <a href="https://github.com/tabler/tabler" class="btn" target="_blank" rel="noreferrer">
                  <!-- Github -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon text-github" width="24" height="24"
                       viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                       stroke-linecap="round" stroke-linejoin="round">
                       <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                       <path d="M9 19c-4.3 1.4 -4.3 -2.5 -6 -3m12 5v-3.5c0 -1 .1 -1.4 -.5 -2
                                c2.8 -.3 5.5 -1.4 5.5 -6a4.6 4.6 0 0 0 -1.3 -3.2
                                a4.2 4.2 0 0 0 -.1 -3.2s-1.1 -.3 -3.5 1.3
                                a12.3 12.3 0 0 0 -6.2 0c-2.4 -1.6 -3.5 -1.3
                                -3.5 -1.3a4.2 4.2 0 0 0 -.1 3.2
                                a4.6 4.6 0 0 0 -1.3 3.2c0 4.6
                                2.7 5.7 5.5 6c-.6 .6 -.6 1.2 -.5 2v3.5"/>
                  </svg>
                  Source code
                </a>
                <a href="https://github.com/sponsors/codecalm" class="btn" target="_blank" rel="noreferrer">
                  <!-- Sponsor -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon text-pink" width="24" height="24"
                       viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                       stroke-linecap="round" stroke-linejoin="round">
                       <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                       <path d="M19.5 13.572l-7.5 7.428l-7.5 -7.428
                                m0 0a5 5 0 1 1 7.5 -6.566
                                a5 5 0 1 1 7.5 6.572"/>
                  </svg>
                  Sponsor
                </a>
              </div>
            </div>

            <!-- Dark/Light mode -->
            <a href="{{ url('?theme=dark') }}" class="nav-link px-0 hide-theme-dark" title="Enable dark mode"
               data-bs-toggle="tooltip" data-bs-placement="bottom">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                   viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                   stroke-linecap="round" stroke-linejoin="round">
                   <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                   <path d="M12 3c.132 0 .263 0 .393 0
                            a7.5 7.5 0 0 0 7.92 12.446
                            a9 9 0 1 1 -8.313 -12.454z"/>
              </svg>
            </a>
            <a href="{{ url('?theme=light') }}" class="nav-link px-0 hide-theme-light" title="Enable light mode"
               data-bs-toggle="tooltip" data-bs-placement="bottom">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                   viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                   stroke-linecap="round" stroke-linejoin="round">
                   <circle cx="12" cy="12" r="4"/>
                   <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1
                            m-6.4 -15.4l.7 .7
                            m12.1 -.7l-.7 .7
                            m0 11.4l.7 .7
                            m-12.1 -.7l-.7 .7"/>
              </svg>
            </a>

            <!-- Notifications -->
            <div class="nav-item dropdown d-none d-md-flex me-3">
              <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                     stroke-linecap="round" stroke-linejoin="round">
                     <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                     <path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3
                              a4 4 0 0 0 2 3h-16
                              a4 4 0 0 0 2 -3v-3
                              a7 7 0 0 1 4 -6"/>
                     <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
                </svg>
                <span class="badge bg-red"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-card">
                <div class="card">
                  <div class="card-body">
                    Notifications content here.
                  </div>
                </div>
              </div>
            </div>

            <!-- User menu -->
            <div class="nav-item dropdown">
              <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                 aria-label="Open user menu">
                <span class="avatar avatar-sm" style="background-image: url({{ asset('static/avatars/000m.jpg') }})"></span>
                <div class="d-none d-xl-block ps-2">
                  <div>{{ Auth::user()->name ?? 'Guest User' }}</div>
                  <div class="mt-1 small text-muted">UI Designer</div>
                </div>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <a href="{{ route('status.set') }}" class="dropdown-item">Set status</a>
                <a href="{{ route('profile') }}" class="dropdown-item">Profile & account</a>
                <a href="{{ route('feedback') }}" class="dropdown-item">Feedback</a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('settings') }}" class="dropdown-item">Settings</a>
                <a href="{{ route('logout') }}" class="dropdown-item"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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

      @yield('content')

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  </body>
</html>
