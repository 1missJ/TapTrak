<?php
session_start();
include 'db_connection.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']); // LRN or system username
    $password = trim($_POST['password']); // MMDDYYYY or system password

    if (preg_match('/^\d{12}$/', $username)) {
        // 🌟 Student login
        $stmt = $conn->prepare("SELECT id, lrn, date_of_birth, first_name FROM students WHERE lrn = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $student = $result->fetch_assoc();
            $dob_db = $student['date_of_birth'];

            // Convert YYYY-MM-DD to MMDDYYYY
            $dob_formatted = date("mdY", strtotime($dob_db));

            if ($password === $dob_formatted) {
                $_SESSION['student_id'] = $student['id'];
                $_SESSION['lrn'] = $student['lrn'];
                $_SESSION['student_name'] = $student['first_name'];

                header("Location: student_portal.php");
                exit();
            } else {
                $error = "Invalid LRN or date of birth!";
            }
        } else {
            $error = "Invalid LRN or date of birth!";
        }

        $stmt->close();
    } else {
        // 🌟 System user login (admin, counselor, etc.)
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];

                if ($user['role'] === 'counselor') {
                    header("Location: counselor_dashboard.php");
                } else {
                    header("Location: dashboard.php"); // admin, superadmin, etc.
                }
                exit();
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            $error = "Invalid username or password!";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
      body {
    background: url('assets/imgs/dahs2.jpg') no-repeat center center fixed;
    background-size: cover;
}

body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: inherit;
    filter: blur(2px); /* Adjust the blur intensity */
    z-index: -1; /* Send it behind everything */
}

.login-container {
    width: 350px;
    margin: 100px auto;
    padding: 20px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 10px;
}

    </style>
</head>
<body>
    <div class="container">
        <div class="login-container text-center">
            <h3>Login</h3>
            <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <hr>
            <p>Application for New Student <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
