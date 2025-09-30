<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2>Register</h2>
    <form id="registerForm">
        <input type="text" name="name" placeholder="Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required><br>
        <button type="submit">Register</button>
    </form>
     <br>
    <!-- 🔹 Login button -->
    <p>Already have an account?</p>
    <a href="{{ route('login') }}">
        <button type="button">Login</button>
    </a>

    <script>
        document.getElementById("registerForm").addEventListener("submit", async function(e){
            e.preventDefault();
            let formData = new FormData(this);

            let response = await fetch("/api/register", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            let data = await response.json();
            alert(JSON.stringify(data));
        });
    </script>
</body>
</html>
