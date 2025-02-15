<?php
include 'db_connection.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    // Check if the email exists
    $query = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email exists, update the password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT); // Hash the new password
        $update_query = "UPDATE admin SET password = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ss", $hashed_password, $email);
        if ($update_stmt->execute()) {
            // Redirect to login page after success
            header('Location: index.php'); 
            exit(); // Ensure no further code is executed
        } else {
            $error = "Failed to update password. Please try again.";
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
    <title>Forgot Password</title>
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #1c1c1c;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.password-container {
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

.password-container img {
    width: 150px;
    margin-bottom: 20px;
}

h1 {
    color: #333;
    margin-bottom: 20px;
}

.password-container input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 16px;
}

.password-container button {
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

.password-container button:hover {
    background: linear-gradient(135deg, #FFC107, #FF9800);
}

p {
    margin-top: 10px;
    font-size: 14px;
}

.success {
    color: green;
}

.error {
    color: red;
}

/* Mobile View */
@media (max-width: 768px) {

    body {
        padding: 10px;
    }

    .password-container {
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .password-container img {
        width: 120px; /* Slightly smaller logo */
    }

    .password-container input {
        font-size: 14px;
        padding: 10px;
    }

    .password-container button {
        font-size: 14px;
        padding: 10px;
    }
}



/* Very Small Mobile View */
@media (max-width: 480px) {
    .password-container {
        max-width: 280px;
        padding: 15px;
    }

    .password-container img {
        width: 100px;
    }

    h1 {
        font-size: 18px;
    }

    .password-container input,
    .password-container button {
        font-size: 14px;
    }
}

    </style>
</head>
<body>
    <div class="password-container">
        <img src="logo.jpg" alt="Quick Cabs Service Logo">
        <h1>Forgot Password</h1>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="password" name="new_password" placeholder="Enter new password" required>
            <button type="submit" name="update_password">Update Password</button>
        </form>
        <?php 
        if (isset($error)) echo "<p class='error'>$error</p>";
        ?>
    </div>
</body>
</html>
