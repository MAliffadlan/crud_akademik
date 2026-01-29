<?php
require_once 'config/database.php';
$username = 'mahasiswa';
$password = '123';
$role = 'student';
$full_name = 'Mahasiswa Test';

$check = $conn->query("SELECT id FROM users WHERE username = '$username'");
if ($check->num_rows == 0) {
    $conn->query("INSERT INTO users (username, password, role, full_name) VALUES ('$username', '$password', '$role', '$full_name')");
    $user_id = $conn->insert_id;
    
    $conn->query("INSERT INTO students (user_id, full_name, status, registration_date) VALUES ($user_id, '$full_name', 'active', NOW())");
    echo "Student user created: $username / $password";
} else {
    echo "Student user already exists";
}
?>
