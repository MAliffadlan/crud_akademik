<?php
require_once 'config/database.php';
$username = 'akademik';
$password = '123';
$role = 'academic';
$full_name = 'Staf Akademik';

// Check if user exists
$check = $conn->query("SELECT id FROM users WHERE username = '$username'");
if ($check->num_rows == 0) {
    // Insert user
    $conn->query("INSERT INTO users (username, password, role, full_name) VALUES ('$username', '$password', '$role', '$full_name')");
    echo "Academic user created: $username / $password";
} else {
    echo "Academic user already exists";
}
?>
