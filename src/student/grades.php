<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('student');
$pageTitle = 'Lihat Nilai';
$active = 'grades';

$user_id = $_SESSION['user_id'];
$student_result = $conn->query("SELECT id FROM students WHERE user_id = $user_id");
$student_row = $student_result->fetch_assoc();
$student_id = $student_row['id'] ?? 0;

$records = $conn->query("SELECT * FROM academic_records WHERE student_id = $student_id ORDER BY semester");

$total_gpa = 0;
$semester_count = 0;
$records_array = [];
while ($row = $records->fetch_assoc()) {
    $records_array[] = $row;
    if ($row['gpa']) {
        $total_gpa += $row['gpa'];
        $semester_count++;
    }
}
$cumulative_gpa = $semester_count > 0 ? round($total_gpa / $semester_count, 2) : 0;
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Nilai Akademik</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'S', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        <div class="max-w-5xl mx-auto">
            
            <header class="mb-8">
                <h1 class="text-2xl font-display font-bold text-slate-900">Nilai Akademik</h1>
                <p class="text-slate-500">Riwayat pencapaian akademik per semester.</p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                                <ion-icon name="stats-chart" class="text-primary"></ion-icon> Transkrip Nilai
                            </h3>
                        </div>
                        
                        <?php if (count($records_array) == 0): ?>
                            <div class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                                    <ion-icon name="document-text-outline" class="text-2xl text-slate-400"></ion-icon>
                                </div>
                                <h3 class="text-lg font-medium text-slate-900">Belum Ada Nilai</h3>
                                <p class="text-slate-500 mt-1">Nilai semester akan muncul disini setelah diinput.</p>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                                        <tr>
                                            <th class="px-6 py-4">Semester</th>
                                            <th class="px-6 py-4 text-center">IPK</th>
                                            <th class="px-6 py-4 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php foreach ($records_array as $record): ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-900">Semester <?= $record['semester'] ?></div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="text-lg font-bold <?= $record['gpa'] >= 3.0 ? 'text-emerald-600' : ($record['gpa'] >= 2.0 ? 'text-amber-500' : 'text-red-500') ?>">
                                                    <?= number_format($record['gpa'], 2) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold <?= $record['status_active'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' ?>">
                                                    <?= $record['status_active'] ? 'Aktif' : 'Tidak Aktif' ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-50 to-primary/10 rounded-full -mr-16 -mt-16 z-0"></div>
                        <div class="relative z-10">
                            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">IPK Kumulatif</h3>
                            <div class="text-5xl font-display font-bold text-primary mb-2">
                                <?= number_format($cumulative_gpa, 2) ?>
                            </div>
                            <p class="text-sm text-slate-400">Dari total <?= $semester_count ?> semester</p>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6">
                        <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <ion-icon name="information-circle-outline"></ion-icon> Keterangan Nilai
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-slate-100">
                                <span class="text-sm font-medium text-slate-600">Sangat Baik</span>
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-1 rounded-lg">≥ 3.00</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-slate-100">
                                <span class="text-sm font-medium text-slate-600">Baik</span>
                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-1 rounded-lg">2.00 - 2.99</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-slate-100">
                                <span class="text-sm font-medium text-slate-600">Perlu Perbaikan</span>
                                <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded-lg">< 2.00</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
