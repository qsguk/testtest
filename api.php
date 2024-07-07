<?php
header("Content-Type: application/json");
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$resource = array_shift($request);

switch ($resource) {
    case 'users':
        handleUsers($method, $request);
        break;
    case 'auth':
        handleAuth($method);
        break;
    default:
        http_response_code(404);
        echo json_encode(["message" => "Resource not found"]);
        break;
}

function handleUsers($method, $request) {
    global $conn;

    switch ($method) {
        case 'GET':
            if (isset($request[0])) {
                $id = $request[0];
                $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                echo json_encode($user);
                $stmt->close();
            } else {
                $result = $conn->query("SELECT id, username, role FROM users");
                $users = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode($users);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data['username'];
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $role = $data['role'] ?? 'user';

            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password, $role);

            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(["message" => "User created"]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "User creation failed"]);
            }

            $stmt->close();
            break;

        case 'PUT':
            if (!isset($request[0])) {
                http_response_code(400);
                echo json_encode(["message" => "User ID is required"]);
                return;
            }

            $id = $request[0];
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data['username'];
            $role = $data['role'];

            $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
            $stmt->bind_param("ssi", $username, $role, $id);

            if ($stmt->execute()) {
                echo json_encode(["message" => "User updated"]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "User update failed"]);
            }

            $stmt->close();
            break;

        case 'DELETE':
            if (!isset($request[0])) {
                http_response_code(400);
                echo json_encode(["message" => "User ID is required"]);
                return;
            }

            $id = $request[0];
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo json_encode(["message" => "User deleted"]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "User deletion failed"]);
            }

            $stmt->close();
            break;

        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
            break;
    }
}

function handleAuth($method) {
    global $conn;

    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data['username'];
            $password = $data['password'];

            $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($id, $username, $hashed_password, $role);

            if ($stmt->num_rows > 0) {
                $stmt->fetch();
                if (password_verify($password, $hashed_password)) {
                    $response = ["username" => $username, "role" => $role];
                    echo json_encode($response);
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Invalid login credentials"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Invalid login credentials"]);
            }

            $stmt->close();
            break;

        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
            break;
    }
}

$conn->close();
?>
