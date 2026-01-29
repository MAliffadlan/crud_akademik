<?php
require_once 'config/database.php';
$username = 'test';
$new_role = 'manager';

$stmt = $conn->prepare("UPDATE users SET role = ? WHERE username = ?");
$stmt->bind_param("ss", $new_role, $username);

if ($stmt->execute()) {
    echo "User $username promoted to $new_role";
} else {
    echo "Error: " . $conn->error;
}
?>
