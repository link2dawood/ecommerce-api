<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>Register - {{ config('app.name') }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <!-- Tabler CSS (via Vite or asset) -->
  <link href="{{ asset('dist/css/tabler.min.css') }}" rel="stylesheet"/>
  <link href="{{ asset('dist/css/tabler-flags.min.css') }}" rel="stylesheet"/>
  <link href="{{ asset('dist/css/tabler-payments.min.css') }}" rel="stylesheet"/>
  <link href="{{ asset('dist/css/tabler-vendors.min.css') }}" rel="stylesheet"/>
  <link href="{{ asset('dist/css/demo.min.css') }}" rel="stylesheet"/>
</head>
<body class=" border-top-wide border-primary d-flex flex-column">
  <div class="page page-center">
    <div class="container-tight py-4">
      <div class="text-center mb-4">
        <a href="{{ url('/') }}" class="navbar-brand navbar-brand-autodark">
          <img src="{{ asset('static/logo.svg') }}" height="36" alt="Logo">
        </a>
      </div>

      <!-- Register Form -->
      <form id="registerForm" class="card card-md">
        <div class="card-body">
          <h2 class="card-title text-center mb-4">Create new account</h2>

          <!-- Name -->
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter name" required>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter email" required>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group input-group-flat">
              <input type="password" name="password" class="form-control" placeholder="Password" required>
              <span class="input-group-text">
                <a href="#" class="link-secondary" title="Show password" onclick="togglePassword(event, 'password')">👁</a>
              </span>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
          </div>

          <!-- Terms -->
          <div class="mb-3">
            <label class="form-check">
              <input type="checkbox" class="form-check-input" required>
              <span class="form-check-label">
                Agree to the <a href="{{ route('terms') }}" tabindex="-1">terms and policy</a>.
              </span>
            </label>
          </div>

          <!-- Submit -->
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Create new account</button>
          </div>
        </div>
      </form>

      <!-- Login link -->
      <div class="text-center text-muted mt-3">
        Already have an account? <a href="{{ route('login') }}" tabindex="-1">Sign in</a>
      </div>
    </div>
  </div>

  <!-- Tabler JS -->
  <script src="{{ asset('dist/js/tabler.min.js') }}"></script>
  <script src="{{ asset('dist/js/demo.min.js') }}"></script>

  <!-- Custom JS -->
  <script>
    // 🔹 Toggle password visibility
    function togglePassword(e, field) {
      e.preventDefault();
      const input = document.querySelector(`input[name="${field}"]`);
      input.type = input.type === "password" ? "text" : "password";
    }

    // 🔹 Handle registration form
    document.getElementById("registerForm").addEventListener("submit", async function(e) {
      e.preventDefault();

      let formData = new FormData(this);

      let response = await fetch("{{ route('api.register') }}", {
        method: "POST",
        headers: {
          "Accept": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
      });

      let data = await response.json();

      if(response.ok){
        alert("✅ Registration successful!");
        window.location.href = "{{ route('login') }}"; // redirect to login
      } else {
        alert("❌ " + JSON.stringify(data.errors || data.message));
      }
    });
  </script>
</body>
</html>
