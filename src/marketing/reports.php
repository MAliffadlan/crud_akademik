<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('marketing');
$pageTitle = 'Laporan Harian';
$active = 'reports';

$marketing_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$selected_date = isset($_GET['date']) ? $_GET['date'] : $today;

$query = "SELECT * FROM students WHERE marketing_id = ? AND DATE(registration_date) = ? ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $marketing_id, $selected_date);
$stmt->execute();
$leads = $stmt->get_result();

$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'lead' THEN 1 ELSE 0 END) as leads,
    SUM(CASE WHEN status = 'registered' THEN 1 ELSE 0 END) as registered,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active
    FROM students WHERE marketing_id = ? AND DATE(registration_date) = ?";
$stmt2 = $conn->prepare($stats_query);
$stmt2->bind_param("is", $marketing_id, $selected_date);
$stmt2->execute();
$stats = $stmt2->get_result()->fetch_assoc();
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50 print:bg-white print:overflow-visible">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20 print:hidden">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Laporan</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'M', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in print:overflow-visible print:p-0">
        <div class="max-w-5xl mx-auto">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8 print:hidden">
                <div>
                    <h1 class="text-2xl font-display font-bold text-slate-900">Laporan Kegiatan Harian</h1>
                    <p class="text-slate-500">Rekap data input harian Anda.</p>
                </div>
                <form method="GET" class="flex items-center gap-3 bg-white p-2 rounded-xl shadow-sm border border-slate-100">
                    <label class="text-sm font-medium text-slate-600 pl-2">Tanggal:</label>
                    <input type="date" name="date" value="<?= $selected_date ?>" class="rounded-lg border-slate-200 text-sm focus:border-primary focus:ring-primary/20" onchange="this.form.submit()">
                </form>
            </div>

            <div class="hidden print:block mb-8 text-center border-b border-slate-900 pb-6">
                <h1 class="text-2xl font-bold uppercase tracking-wider mb-2">Laporan Harian Marketing</h1>
                <p class="text-slate-600">Dicetak oleh: <?= $_SESSION['full_name'] ?> | Tanggal Output: <?= date('d F Y') ?></p>
                <p class="text-slate-600">Periode Laporan: <?= date('d F Y', strtotime($selected_date)) ?></p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 print:grid-cols-4 print:gap-4">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center print:border print:border-slate-300">
                    <h3 class="text-3xl font-display font-bold text-slate-900 mb-1"><?= $stats['total'] ?? 0 ?></h3>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Total Input</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center print:border print:border-slate-300">
                    <h3 class="text-3xl font-display font-bold text-amber-500 mb-1"><?= $stats['leads'] ?? 0 ?></h3>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Leads</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center print:border print:border-slate-300">
                    <h3 class="text-3xl font-display font-bold text-blue-500 mb-1"><?= $stats['registered'] ?? 0 ?></h3>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Registered</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center print:border print:border-slate-300">
                    <h3 class="text-3xl font-display font-bold text-green-500 mb-1"><?= $stats['active'] ?? 0 ?></h3>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Active</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden print:shadow-none print:border print:border-slate-300">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between print:hidden">
                    <h3 class="font-bold text-slate-900">Daftar Input Tanggal <?= date('d M Y', strtotime($selected_date)) ?></h3>
                    <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-50 text-slate-600 text-sm font-bold hover:bg-slate-100 transition-colors">
                        <ion-icon name="print-outline"></ion-icon> Cetak
                    </button>
                </div>
                
                <?php if ($leads->num_rows == 0): ?>
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                            <ion-icon name="document-text-outline" class="text-2xl text-slate-400"></ion-icon>
                        </div>
                        <h3 class="text-lg font-medium text-slate-900">Tidak ada data</h3>
                        <p class="text-slate-500 mt-1">Belum ada input data pada tanggal ini.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-semibold print:bg-slate-100 print:text-black">
                                    <th class="px-6 py-4 w-16">No</th>
                                    <th class="px-6 py-4">Nama Lengkap</th>
                                    <th class="px-6 py-4">Asal Sekolah</th>
                                    <th class="px-6 py-4">Program Studi</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 print:divide-slate-300">
                                <?php $no = 1; while ($row = $leads->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-slate-500 text-sm text-center"><?= $no++ ?></td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900"><?= htmlspecialchars($row['full_name']) ?></div>
                                        <div class="text-xs text-slate-500"><?= htmlspecialchars($row['phone']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600"><?= htmlspecialchars($row['school_origin']) ?></td>
                                    <td class="px-6 py-4 text-sm text-slate-600"><?= htmlspecialchars($row['program_choice']) ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <?php 
                                        $statusClass = match($row['status']) {
                                            'lead' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            'registered' => 'bg-blue-50 text-blue-700 border-blue-100',
                                            'active' => 'bg-green-50 text-green-700 border-green-100',
                                            'rejected' => 'bg-red-50 text-red-700 border-red-100',
                                            default => 'bg-slate-50 text-slate-600 border-slate-100'
                                        };
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border <?= $statusClass ?> print:border print:border-slate-400 print:text-black print:bg-transparent">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="hidden print:block mt-8 pt-8 border-t border-slate-300">
                <div class="flex justify-between text-sm text-slate-600">
                    <div>
                        <p>Mengetahui,</p>
                        <br><br><br>
                        <p class="font-bold border-t border-slate-400 inline-block pr-12 pt-1">Marketing Manager</p>
                    </div>
                    <div class="text-right">
                        <p>Jakarta, <?= date('d F Y') ?></p>
                        <br><br><br>
                        <p class="font-bold border-t border-slate-400 inline-block pl-12 pt-1"><?= $_SESSION['full_name'] ?></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
