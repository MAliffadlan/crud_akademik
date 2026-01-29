<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('student');
$pageTitle = 'Student Dashboard';
$active = 'dashboard';

$user_id = $_SESSION['user_id'];
$student_result = $conn->query("SELECT id FROM students WHERE user_id = $user_id");
$student_row = $student_result->fetch_assoc();
$student_id = $student_row['id'] ?? 0;

$days_map = [
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa', 
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu',
    'Sunday' => 'Minggu'
];
$today_en = date('l');
$today = $days_map[$today_en] ?? $today_en;
$day_index = date('N') - 1; // 0 = Monday

$current_semester = 1;
$query = "SELECT c.code, c.name, c.credits 
    FROM krs k
    JOIN courses c ON k.course_id = c.id
    WHERE k.student_id = ? AND k.semester = ?
    ORDER BY c.code";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $student_id, $current_semester);
$stmt->execute();
$courses_result = $stmt->get_result();

$course_list = [];
while ($c = $courses_result->fetch_assoc()) {
    $course_list[] = $c;
}

$time_slots = [
    ['08:00', '09:40'],
    ['10:00', '11:40'],
    ['13:00', '14:40'],
    ['15:00', '16:40'],
];

$todays_schedule = [];
$idx = 0;
foreach ($course_list as $course) {
    $course_day_idx = $idx % 5;
    if ($course_day_idx == $day_index) {
        $time_idx = floor($idx / 5) % 4;
        $todays_schedule[] = [
            'time' => $time_slots[$time_idx][0] . ' - ' . $time_slots[$time_idx][1],
            'course' => $course['name'],
            'code' => $course['code'],
            'room' => 'R.' . (101 + ($idx * 7) % 50),
        ];
    }
    $idx++;
}

$total_courses = count($course_list);
$total_sks = array_sum(array_column($course_list, 'credits'));
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Student Portal</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'S', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        
        <div class="mb-8 border-b border-slate-200 pb-6">
            <h1 class="text-3xl font-display font-bold text-slate-900 mb-2">Halo, <?= $_SESSION['full_name'] ?> 👋</h1>
            <p class="text-slate-500">Selamat datang di Student Portal LP3I. Semangat belajar!</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:border-blue-300 transition-all">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-xl bg-blue-100/50 text-blue-600 flex items-center justify-center text-2xl mb-4">
                        <ion-icon name="school-outline"></ion-icon>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-1">Status Akademik</h3>
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Semester Saat Ini</span>
                            <span class="font-bold text-slate-900">Semester <?= $current_semester ?></span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Mata Kuliah Diambil</span>
                            <span class="font-bold text-slate-900"><?= $total_courses ?> MK (<?= $total_sks ?> SKS)</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Status Mahasiswa</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                Aktif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:border-purple-300 transition-all">
                <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-xl bg-purple-100/50 text-purple-600 flex items-center justify-center text-2xl mb-4">
                        <ion-icon name="calendar-outline"></ion-icon>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-1">Jadwal Hari Ini (<?= $today ?>)</h3>
                    
                    <?php if (empty($todays_schedule)): ?>
                        <div class="mt-4 flex flex-col items-center justify-center text-center h-20 border-2 border-dashed border-slate-100 rounded-xl">
                            <p class="text-sm text-slate-400">Tidak ada jadwal kuliah hari ini.</p>
                            <a href="schedule.php" class="text-xs font-bold text-primary mt-1 hover:underline">Lihat Jadwal Lengkap →</a>
                        </div>
                    <?php else: ?>
                        <div class="mt-4 space-y-2 max-h-32 overflow-y-auto">
                            <?php foreach ($todays_schedule as $class): ?>
                            <div class="flex items-center justify-between p-2 bg-purple-50 rounded-lg">
                                <div>
                                    <div class="text-xs font-bold text-purple-700"><?= htmlspecialchars($class['code']) ?></div>
                                    <div class="text-xs text-purple-600 truncate max-w-[120px]"><?= htmlspecialchars($class['course']) ?></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold text-slate-700"><?= $class['time'] ?></div>
                                    <div class="text-xs text-slate-500"><?= $class['room'] ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="schedule.php" class="block text-center text-xs font-bold text-primary mt-3 hover:underline">Lihat Jadwal Lengkap →</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col items-center text-center relative overflow-hidden group hover:border-amber-300 transition-all">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center text-3xl font-bold text-slate-400 mb-3 ring-4 ring-slate-50">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'S', 0, 1)) ?>
                </div>
                <h3 class="font-bold text-lg text-slate-900"><?= $_SESSION['full_name'] ?></h3>
                <p class="text-sm text-slate-500 mb-4">Mahasiswa</p>
                <a href="#" class="px-4 py-2 rounded-lg bg-slate-50 text-slate-600 text-sm font-medium hover:bg-slate-100 w-full transition-colors">
                    Edit Profil
                </a>
            </div>

        </div>

        <div class="mt-8">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wide mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="krs.php" class="flex flex-col items-center p-4 bg-white rounded-xl border border-slate-100 hover:border-primary hover:shadow-lg hover:shadow-primary/10 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-2 group-hover:scale-110 transition-transform">
                        <ion-icon name="document-text-outline"></ion-icon>
                    </div>
                    <span class="text-sm font-medium text-slate-700 group-hover:text-primary transition-colors">Isi KRS</span>
                </a>
                <a href="schedule.php" class="flex flex-col items-center p-4 bg-white rounded-xl border border-slate-100 hover:border-purple-500 hover:shadow-lg hover:shadow-purple-500/10 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-xl mb-2 group-hover:scale-110 transition-transform">
                        <ion-icon name="calendar-outline"></ion-icon>
                    </div>
                    <span class="text-sm font-medium text-slate-700 group-hover:text-purple-600 transition-colors">Jadwal</span>
                </a>
                <a href="grades.php" class="flex flex-col items-center p-4 bg-white rounded-xl border border-slate-100 hover:border-emerald-500 hover:shadow-lg hover:shadow-emerald-500/10 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-2 group-hover:scale-110 transition-transform">
                        <ion-icon name="ribbon-outline"></ion-icon>
                    </div>
                    <span class="text-sm font-medium text-slate-700 group-hover:text-emerald-600 transition-colors">Nilai</span>
                </a>
                <a href="payment.php" class="flex flex-col items-center p-4 bg-white rounded-xl border border-slate-100 hover:border-amber-500 hover:shadow-lg hover:shadow-amber-500/10 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-2 group-hover:scale-110 transition-transform">
                        <ion-icon name="wallet-outline"></ion-icon>
                    </div>
                    <span class="text-sm font-medium text-slate-700 group-hover:text-amber-600 transition-colors">Pembayaran</span>
                </a>
            </div>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
