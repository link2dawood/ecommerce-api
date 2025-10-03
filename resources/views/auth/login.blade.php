@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>

    <!-- CSS files -->
    <link href="{{ asset('dist/css/tabler.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/css/tabler-flags.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/css/tabler-payments.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/css/tabler-vendors.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/css/demo.min.css') }}" rel="stylesheet"/>
  </head>
  <body class="border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
      <div class="container-tight py-4">
        <div class="text-center mb-4">
          <a href="/" class="navbar-brand navbar-brand-autodark">
            <img src="{{ asset('static/logo.svg') }}" height="36" alt="Logo">
          </a>
        </div>

        <!-- Login Card -->
        <form id="loginForm" class="card card-md" autocomplete="off">
          <div class="card-body">
            <h2 class="card-title text-center mb-4">Login to your account</h2>

            <!-- Email -->
            <div class="mb-3">
              <label class="form-label">Email address</label>
              <input type="email" name="email" class="form-control" placeholder="Enter email" required>
            </div>

            <!-- Password -->
            <div class="mb-2">
              <label class="form-label">
                Password
                <span class="form-label-description">
                  <a href="#">I forgot password</a>
                </span>
              </label>
              <div class="input-group input-group-flat">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <span class="input-group-text">
                  <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" 
                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                         fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                      <circle cx="12" cy="12" r="2" />
                      <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7
                               c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                    </svg>
                  </a>
                </span>
              </div>
            </div>

            <!-- Remember -->
            <div class="mb-2">
              <label class="form-check">
                <input type="checkbox" class="form-check-input"/>
                <span class="form-check-label">Remember me</span>
              </label>
            </div>

            <!-- Submit -->
            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Sign in</button>
            </div>
          </div>
        </form>

        <!-- Register link -->
        <div class="text-center text-muted mt-3">
          Don't have an account yet? 
          <a href="{{ route('register') }}" tabindex="-1">Sign up</a>
        </div>
      </div>
    </div>

    <!-- Tabler JS -->
    <script src="{{ asset('dist/js/tabler.min.js') }}"></script>
    <script src="{{ asset('dist/js/demo.min.js') }}"></script>

    <!-- Login Script -->
    <script>
      document.getElementById("loginForm").addEventListener("submit", async function(e){
        e.preventDefault();

        let formData = new FormData(this);

        let response = await fetch("{{ url('/api/login') }}", {
            method: "POST",
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });

        let data = await response.json();

        if(data.token){
            localStorage.setItem("auth_token", data.token);
            alert("Login Successful!");
            window.location.href = "/home"; // redirect after login
        } else {
            alert(JSON.stringify(data));
        }
      });
    </script>
  </body>
</html>
