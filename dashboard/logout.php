<?php
session_start();

// Destroy the session and redirect to the login page
session_destroy();
header('Location: index.php'); // Adjust 'login.php' to the correct login page URL if different
exit();
?>
