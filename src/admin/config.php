<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/settings.php';
require_once '../../config/activity_log.php';
requireRole('admin');
$pageTitle = 'Konfigurasi Sistem';
$active = 'config';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $institutionName = clean($_POST['institution_name']);
    $activeSemester = clean($_POST['active_semester']);
    $activeYearId = (int)$_POST['active_year_id'];
    
    setSetting('institution_name', $institutionName);
    setSetting('active_semester', $activeSemester);
    
    if ($activeYearId > 0) {
        setActiveAcademicYear($activeYearId);
        $yearData = $conn->query("SELECT year_name FROM academic_years WHERE id = $activeYearId")->fetch_assoc();
        setSetting('active_academic_year', $yearData['year_name']);
    }
    
    logActivity('update_settings', 'Updated system configuration');
    $message = 'Pengaturan berhasil disimpan!';
    $messageType = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_year'])) {
    $newYear = clean($_POST['new_year']);
    if (!empty($newYear)) {
        addAcademicYear($newYear);
        logActivity('create_academic_year', "Added academic year: $newYear");
        $message = 'Tahun ajaran berhasil ditambahkan!';
        $messageType = 'success';
    }
}

if (isset($_GET['delete_year'])) {
    $yearId = (int)$_GET['delete_year'];
    deleteAcademicYear($yearId);
    logActivity('delete_academic_year', "Deleted academic year ID: $yearId");
    header('Location: config.php?msg=deleted');
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Tahun ajaran berhasil dihapus!';
    $messageType = 'success';
}

$settings = getAllSettings();
$academicYears = getAcademicYears();
$activeYear = getActiveAcademicYear();

