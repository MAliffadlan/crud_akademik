<?php
require_once 'config/database.php';

$result = $conn->query("SELECT id FROM students WHERE status = 'active' LIMIT 1");
$student = $result->fetch_assoc();

if ($student) {
    $student_id = $student['id'];
    
    $records = [
        [1, 3.45],  // Semester 1, GPA 3.45
        [2, 3.67],  // Semester 2, GPA 3.67
        [3, 3.52],  // Semester 3, GPA 3.52
    ];
    
    foreach ($records as $record) {
        $stmt = $conn->prepare("INSERT INTO academic_records (student_id, semester, gpa, status_active) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("iid", $student_id, $record[0], $record[1]);
        $stmt->execute();
    }
    
    echo "Sample academic records (nilai) added for student ID: $student_id!";
} else {
    echo "No active student found. Please activate a student first.";
}
?>
