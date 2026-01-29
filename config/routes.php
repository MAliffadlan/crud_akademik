<?php
// config/routes.php - Centralized Route Configuration

// Define all routes with their required roles
$routes = [
    // Public routes (no auth required)
    '/' => ['file' => 'index.php', 'roles' => []],
    '/index.php' => ['file' => 'index.php', 'roles' => []],
    
    // Auth routes
    '/src/auth/login_process.php' => ['file' => 'src/auth/login_process.php', 'roles' => []],
    '/src/auth/logout.php' => ['file' => 'src/auth/logout.php', 'roles' => []],
    
    // Admin routes
    '/src/admin/dashboard.php' => ['file' => 'src/admin/dashboard.php', 'roles' => ['admin']],
    '/src/admin/users.php' => ['file' => 'src/admin/users.php', 'roles' => ['admin']],
    '/src/admin/user_form.php' => ['file' => 'src/admin/user_form.php', 'roles' => ['admin']],
    
    // Marketing routes
    '/src/marketing/dashboard_staff.php' => ['file' => 'src/marketing/dashboard_staff.php', 'roles' => ['marketing']],
    '/src/marketing/dashboard_manager.php' => ['file' => 'src/marketing/dashboard_manager.php', 'roles' => ['manager']],
    '/src/marketing/input_leads.php' => ['file' => 'src/marketing/input_leads.php', 'roles' => ['marketing']],
    '/src/marketing/reports.php' => ['file' => 'src/marketing/reports.php', 'roles' => ['marketing']],
    '/src/marketing/team_monitoring.php' => ['file' => 'src/marketing/team_monitoring.php', 'roles' => ['manager']],
    
    // Academic routes
    '/src/academic/dashboard.php' => ['file' => 'src/academic/dashboard.php', 'roles' => ['academic']],
    '/src/academic/students.php' => ['file' => 'src/academic/students.php', 'roles' => ['academic']],
    '/src/academic/student_detail.php' => ['file' => 'src/academic/student_detail.php', 'roles' => ['academic']],
    '/src/academic/validation.php' => ['file' => 'src/academic/validation.php', 'roles' => ['academic']],
    '/src/academic/print_letter.php' => ['file' => 'src/academic/print_letter.php', 'roles' => ['academic']],
    
    // Student routes
    '/src/student/dashboard.php' => ['file' => 'src/student/dashboard.php', 'roles' => ['student']],
    '/src/student/krs.php' => ['file' => 'src/student/krs.php', 'roles' => ['student']],
    '/src/student/grades.php' => ['file' => 'src/student/grades.php', 'roles' => ['student']],
];

$navigation = [
    'admin' => [
        ['label' => 'Dashboard', 'url' => '/crud_akademik/src/admin/dashboard.php', 'icon' => 'speedometer-outline', 'key' => 'dashboard'],
        ['label' => 'Manajemen User', 'url' => '/crud_akademik/src/admin/users.php', 'icon' => 'people-outline', 'key' => 'users'],
        ['label' => 'Activity Log', 'url' => '/crud_akademik/src/admin/activity_log.php', 'icon' => 'time-outline', 'key' => 'activity_log'],
        ['label' => 'Konfigurasi', 'url' => '/crud_akademik/src/admin/config.php', 'icon' => 'settings-outline', 'key' => 'config'],
    ],
    'marketing' => [
        ['label' => 'Dashboard', 'url' => '/crud_akademik/src/marketing/dashboard_staff.php', 'icon' => 'speedometer-outline', 'key' => 'dashboard'],
        ['label' => 'Input Prospek', 'url' => '/crud_akademik/src/marketing/input_leads.php', 'icon' => 'person-add-outline', 'key' => 'input'],
        ['label' => 'Laporan Harian', 'url' => '/crud_akademik/src/marketing/reports.php', 'icon' => 'document-text-outline', 'key' => 'reports'],
    ],
    'manager' => [
        ['label' => 'Analytics', 'url' => '/crud_akademik/src/marketing/dashboard_manager.php', 'icon' => 'bar-chart-outline', 'key' => 'dashboard'],
        ['label' => 'ROI Analysis', 'url' => '/crud_akademik/src/marketing/roi_analysis.php', 'icon' => 'trending-up-outline', 'key' => 'roi'],
        ['label' => 'Monitoring Tim', 'url' => '/crud_akademik/src/marketing/team_monitoring.php', 'icon' => 'eye-outline', 'key' => 'monitoring'],
    ],
    'academic' => [
        ['label' => 'Dashboard', 'url' => '/crud_akademik/src/academic/dashboard.php', 'icon' => 'grid-outline', 'key' => 'dashboard'],
        ['label' => 'Validasi Pembayaran', 'url' => '/crud_akademik/src/academic/validation.php', 'icon' => 'checkmark-circle-outline', 'key' => 'validation'],
        ['label' => 'Approval UKT', 'url' => '/crud_akademik/src/academic/ukt_approval.php', 'icon' => 'wallet-outline', 'key' => 'ukt'],
        ['label' => 'Data Mahasiswa', 'url' => '/crud_akademik/src/academic/students.php', 'icon' => 'school-outline', 'key' => 'students'],
    ],
    'student' => [
        ['label' => 'Dashboard', 'url' => '/crud_akademik/src/student/dashboard.php', 'icon' => 'home-outline', 'key' => 'dashboard'],
        ['label' => 'KRS Online', 'url' => '/crud_akademik/src/student/krs.php', 'icon' => 'book-outline', 'key' => 'krs'],
        ['label' => 'Jadwal Kuliah', 'url' => '/crud_akademik/src/student/schedule.php', 'icon' => 'calendar-outline', 'key' => 'schedule'],
        ['label' => 'Lihat Nilai', 'url' => '/crud_akademik/src/student/grades.php', 'icon' => 'ribbon-outline', 'key' => 'grades'],
        ['label' => 'Status Pembayaran', 'url' => '/crud_akademik/src/student/payment.php', 'icon' => 'wallet-outline', 'key' => 'payment'],
    ],
];

$breadcrumbs = [
    'dashboard' => 'Dashboard',
    'users' => 'Manajemen User',
    'input' => 'Input Prospek',
    'reports' => 'Laporan Harian',
    'validation' => 'Validasi Pembayaran',
    'students' => 'Data Mahasiswa',
    'krs' => 'KRS Online',
    'grades' => 'Lihat Nilai',
];

/**
 * Get navigation menu for current user role
 */
function getNavigation($role) {
    global $navigation;
    return $navigation[$role] ?? [];
}

/**
 * Check if current page matches key for active state
 */
function isActivePage($key, $active) {
    return $key === $active;
}

/**
 * Get dashboard URL for role
 */
function getDashboardUrl($role) {
    $urls = [
        'admin' => '/crud_akademik/src/admin/dashboard.php',
        'marketing' => '/crud_akademik/src/marketing/dashboard_staff.php',
        'manager' => '/crud_akademik/src/marketing/dashboard_manager.php',
        'academic' => '/crud_akademik/src/academic/dashboard.php',
        'student' => '/crud_akademik/src/student/dashboard.php',
    ];
    return $urls[$role] ?? '/crud_akademik/';
}
?>
