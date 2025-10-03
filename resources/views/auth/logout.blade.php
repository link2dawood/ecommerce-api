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

            if (!token) {
                alert("No user is logged in.");
                window.location.href = "/login";
                return;
            }

            let response = await fetch("/api/logout", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + token
                }
            });

            if (response.ok) {
                localStorage.removeItem("auth_token");
                alert("Logged out successfully!");
                window.location.href = "/login";
            } else {
                alert("Logout failed. Please try again.");
            }
        });
    </script>
</body>
</html>
