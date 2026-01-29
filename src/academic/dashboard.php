<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('academic');
$pageTitle = 'Academic Dashboard';
$active = 'dashboard';

$pending_payments = $conn->query("SELECT COUNT(*) as c FROM payments WHERE status = 'pending'")->fetch_assoc()['c'];
$verified_payments = $conn->query("SELECT COUNT(*) as c FROM payments WHERE status = 'verified'")->fetch_assoc()['c'];
$total_students = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'];
$active_students = $conn->query("SELECT COUNT(*) as c FROM students WHERE status = 'active'")->fetch_assoc()['c'];
$registered_students = $conn->query("SELECT COUNT(*) as c FROM students WHERE status = 'registered'")->fetch_assoc()['c'];

$recent_pending = $conn->query("SELECT p.*, s.full_name, s.program_choice 
    FROM payments p 
    JOIN students s ON p.student_id = s.id 
    WHERE p.status = 'pending' 
    ORDER BY p.payment_date DESC 
    LIMIT 5");

$recent_students = $conn->query("SELECT * FROM students ORDER BY registration_date DESC LIMIT 5");
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Academic</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        <div class="max-w-7xl mx-auto">
            
            <div class="mb-8 border-b border-slate-200 pb-6">
                <h1 class="text-3xl font-display font-bold text-slate-900 mb-2">Academic Dashboard</h1>
                <p class="text-slate-500">Overview operasional akademik dan pembayaran mahasiswa.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group hover:border-amber-300 transition-all">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Pending Payment</p>
                        <h3 class="text-3xl font-display font-bold text-amber-500"><?= $pending_payments ?></h3>
                        <div class="mt-2 text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg inline-block">PERLU VALIDASI</div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group hover:border-emerald-300 transition-all">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Verified</p>
                        <h3 class="text-3xl font-display font-bold text-emerald-600"><?= $verified_payments ?></h3>
                        <div class="mt-2 text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg inline-block">SUDAH LUNAS</div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group hover:border-blue-300 transition-all">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Total Mahasiswa</p>
                        <h3 class="text-3xl font-display font-bold text-slate-900"><?= $total_students ?></h3>
                        <div class="mt-2 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg inline-block">TERDAFTAR</div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group hover:border-purple-300 transition-all">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-purple-50 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Status Aktif</p>
                        <h3 class="text-3xl font-display font-bold text-purple-600"><?= $active_students ?></h3>
                        <div class="mt-2 text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-lg inline-block">MAHASISWA AKTIF</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-900 flex items-center gap-2">
                            <ion-icon name="time-outline" class="text-amber-500"></ion-icon> Pembayaran Pending
                        </h3>
                        <a href="ukt_approval.php" class="text-sm font-bold text-primary hover:text-blue-700">Lihat Semua →</a>
                    </div>
                    
                    <div class="flex-1 overflow-x-auto">
                        <?php if ($recent_pending->num_rows == 0): ?>
                            <div class="text-center py-12">
                                <p class="text-slate-400 italic">Tidak ada pembayaran pending saat ini.</p>
                            </div>
                        <?php else: ?>
                            <table class="w-full text-left">
                                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                                    <tr>
                                        <th class="px-6 py-4">Mahasiswa</th>
                                        <th class="px-6 py-4">Jumlah</th>
                                        <th class="px-6 py-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php while ($row = $recent_pending->fetch_assoc()): ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($row['full_name']) ?></div>
                                            <div class="text-xs text-slate-500"><?= htmlspecialchars($row['program_choice']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-slate-700 text-sm">
                                            Rp <?= number_format($row['amount'], 0, ',', '.') ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="ukt_approval.php?action=approve&id=<?= $row['id'] ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 hover:bg-emerald-200 transition-colors" title="Approve">
                                                <ion-icon name="checkmark-outline"></ion-icon>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-900 flex items-center gap-2">
                            <ion-icon name="people-outline" class="text-blue-500"></ion-icon> Mahasiswa Terbaru
                        </h3>
                        <a href="students.php" class="text-sm font-bold text-primary hover:text-blue-700">Lihat Semua →</a>
                    </div>
                    
                    <div class="flex-1 overflow-x-auto">
                        <?php if ($recent_students->num_rows == 0): ?>
                            <div class="text-center py-12">
                                <p class="text-slate-400 italic">Belum ada data mahasiswa.</p>
                            </div>
                        <?php else: ?>
                            <table class="w-full text-left">
                                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                                    <tr>
                                        <th class="px-6 py-4">Nama</th>
                                        <th class="px-6 py-4">Program</th>
                                        <th class="px-6 py-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php while ($row = $recent_students->fetch_assoc()): 
                                        $statusClass = match($row['status']) {
                                            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'registered' => 'bg-blue-50 text-blue-700 border-blue-100',
                                            'lead' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            default => 'bg-slate-50 text-slate-600'
                                        };
                                    ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 font-bold text-slate-900 text-sm">
                                            <?= htmlspecialchars($row['full_name']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-slate-600 text-xs">
                                            <?= htmlspecialchars($row['program_choice']) ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold border <?= $statusClass ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-4 text-sm uppercase tracking-wide">Quick Actions</h3>
                <div class="flex flex-wrap gap-4">
                    <a href="ukt_approval.php" class="flex items-center gap-3 px-5 py-3 rounded-xl bg-primary text-white font-bold hover:bg-blue-700 transition-all shadow-lg shadow-primary/20 hover:scale -[1.02]">
                        <ion-icon name="wallet-outline" class="text-xl"></ion-icon>
                        Approval UKT
                    </a>
                    <a href="validation.php" class="flex items-center gap-3 px-5 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition-all hover:border-slate-300">
                        <ion-icon name="checkmark-done-circle-outline" class="text-xl text-purple-500"></ion-icon>
                        Validasi Pembayaran
                    </a>
                    <a href="students.php" class="flex items-center gap-3 px-5 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition-all hover:border-slate-300">
                        <ion-icon name="people-circle-outline" class="text-xl text-emerald-500"></ion-icon>
                        Data Mahasiswa
                    </a>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
