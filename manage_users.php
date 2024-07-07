<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Manage Users</h2>
        <table id="usersTable">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </table>
        <a href="welcome.php">Back</a>
    </div>
    <script>
        fetch('/api.php/users')
            .then(response => response.json())
            .then(users => {
                const usersTable = document.getElementById('usersTable');
                users.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.role}</td>
                        <td>
                            <a href="edit_user.html?id=${user.id}">Edit</a>
                            <a href="#" onclick="deleteUser(${user.id})">Delete</a>
                        </td>
                    `;
                    usersTable.appendChild(row);
                });
            });

        function deleteUser(id) {
            if (confirm('Are you sure?')) {
                fetch(`/api.php/users/${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.message === "User deleted") {
                        location.reload();
                    }
                });
            }
        }
    </script>
</body>
</html>
