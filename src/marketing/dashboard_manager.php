<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('manager');
$pageTitle = 'Manager Dashboard';
$active = 'dashboard';

// Get overall stats
$total_leads = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'];
$active_students = $conn->query("SELECT COUNT(*) as c FROM students WHERE status = 'active'")->fetch_assoc()['c'];
$registered = $conn->query("SELECT COUNT(*) as c FROM students WHERE status = 'registered'")->fetch_assoc()['c'];
$pending_leads = $conn->query("SELECT COUNT(*) as c FROM students WHERE status = 'lead'")->fetch_assoc()['c'];

// Conversion rate
$conversion_rate = $total_leads > 0 ? round(($active_students / $total_leads) * 100, 1) : 0;

// Total revenue (verified payments)
$revenue = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'verified'")->fetch_assoc()['total'];

// Leads per day (last 7 days)
$daily_leads = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $count = $conn->query("SELECT COUNT(*) as c FROM students WHERE DATE(registration_date) = '$date'")->fetch_assoc()['c'];
    $daily_leads[] = ['date' => date('d M', strtotime($date)), 'count' => $count];
}

// Status distribution
$status_data = [
    'lead' => $pending_leads,
    'registered' => $registered,
    'active' => $active_students,
    'rejected' => $conn->query("SELECT COUNT(*) as c FROM students WHERE status = 'rejected'")->fetch_assoc()['c']
];

// Leads per program
$program_query = $conn->query("SELECT program_choice, COUNT(*) as count FROM students GROUP BY program_choice ORDER BY count DESC");
$program_data = [];
while ($row = $program_query->fetch_assoc()) {
    $program_data[] = $row;
}

// Top marketing staff
$staff_query = $conn->query("SELECT u.full_name, COUNT(s.id) as leads 
    FROM users u 
    LEFT JOIN students s ON u.id = s.marketing_id 
    WHERE u.role = 'marketing' 
    GROUP BY u.id 
    ORDER BY leads DESC 
    LIMIT 5");
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Manager</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'M', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-display font-bold text-slate-900 mb-2">Marketing Analytics</h1>
                <p class="text-slate-500">Overview performa marketing & sales campaign.</p>
            </div>
            <div class="text-sm text-slate-500 bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-100 flex items-center gap-2">
                <ion-icon name="calendar-outline"></ion-icon>
                <?= date('l, d F Y') ?>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-blue-500 relative overflow-hidden">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Leads</p>
                <h3 class="text-3xl font-display font-bold text-slate-900"><?= $total_leads ?></h3>
                <div class="absolute right-2 top-2 text-slate-100 text-6xl rotate-12 -z-0">
                    <ion-icon name="people"></ion-icon>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-amber-500 relative overflow-hidden">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pending</p>
                <h3 class="text-3xl font-display font-bold text-slate-900"><?= $pending_leads ?></h3>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-indigo-500 relative overflow-hidden">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Registered</p>
                <h3 class="text-3xl font-display font-bold text-slate-900"><?= $registered ?></h3>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-green-500 relative overflow-hidden">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Active</p>
                <h3 class="text-3xl font-display font-bold text-slate-900"><?= $active_students ?></h3>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-purple-500 relative overflow-hidden">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Conversion</p>
                <h3 class="text-3xl font-display font-bold text-slate-900"><?= $conversion_rate ?>%</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <ion-icon name="trending-up-outline" class="text-primary"></ion-icon>
                    Leads per Hari (7 Hari Terakhir)
                </h3>
                <div class="h-64">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <ion-icon name="pie-chart-outline" class="text-primary"></ion-icon>
                    Distribusi Status
                </h3>
                <div class="h-64">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <ion-icon name="school-outline" class="text-primary"></ion-icon>
                    Leads per Program Studi
                </h3>
                <div class="h-[240px]">
                    <canvas id="programChart"></canvas>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <ion-icon name="trophy-outline" class="text-amber-500"></ion-icon>
                        Top Marketing Staff
                    </h3>
                    <div class="overflow-hidden bg-slate-50 rounded-xl border border-slate-100">
                        <table class="w-full text-left">
                            <thead class="bg-slate-100 text-slate-500 text-xs uppercase font-bold">
                                <tr>
                                    <th class="px-4 py-3">Nama Staff</th>
                                    <th class="px-4 py-3 text-right">Total Leads</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <?php while ($staff = $staff_query->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 text-sm font-medium text-slate-700"><?= htmlspecialchars($staff['full_name']) ?></td>
                                    <td class="px-4 py-3 text-sm font-bold text-slate-900 text-right"><?= $staff['leads'] ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 rounded-2xl shadow-lg relative overflow-hidden text-white">
                    <div class="relative z-10">
                        <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Total Verified Revenue</p>
                        <h3 class="text-3xl font-display font-bold">Rp <?= number_format($revenue, 0, ',', '.') ?></h3>
                    </div>
                    <div class="absolute right-0 bottom-0 text-white/10 text-8xl -mr-4 -mb-4 rotate-12">
                        <ion-icon name="cash"></ion-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#64748b';

const colors = {
    primary: '#3b82f6',
    success: '#10b981',
    warning: '#f59e0b',
    danger: '#ef4444',
    purple: '#8b5cf6'
};

// Daily Leads Line Chart
new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($daily_leads, 'date')) ?>,
        datasets: [{
            label: 'Leads',
            data: <?= json_encode(array_column($daily_leads, 'count')) ?>,
            borderColor: colors.primary,
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: colors.primary,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                padding: 12,
                cornerRadius: 8,
                displayColors: false
            }
        },
        scales: { 
            y: { 
                beginAtZero: true, 
                grid: { borderDash: [2, 4], color: '#e2e8f0' },
                ticks: { padding: 10 }
            },
            x: {
                grid: { display: false },
                ticks: { padding: 10 }
            }
        }
    }
});

// Status Doughnut Chart
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Lead', 'Registered', 'Active', 'Rejected'],
        datasets: [{
            data: [<?= $status_data['lead'] ?>, <?= $status_data['registered'] ?>, <?= $status_data['active'] ?>, <?= $status_data['rejected'] ?>],
            backgroundColor: [colors.warning, colors.primary, colors.success, colors.danger],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } 
        },
        cutout: '70%',
    }
});

// Program Bar Chart
new Chart(document.getElementById('programChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($program_data, 'program_choice')) ?>,
        datasets: [{
            label: 'Leads',
            data: <?= json_encode(array_column($program_data, 'count')) ?>,
            backgroundColor: colors.primary,
            borderRadius: 6,
            barThickness: 30
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { 
            y: { 
                beginAtZero: true,
                grid: { borderDash: [2, 4], color: '#e2e8f0' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
</script>

<?php include '../../templates/footer.php'; ?>
