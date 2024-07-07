<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Edit User</h2>
        <form id="editUserForm">
            <input type="hidden" name="id" id="userId">
            <input type="text" name="username" id="username" required>
            <select name="role" id="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <input type="submit" value="Save Changes">
        </form>
        <a href="manage_users.php">Back</a>
    </div>
    <script>
        const params = new URLSearchParams(window.location.search);
        const userId = params.get('id');
        document.getElementById('userId').value = userId;

        fetch(`/api.php/users/${userId}`)
            .then(response => response.json())
            .then(user => {
                document.getElementById('username').value = user.username;
                document.getElementById('role').value = user.role;
            });

        document.getElementById('editUserForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            fetch(`/api.php/users/${userId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.message === "User updated") {
                    window.location.href = 'manage_users.php';
                }
            });
        });
    </script>
</body>
</html>
