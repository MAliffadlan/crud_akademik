<?php
require_once 'config/database.php';

$courses = [
    ['MK001', 'Pemrograman Dasar', 3],
    ['MK002', 'Basis Data', 3],
    ['MK003', 'Jaringan Komputer', 3],
    ['MK004', 'Matematika Diskrit', 2],
    ['MK005', 'Bahasa Inggris', 2],
    ['MK006', 'Kewirausahaan', 2],
    ['MK007', 'Pemrograman Web', 3],
    ['MK008', 'Sistem Operasi', 3],
];

foreach ($courses as $course) {
    $stmt = $conn->prepare("INSERT IGNORE INTO courses (code, name, credits) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $course[0], $course[1], $course[2]);
    $stmt->execute();
}

echo "Sample courses added successfully!";
?>