$stats = [
    'Users' => $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'],
    'Students' => $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'],
    'Courses' => $conn->query("SELECT COUNT(*) as c FROM courses")->fetch_assoc()['c'],
    'Payments' => $conn->query("SELECT COUNT(*) as c FROM payments")->fetch_assoc()['c'],
    'Documents' => $conn->query("SELECT COUNT(*) as c FROM documents")->fetch_assoc()['c'],
];
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Konfigurasi</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        
        <div class="mb-8">
            <h1 class="text-3xl font-display font-bold text-slate-900 mb-2">Konfigurasi Sistem</h1>
            <p class="text-slate-500">Pusat pengaturan global aplikasi dan manajemen data referensi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 h-full">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                        <ion-icon name="library-outline"></ion-icon>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">Master Data</h3>
                        <p class="text-xs text-slate-500">Kelola data referensi utama</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <a href="courses.php" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-primary/30 hover:bg-primary/5 transition-all group">
                        <div class="flex items-center gap-3 text-slate-600 group-hover:text-primary">
                            <ion-icon name="book-outline" class="text-lg"></ion-icon>
                            <span class="font-medium">Mata Kuliah</span>
                        </div>
                        <span class="px-2 py-1 rounded-md bg-slate-100 text-xs font-bold text-slate-600 group-hover:bg-white group-hover:text-primary transition-colors"><?= $stats['Courses'] ?></span>
                    </a>
                    
                    <a href="programs.php" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-primary/30 hover:bg-primary/5 transition-all group">
                        <div class="flex items-center gap-3 text-slate-600 group-hover:text-primary">
                            <ion-icon name="school-outline" class="text-lg"></ion-icon>
                            <span class="font-medium">Program Studi</span>
                        </div>
                        <ion-icon name="chevron-forward-outline" class="text-slate-400 group-hover:text-primary text-sm"></ion-icon>
                    </a>
                    
                    <a href="users.php" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-primary/30 hover:bg-primary/5 transition-all group">
                        <div class="flex items-center gap-3 text-slate-600 group-hover:text-primary">
                            <ion-icon name="people-outline" class="text-lg"></ion-icon>
                            <span class="font-medium">Manajemen User</span>
                        </div>
                        <span class="px-2 py-1 rounded-md bg-slate-100 text-xs font-bold text-slate-600 group-hover:bg-white group-hover:text-primary transition-colors"><?= $stats['Users'] ?></span>
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 h-full">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl">
                        <ion-icon name="settings-outline"></ion-icon>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">Pengaturan Umum</h3>
                        <p class="text-xs text-slate-500">Konfigurasi dasar institusi</p>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="mb-4 p-3 rounded-lg <?= $messageType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
                        <div class="flex items-center gap-2">
                            <ion-icon name="<?= $messageType === 'success' ? 'checkmark-circle' : 'alert-circle' ?>"></ion-icon>
                            <?= $message ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Institusi</label>
                        <input type="text" name="institution_name" class="w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20 text-sm" value="<?= htmlspecialchars($settings['institution_name'] ?? 'Politeknik LP3I Jakarta') ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun Akademik Aktif</label>
                        <select name="active_year_id" class="w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20 text-sm">
                            <?php foreach ($academicYears as $year): ?>
                                <option value="<?= $year['id'] ?>" <?= $year['is_active'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($year['year_name']) ?> <?= $year['is_active'] ? '(Aktif)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Semester Aktif</label>
                        <select name="active_semester" class="w-full rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20 text-sm">
                            <option value="Ganjil" <?= ($settings['active_semester'] ?? '') === 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                            <option value="Genap" <?= ($settings['active_semester'] ?? '') === 'Genap' ? 'selected' : '' ?>>Genap</option>
                        </select>
                    </div>
                    <button type="submit" name="save_settings" class="w-full py-2.5 px-4 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition flex items-center justify-center gap-2">
                        <ion-icon name="save-outline"></ion-icon>
                        Simpan Pengaturan
                    </button>
                </form>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <h4 class="text-sm font-bold text-slate-700 mb-3">Tambah Tahun Ajaran</h4>
                    <form method="POST" action="config.php" class="flex gap-2">
                        <input type="hidden" name="add_year" value="1">
                        <input type="text" name="new_year" placeholder="contoh: 2025/2026" required class="flex-1 rounded-lg border-slate-200 focus:border-primary focus:ring-primary/20 text-sm">
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg font-medium hover:bg-green-600 transition">
                            <ion-icon name="add-outline" class="text-lg"></ion-icon>
                        </button>
                    </form>
                    
                    <div class="mt-4 space-y-2 max-h-32 overflow-y-auto">
                        <?php foreach ($academicYears as $year): ?>
                            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 text-sm">
                                <span class="<?= $year['is_active'] ? 'font-bold text-primary' : 'text-slate-600' ?>">
                                    <?= htmlspecialchars($year['year_name']) ?>
                                    <?php if ($year['is_active']): ?>
                                        <span class="ml-1 px-1.5 py-0.5 text-xs bg-primary/10 text-primary rounded">Aktif</span>
                                    <?php endif; ?>
                                </span>
                                <?php if (!$year['is_active']): ?>
                                    <a href="?delete_year=<?= $year['id'] ?>" onclick="return confirm('Hapus tahun ajaran ini?')" class="text-red-500 hover:text-red-700">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                            <ion-icon name="server-outline"></ion-icon>
                        </div>
                        <h3 class="font-bold text-slate-900">Database Stat</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <?php foreach ($stats as $label => $count): ?>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs text-slate-500 mb-1"><?= $label ?></p>
                            <p class="text-xl font-bold text-slate-800"><?= number_format($count) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <ion-icon name="construct-outline"></ion-icon> System Tools
                    </h3>
                    <div class="space-y-3">
                        <a href="/phpmyadmin" target="_blank" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition-colors">
                            <ion-icon name="logo-buffer" class="text-xl"></ion-icon>
                            <span class="text-sm font-medium">Buka phpMyAdmin</span>
                        </a>
                        <button onclick="alert('Backup berhasil! (simulasi)')" class="w-full flex items-center gap-3 p-3 rounded-xl bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition-colors">
                            <ion-icon name="cloud-download-outline" class="text-xl"></ion-icon>
                            <span class="text-sm font-medium">Backup Database</span>
                        </button>
                    </div>
                </div>

            </div>
            
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
