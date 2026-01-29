<?php
// src/auth/login_process.php
require_once '../../config/database.php';
require_once '../../config/functions.php'; // session_start is here
require_once '../../config/activity_log.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username']);
    $password = $_POST['password'];

    // Query users table
    $stmt = $conn->prepare("SELECT id, password, role, full_name FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        // Note: In setup.sql we used direct hashes.
        // If password_verify fails, it might be because I manually inserted hashes that are not valid for the current system algorithm or cost?
        // Actually, $2y$ is bcrypt, which password_verify supports.
        // BUT, if I put 'admin123' as the password in the prompt, I should check if the hash matches.
        
        if (password_verify($password, $user['password'])) {
            // Login Success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Log the login activity
            logActivity('login', 'User logged in successfully', 'user', $user['id']);
            
            // If student, get student_id
            if ($user['role'] == 'student') {
                $stmt_student = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
                $stmt_student->bind_param("i", $user['id']);
                $stmt_student->execute();
                $res_student = $stmt_student->get_result();
                if ($row_s = $res_student->fetch_assoc()) {
                    $_SESSION['student_id'] = $row_s['id'];
                }
            }
            
            // Redirect based on role using helper function
            redirectToDashboard();
        } else {
            redirect('login.php?error=Password salah!');
        }
    } else {
        redirect('login.php?error=Username tidak ditemukan!');
    }
} else {
    redirect('login.php');
}
?>
