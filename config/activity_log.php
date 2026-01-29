<?php
// config/activity_log.php - Activity Logging Helper Functions

require_once __DIR__ . '/database.php';

/**
 * Log user activity to database
 * 
 * @param string $action - Action type (login, logout, create_user, etc.)
 * @param string $description - Human readable description
 * @param string|null $target_type - Target entity type (user, course, etc.)
 * @param int|null $target_id - Target entity ID
 * @return bool - Success status
 */
function logActivity($action, $description = '', $target_type = null, $target_id = null) {
    global $conn;
    
    // Get current user info
    $user_id = $_SESSION['user_id'] ?? 0;
    
    // Get IP address
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Get user agent
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    // Prepare statement
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action, description, target_type, target_id, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("isssiis", $user_id, $action, $description, $target_type, $target_id, $ip_address, $user_agent);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * Get activity logs with filters
 * 
 * @param array $filters - Optional filters (user_id, action, date_from, date_to)
 * @param int $limit - Number of records per page
 * @param int $offset - Offset for pagination
 * @return array - Array of log entries
 */
function getActivityLogs($filters = [], $limit = 20, $offset = 0) {
    global $conn;
    
    $where = [];
    $params = [];
    $types = '';
    
    // Build WHERE clause based on filters
    if (!empty($filters['user_id'])) {
        $where[] = "al.user_id = ?";
        $params[] = $filters['user_id'];
        $types .= 'i';
    }
    
    if (!empty($filters['action'])) {
        $where[] = "al.action = ?";
        $params[] = $filters['action'];
        $types .= 's';
    }
    
    if (!empty($filters['date_from'])) {
        $where[] = "DATE(al.created_at) >= ?";
        $params[] = $filters['date_from'];
        $types .= 's';
    }
    
    if (!empty($filters['date_to'])) {
        $where[] = "DATE(al.created_at) <= ?";
        $params[] = $filters['date_to'];
        $types .= 's';
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $sql = "SELECT al.*, u.full_name as user_name, u.username 
            FROM activity_log al 
            LEFT JOIN users u ON al.user_id = u.id 
            $whereClause
            ORDER BY al.created_at DESC 
            LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return [];
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $logs = [];
    
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
    
    $stmt->close();
    return $logs;
}

/**
 * Count total activity logs (for pagination)
 */
function countActivityLogs($filters = []) {
    global $conn;
    
    $where = [];
    $params = [];
    $types = '';
    
    if (!empty($filters['user_id'])) {
        $where[] = "user_id = ?";
        $params[] = $filters['user_id'];
        $types .= 'i';
    }
    
    if (!empty($filters['action'])) {
        $where[] = "action = ?";
        $params[] = $filters['action'];
        $types .= 's';
    }
    
    if (!empty($filters['date_from'])) {
        $where[] = "DATE(created_at) >= ?";
        $params[] = $filters['date_from'];
        $types .= 's';
    }
    
    if (!empty($filters['date_to'])) {
        $where[] = "DATE(created_at) <= ?";
        $params[] = $filters['date_to'];
        $types .= 's';
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $sql = "SELECT COUNT(*) as total FROM activity_log $whereClause";
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row['total'] ?? 0;
}

/**
 * Get unique action types for filter dropdown
 */
function getUniqueActions() {
    global $conn;
    
    $sql = "SELECT DISTINCT action FROM activity_log ORDER BY action";
    $result = $conn->query($sql);
    $actions = [];
    
    while ($row = $result->fetch_assoc()) {
        $actions[] = $row['action'];
    }
    
    return $actions;
}

/**
 * Get action badge color based on action type
 */
function getActionBadgeClass($action) {
    $classes = [
        'login' => 'bg-green-100 text-green-800',
        'logout' => 'bg-gray-100 text-gray-800',
        'create_user' => 'bg-blue-100 text-blue-800',
        'update_user' => 'bg-yellow-100 text-yellow-800',
        'delete_user' => 'bg-red-100 text-red-800',
        'view_page' => 'bg-purple-100 text-purple-800',
    ];
    
    return $classes[$action] ?? 'bg-slate-100 text-slate-800';
}

/**
 * Get action icon based on action type
 */
function getActionIcon($action) {
    $icons = [
        'login' => 'log-in-outline',
        'logout' => 'log-out-outline',
        'create_user' => 'person-add-outline',
        'update_user' => 'create-outline',
        'delete_user' => 'trash-outline',
        'view_page' => 'eye-outline',
    ];
    
    return $icons[$action] ?? 'ellipse-outline';
}
?>
