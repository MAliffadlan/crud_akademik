<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('manager');
$pageTitle = 'Monitoring Tim';
$active = 'monitoring';

$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$query = "SELECT 
    u.id, u.full_name, u.username,
    COUNT(s.id) as total_leads,
    SUM(CASE WHEN DATE(s.registration_date) = ? THEN 1 ELSE 0 END) as today_leads,
    SUM(CASE WHEN s.status = 'active' THEN 1 ELSE 0 END) as converted,
    SUM(CASE WHEN s.status = 'lead' THEN 1 ELSE 0 END) as pending,
    MAX(s.registration_date) as last_activity
    FROM users u
    LEFT JOIN students s ON u.id = s.marketing_id
    WHERE u.role = 'marketing'
    GROUP BY u.id
    ORDER BY today_leads DESC, total_leads DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $selected_date);
$stmt->execute();
$staff_result = $stmt->get_result();

$team_totals = $conn->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN DATE(registration_date) = '$selected_date' THEN 1 ELSE 0 END) as today
    FROM students")->fetch_assoc();

$daily_target = 5;
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Team Monitor</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'M', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-display font-bold text-slate-900">Monitoring Tim Marketing</h1>
                <p class="text-slate-500">Pantau produktivitas harian tim Anda.</p>
            </div>
            <form method="GET" class="flex items-center gap-2 bg-white p-2 rounded-xl shadow-sm border border-slate-100">
                <label class="text-sm font-medium text-slate-600 pl-2">Tanggal:</label>
                <input type="date" name="date" value="<?= $selected_date ?>" class="rounded-lg border-slate-200 text-sm focus:border-primary focus:ring-primary/20" onchange="this.form.submit()">
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-blue-500 text-center">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Input Hari Ini</p>
                <h3 class="text-4xl font-display font-bold text-slate-900"><?= $team_totals['today'] ?? 0 ?></h3>
                <p class="text-xs text-slate-400 mt-2">Leads</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-emerald-500 text-center">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Total Leads</p>
                <h3 class="text-4xl font-display font-bold text-slate-900"><?= $team_totals['total'] ?? 0 ?></h3>
                <p class="text-xs text-slate-400 mt-2">All Time</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-purple-500 text-center">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Target Harian</p>
                <h3 class="text-4xl font-display font-bold text-slate-900"><?= $daily_target ?></h3>
                <p class="text-xs text-slate-400 mt-2">Leads / Staff</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <ion-icon name="podium-outline" class="text-primary"></ion-icon> Performa Staff
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                        <tr>
                            <th class="px-6 py-4">Staff Name</th>
                            <th class="px-6 py-4 text-center">Input Hari Ini</th>
                            <th class="px-6 py-4">Target Status</th>
                            <th class="px-6 py-4 text-center">Total</th>
                            <th class="px-6 py-4 text-center">Converted</th>
                            <th class="px-6 py-4 text-center">Pending</th>
                            <th class="px-6 py-4">Conversion Rate</th>
                            <th class="px-6 py-4 text-right">Last Activity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if ($staff_result->num_rows == 0): ?>
                            <tr><td colspan="8" class="px-6 py-12 text-center text-slate-500">Belum ada staff marketing.</td></tr>
                        <?php else: ?>
                            <?php while ($row = $staff_result->fetch_assoc()): 
                                $rate = $row['total_leads'] > 0 ? round(($row['converted'] / $row['total_leads']) * 100, 1) : 0;
                                $target_met = $row['today_leads'] >= $daily_target;
                            ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900"><?= htmlspecialchars($row['full_name']) ?></div>
                                    <div class="text-xs text-slate-500">@<?= htmlspecialchars($row['username']) ?></div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-2xl font-bold <?= $target_met ? 'text-emerald-600' : 'text-slate-400' ?>">
                                        <?= $row['today_leads'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($target_met): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-xs font-bold">
                                            <ion-icon name="checkmark-circle"></ion-icon> Tercapai
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-700 border border-red-100 text-xs font-bold">
                                            <ion-icon name="alert-circle"></ion-icon> Kurang <?= $daily_target - $row['today_leads'] ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-700"><?= $row['total_leads'] ?></td>
                                <td class="px-6 py-4 text-center text-emerald-600 font-medium"><?= $row['converted'] ?></td>
                                <td class="px-6 py-4 text-center text-amber-500 font-medium"><?= $row['pending'] ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden w-24">
                                            <div class="h-full rounded-full <?= $rate >= 50 ? 'bg-emerald-500' : ($rate >= 25 ? 'bg-amber-500' : 'bg-red-500') ?>" style="width: <?= min($rate, 100) ?>%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-600"><?= $rate ?>%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-xs text-slate-500 font-mono">
                                    <?= $row['last_activity'] ? date('d M H:i', strtotime($row['last_activity'])) : '-' ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
            <h3 class="text-sm font-bold text-slate-800 mb-2 flex items-center gap-2">
                <ion-icon name="information-circle-outline"></ion-icon> Catatan
            </h3>
            <ul class="text-xs text-slate-600 space-y-1 list-disc list-inside">
                <li>Target harian adalah <strong><?= $daily_target ?> leads</strong> per staff.</li>
                <li>Indikator hijau menandakan target tercapai, merah menandakan belum tercapai.</li>
                <li>Rate adalah persentase kesuksesan konversi dari Lead menjadi Active Student.</li>
            </ul>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
