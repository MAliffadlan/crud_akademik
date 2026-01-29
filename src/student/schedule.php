<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('student');
$pageTitle = 'Jadwal Kuliah';
$active = 'schedule';

$user_id = $_SESSION['user_id'];
$student_result = $conn->query("SELECT id FROM students WHERE user_id = $user_id");
$student_row = $student_result->fetch_assoc();
$student_id = $student_row['id'] ?? 0;

$current_semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 1;

$query = "SELECT c.code, c.name, c.credits 
    FROM krs k
    JOIN courses c ON k.course_id = c.id
    WHERE k.student_id = ? AND k.semester = ?
    ORDER BY c.code";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $student_id, $current_semester);
$stmt->execute();
$courses = $stmt->get_result();

$days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
$time_slots = [
    ['08:00', '09:40'],
    ['10:00', '11:40'],
    ['13:00', '14:40'],
    ['15:00', '16:40'],
];

$schedule = [];
$course_list = [];
while ($c = $courses->fetch_assoc()) {
    $course_list[] = $c;
}

$idx = 0;
foreach ($course_list as $course) {
    $day_idx = $idx % 5;
    $time_idx = ($idx / 5) % 4;
    $schedule[$days[$day_idx]][$idx] = [
        'time' => $time_slots[floor($time_idx)][0] . ' - ' . $time_slots[floor($time_idx)][1],
        'course' => $course['name'],
        'code' => $course['code'],
        'room' => 'R.' . rand(101, 305),
    ];
    $idx++;
}
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Jadwal</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'S', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        <div class="max-w-6xl mx-auto">

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-display font-bold text-slate-900">Jadwal Perkuliahan</h1>
                    <p class="text-slate-500">Semester <?= $current_semester ?> Tahun Ajaran 2025/2026</p>
                </div>
                <div class="bg-white p-2 rounded-xl shadow-sm border border-slate-100">
                    <div class="relative">
                        <select name="semester" onchange="this.form.submit()" class="appearance-none pl-4 pr-10 py-2 rounded-lg border-slate-200 text-sm font-medium focus:border-primary focus:ring-primary/20 bg-white cursor-pointer hover:border-primary/50">
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                                <option value="<?= $i ?>" <?= $current_semester == $i ? 'selected' : '' ?>>Semester <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                            <ion-icon name="chevron-down-outline"></ion-icon>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (count($course_list) == 0): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                        <ion-icon name="calendar-clear-outline" class="text-2xl text-slate-400"></ion-icon>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Jadwal Kosong</h3>
                    <p class="text-slate-500 mt-1 mb-6">Belum ada mata kuliah yang diambil untuk semester ini.</p>
                    <a href="krs.php" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white font-bold shadow-lg shadow-primary/30 hover:bg-blue-600 transition-all">
                        <ion-icon name="add-circle-outline"></ion-icon>
                        Isi KRS Sekarang
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="p-6 border-b border-slate-100">
                                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                                    <ion-icon name="time-outline" class="text-primary"></ion-icon> Jadwal Mingguan
                                </h3>
                            </div>
                            <div class="divide-y divide-slate-100">
                                <?php foreach ($days as $day): ?>
                                <div class="p-6">
                                    <h4 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                                        <div class="w-2 h-8 bg-primary rounded-full"></div> <?= $day ?>
                                    </h4>
                                    
                                    <div class="space-y-3 pl-4">
                                        <?php if (empty($schedule[$day])): ?>
                                            <p class="text-sm text-slate-400 italic">Tidak ada jadwal kuliah.</p>
                                        <?php else: ?>
                                            <?php foreach ($schedule[$day] as $class): ?>
                                                <div class="group relative bg-blue-50/50 hover:bg-blue-50 border border-blue-100 rounded-xl p-4 transition-all">
                                                    <div class="flex items-start justify-between gap-4">
                                                        <div>
                                                            <div class="font-bold text-slate-900 group-hover:text-primary transition-colors"><?= $class['course'] ?></div>
                                                            <div class="text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-md inline-block mt-1"><?= $class['code'] ?></div>
                                                        </div>
                                                        <div class="text-right">
                                                            <div class="font-bold text-slate-700 text-sm"><?= $class['time'] ?></div>
                                                            <div class="text-xs text-slate-500 mt-1 flex items-center justify-end gap-1">
                                                                <ion-icon name="location-outline"></ion-icon> <?= $class['room'] ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden sticky top-24">
                            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                                    <ion-icon name="list-outline" class="text-slate-500"></ion-icon> Ringkasan Mata Kuliah
                                </h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                                        <tr>
                                            <th class="px-4 py-3">Mata Kuliah</th>
                                            <th class="px-4 py-3 text-center">SKS</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php 
                                        $total_sks = 0;
                                        foreach ($course_list as $course): 
                                            $total_sks += $course['credits'];
                                        ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-slate-900"><?= $course['name'] ?></div>
                                                <div class="text-xs text-slate-500 font-mono"><?= $course['code'] ?></div>
                                            </td>
                                            <td class="px-4 py-3 text-center text-sm font-bold text-slate-700"><?= $course['credits'] ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="bg-slate-50 border-t border-slate-200">
                                        <tr>
                                            <td class="px-4 py-3 text-sm font-bold text-slate-700 text-right">Total SKS</td>
                                            <td class="px-4 py-3 text-center text-sm font-bold text-primary"><?= $total_sks ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
