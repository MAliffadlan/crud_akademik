<?php
// config/settings.php - Settings Helper Functions

require_once __DIR__ . '/database.php';

/**
 * Get a setting value by key
 */
function getSetting($key, $default = null) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    
    return $default;
}

/**
 * Set/update a setting value
 */
function setSetting($key, $value) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                           ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = CURRENT_TIMESTAMP");
    $stmt->bind_param("sss", $key, $value, $value);
    return $stmt->execute();
}

/**
 * Get all settings as associative array
 */
function getAllSettings() {
    global $conn;
    
    $result = $conn->query("SELECT setting_key, setting_value FROM settings");
    $settings = [];
    
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
}

/**
 * Get all academic years
 */
function getAcademicYears() {
    global $conn;
    
    $result = $conn->query("SELECT * FROM academic_years ORDER BY year_name DESC");
    $years = [];
    
    while ($row = $result->fetch_assoc()) {
        $years[] = $row;
    }
    
    return $years;
}

/**
 * Get active academic year
 */
function getActiveAcademicYear() {
    global $conn;
    
    $result = $conn->query("SELECT * FROM academic_years WHERE is_active = 1 LIMIT 1");
    return $result->fetch_assoc();
}

/**
 * Add new academic year
 */
function addAcademicYear($yearName, $startDate = null, $endDate = null) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO academic_years (year_name, start_date, end_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $yearName, $startDate, $endDate);
    return $stmt->execute();
}

/**
 * Set active academic year (deactivate others)
 */
function setActiveAcademicYear($yearId) {
    global $conn;
    
    // Deactivate all
    $conn->query("UPDATE academic_years SET is_active = 0");
    
    // Activate selected
    $stmt = $conn->prepare("UPDATE academic_years SET is_active = 1 WHERE id = ?");
    $stmt->bind_param("i", $yearId);
    return $stmt->execute();
}

/**
 * Delete academic year
 */
function deleteAcademicYear($yearId) {
    global $conn;
    
    $stmt = $conn->prepare("DELETE FROM academic_years WHERE id = ?");
    $stmt->bind_param("i", $yearId);
    return $stmt->execute();
}
?>
