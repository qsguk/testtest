<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2 id="welcomeMessage"></h2>
        <div id="adminActions" style="display: none;">
            <a href="manage_users.php">Manage Users</a>
        </div>
        <a href="login.php" onclick="logout()">Logout</a>
    </div>
    <script>
        const username = sessionStorage.getItem('username');
        const role = sessionStorage.getItem('role');
        const welcomeMessage = document.getElementById('welcomeMessage');
        const adminActions = document.getElementById('adminActions');

        if (!username) {
            window.location.href = 'login.php';
        }

        welcomeMessage.textContent = `Welcome, ${username}`;
        if (role === 'admin') {
            adminActions.style.display = 'block';
        }

        function logout() {
            sessionStorage.clear();
        }
    </script>
</body>
</html>
