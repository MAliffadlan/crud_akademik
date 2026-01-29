<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('manager');
$pageTitle = 'Analisis ROI & Campaign';
$active = 'roi';

$start_date = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01'); // First of month
$end_date = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');

$leads_total = $conn->query("SELECT COUNT(*) as c FROM students WHERE registration_date BETWEEN '$start_date' AND '$end_date'")->fetch_assoc()['c'];
$leads_converted = $conn->query("SELECT COUNT(*) as c FROM students WHERE status = 'active' AND registration_date BETWEEN '$start_date' AND '$end_date'")->fetch_assoc()['c'];
$revenue = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'verified' AND payment_date BETWEEN '$start_date' AND '$end_date'")->fetch_assoc()['total'];

$marketing_cost = 5000000; 
$roi = $marketing_cost > 0 ? round((($revenue - $marketing_cost) / $marketing_cost) * 100, 1) : 0;
$cost_per_lead = $leads_total > 0 ? round($marketing_cost / $leads_total) : 0;
$cost_per_acquisition = $leads_converted > 0 ? round($marketing_cost / $leads_converted) : 0;
$conversion_rate = $leads_total > 0 ? round(($leads_converted / $leads_total) * 100, 1) : 0;

$sources = [
    ['source' => 'Website', 'leads' => rand(5, 15), 'cost' => 1500000],
    ['source' => 'Instagram', 'leads' => rand(3, 10), 'cost' => 2000000],
    ['source' => 'Referral', 'leads' => rand(2, 8), 'cost' => 500000],
    ['source' => 'Event/Pameran', 'leads' => rand(4, 12), 'cost' => 1000000],
];

$monthly_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month_start = date('Y-m-01', strtotime("-$i months"));
    $month_end = date('Y-m-t', strtotime("-$i months"));
    $count = $conn->query("SELECT COUNT(*) as c FROM students WHERE registration_date BETWEEN '$month_start' AND '$month_end'")->fetch_assoc()['c'];
    $rev = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'verified' AND payment_date BETWEEN '$month_start' AND '$month_end'")->fetch_assoc()['total'];
    $monthly_data[] = [
        'month' => date('M Y', strtotime($month_start)),
        'leads' => $count,
        'revenue' => $rev
    ];
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
            <span class="font-display font-bold text-lg text-slate-800">ROI Analysis</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'M', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-display font-bold text-slate-900">Analisis ROI & Campaign</h1>
                <p class="text-slate-500">Evaluasi efektivitas biaya marketing.</p>
            </div>
            <form method="GET" class="flex items-center gap-2 bg-white p-2 rounded-xl shadow-sm border border-slate-100">
                <input type="date" name="start" value="<?= $start_date ?>" class="rounded-lg border-slate-200 text-sm focus:border-primary focus:ring-primary/20">
                <span class="text-slate-400">-</span>
                <input type="date" name="end" value="<?= $end_date ?>" class="rounded-lg border-slate-200 text-sm focus:border-primary focus:ring-primary/20">
                <button type="submit" class="p-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
                    <ion-icon name="filter"></ion-icon>
                </button>
            </form>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 <?= $roi >= 0 ? 'border-emerald-500' : 'border-red-500' ?>">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">ROI</p>
                <h3 class="text-2xl font-display font-bold <?= $roi >= 0 ? 'text-emerald-600' : 'text-red-600' ?>"><?= $roi ?>%</h3>
                <p class="text-xs text-slate-400 mt-1">Return on Investment</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-blue-500">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Revenue</p>
                <h3 class="text-2xl font-display font-bold text-slate-900">Rp <?= number_format($revenue / 1000000, 1, ',', '.') ?>jt</h3>
                <p class="text-xs text-slate-400 mt-1">Total Verified</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-amber-500">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Cost per Lead</p>
                <h3 class="text-2xl font-display font-bold text-slate-900">Rp <?= number_format($cost_per_lead / 1000, 0, ',', '.') ?>k</h3>
                <p class="text-xs text-slate-400 mt-1">Avg Cost / Lead</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-purple-500">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Cost / Acq</p>
                <h3 class="text-2xl font-display font-bold text-slate-900">Rp <?= number_format($cost_per_acquisition / 1000, 0, ',', '.') ?>k</h3>
                <p class="text-xs text-slate-400 mt-1">Cost / Active Student</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xl mb-3">
                    <ion-icon name="people-outline"></ion-icon>
                </div>
                <h3 class="text-3xl font-display font-bold text-slate-900"><?= $leads_total ?></h3>
                <p class="text-sm font-medium text-slate-500">Total Leads</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center justify-center text-center relative">
                <!-- Arrow -->
                <div class="hidden md:block absolute -left-6 top-1/2 -translate-y-1/2 text-slate-300 text-2xl z-10">
                    <ion-icon name="arrow-forward"></ion-icon>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-3">
                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                </div>
                <h3 class="text-3xl font-display font-bold text-emerald-600"><?= $leads_converted ?></h3>
                <p class="text-sm font-medium text-slate-500">Converted</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-3">
                    <ion-icon name="pie-chart-outline"></ion-icon>
                </div>
                <h3 class="text-3xl font-display font-bold text-blue-600"><?= $conversion_rate ?>%</h3>
                <p class="text-sm font-medium text-slate-500">Conversion Rate</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <ion-icon name="megaphone-outline" class="text-primary"></ion-icon> Performance per Channel
                    </h3>
                </div>
                <div class="p-0">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                            <tr>
                                <th class="px-6 py-3">Channel</th>
                                <th class="px-6 py-3 text-center">Leads</th>
                                <th class="px-6 py-3 text-right">Cost</th>
                                <th class="px-6 py-3 text-right">CPL</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($sources as $src): 
                                $cpl = $src['leads'] > 0 ? $src['cost'] / $src['leads'] : 0;
                            ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3 font-medium text-slate-900"><?= $src['source'] ?></td>
                                <td class="px-6 py-3 text-center text-slate-600"><?= $src['leads'] ?></td>
                                <td class="px-6 py-3 text-right text-slate-600 text-sm">Rp <?= number_format($src['cost'] / 1000, 0, ',', '.') ?>k</td>
                                <td class="px-6 py-3 text-right text-sm font-bold <?= $cpl < 200000 ? 'text-emerald-600' : 'text-red-600' ?>">
                                    Rp <?= number_format($cpl / 1000, 0, ',', '.') ?>k
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <ion-icon name="stats-chart-outline" class="text-primary"></ion-icon> Trend 6 Bulan Terakhir
                </h3>
                <div class="h-[250px]">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#64748b';

new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($monthly_data, 'month')) ?>,
        datasets: [{
            label: 'Leads',
            data: <?= json_encode(array_column($monthly_data, 'leads')) ?>,
            backgroundColor: '#3b82f6',
            borderRadius: 4,
            barThickness: 30,
            yAxisID: 'y'
        }, {
            label: 'Revenue (jt)',
            data: <?= json_encode(array_map(fn($d) => round($d['revenue'] / 1000000, 1), $monthly_data)) ?>,
            type: 'line',
            borderColor: '#10b981',
            backgroundColor: '#10b981',
            borderWidth: 2,
            pointRadius: 4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } },
        scales: {
            y: { 
                beginAtZero: true, 
                position: 'left',
                grid: { borderDash: [2, 4], color: '#e2e8f0' }
            },
            y1: { 
                beginAtZero: true, 
                position: 'right', 
                grid: { display: false } 
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
</script>

<?php include '../../templates/footer.php'; ?>
