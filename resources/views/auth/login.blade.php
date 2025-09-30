<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2>Login</h2>
    <form id="loginForm">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
 <!-- 🔹 Register button -->
    <p>Don’t have an account?</p>
    <a href="{{ route('register') }}">
        <button type="button">Register</button>
    </a>
    <script>
        document.getElementById("loginForm").addEventListener("submit", async function(e){
            e.preventDefault();
            let formData = new FormData(this);

            let response = await fetch("/api/login", {
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
                window.location.href = "/logout"; // redirect
            } else {
                alert(JSON.stringify(data));
            }
        });
    </script>
</body>
</html>
