<?php
require_once 'config/database.php';

$sql = "CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    semester INT DEFAULT 1,
    enrolled_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
)";

if ($conn->query($sql)) {
    echo "Table 'enrollments' created successfully!<br>";
    
    $student_result = $conn->query("SELECT id FROM students WHERE status = 'active' LIMIT 1");
    $student = $student_result->fetch_assoc();
    
    if ($student) {
        $student_id = $student['id'];
        
        $courses = $conn->query("SELECT id FROM courses LIMIT 5");
        $enrolled = 0;
        
        while ($course = $courses->fetch_assoc()) {
            $stmt = $conn->prepare("INSERT IGNORE INTO enrollments (student_id, course_id, semester) VALUES (?, ?, 1)");
            $stmt->bind_param("ii", $student_id, $course['id']);
            if ($stmt->execute()) {
                $enrolled++;
            }
        }
        
        echo "Enrolled student ID $student_id in $enrolled courses for semester 1!";
    } else {
        echo "No active student found to enroll.";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
