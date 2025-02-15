<?php
session_start();
include 'db_connection.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            header('Location: dashboard.html'); // Redirect after successful login
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #1c1c1c;
}

.login-container {
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

.login-container img {
    width: 150px;
}

h1 {
    color: #333;
    margin-bottom: 20px;
}

.login-container input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 16px;
}

.login-container button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #FFC107, #FF9800);
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: background 0.3s ease;
}

.login-container button:hover {
    background: linear-gradient(135deg, #FFC107, #FF9800);
}

.login-container .forgot-password {
    display: block;
    margin-top: 15px;
    color: #007bff;
    text-decoration: none;
}

.login-container .forgot-password:hover {
    text-decoration: underline;
}

p {
    color: red;
}

/* Mobile View */
@media (max-width: 576px) {
    body {
        padding: 10px;
    }

    .login-container {
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .login-container img {
        width: 120px; /* Slightly smaller logo */
    }

    .login-container input {
        font-size: 14px;
        padding: 10px;
    }

    .login-container button {
        font-size: 14px;
        padding: 10px;
    }
}

    </style>
</head>
<body>
    <div class="login-container">
        <img src="logo.jpg" alt="Quick Cabs Service Logo">
        <h1>Admin Login</h1>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
    </div>
</body>
</html>
