<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2>Logout</h2>
    <button id="logoutBtn">Logout</button>

    <script>
        document.getElementById("logoutBtn").addEventListener("click", async function(){
            let token = localStorage.getItem("auth_token");
            let response = await fetch("/api/logout", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + token,
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                }
            });

            let data = await response.json();
            localStorage.removeItem("auth_token");
            alert("Logged out successfully!");
            window.location.href = "/login";
        });
    </script>
</body>
</html>
