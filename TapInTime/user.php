<?php
session_start();
include 'db_connection.php';
$success_message = ""; // NEW: for prompt alerts

// Only allow superadmin
if ($_SESSION['user_role'] !== 'superadmin') {
    echo "Access Denied.";
    exit;
}

// Add new user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        echo "User added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $new_pass, $user_id);
    
    if ($stmt->execute()) {
        $success_message = "Password successfully updated!";
    }
    $stmt->close();
}



$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Sidebar width */
        .col-md-9 {
            margin-left: 450px; /* Adjust based on your sidebar size */
            padding-top: 40px;
           
        }

        /* Modern card layout for forms */
        .form-card {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        h2 {
            color: #333;
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
            margin-bottom: 15px;
            font-size: 1em;
        }

        button {
            padding: 12px 20px;
            font-size: 1.1em;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .btn-warning {
            background-color: #ffc107;
            color: white;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .container {
            max-width: 900px;
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>

    <!-- Right Content Section -->
    <div class="col-md-9">
        <div class="container mt-4">
            <div class="form-card">
                <h2>Add New User</h2>
                <!-- Form for adding new user -->
                <form method="post">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <select name="role" class="form-control">
                        <option value="admin">Admin</option>
                        <option value="counselor">Guidance Counselor</option>
                    </select>
                    <button type="submit" name="add_user">Add User</button>
                </form>
            </div>

            <div class="form-card">
                <h2>Change Your Password</h2>
                <!-- Form for changing password -->
                <form method="post">
                    <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
                    <button type="submit" name="change_password" class="btn-warning">Change Password</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>      
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    
    <?php if (!empty($success_message)) : ?>
<script>
    alert("<?= $success_message ?>");
</script>
<?php endif; ?>


</body>
</html>
