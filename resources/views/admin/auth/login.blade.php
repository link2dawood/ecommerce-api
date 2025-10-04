<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login</title>
    <link href="{{ asset('dist/css/tabler.min.css') }}" rel="stylesheet"/>
</head>
<body class="border-top-wide border-primary d-flex flex-column bg-dark">
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="text-center mb-4">
                <h2 class="text-white">Admin Panel</h2>
            </div>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" class="card card-md">
                @csrf
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Admin Login</h2>

                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control" placeholder="admin@example.com" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">Sign in to Admin Panel</button>
                    </div>
                </div>
            </form>

            <div class="text-center text-white mt-3">
                <a href="{{ url('/') }}" class="text-white">Back to Website</a>
            </div>
        </div>
    </div>

    <script src="{{ asset('dist/js/tabler.min.js') }}"></script>
</body>
</html>