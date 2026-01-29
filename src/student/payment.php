<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('student');
$pageTitle = 'Status Pembayaran';
$active = 'payment';

$student_id = $_SESSION['student_id'] ?? 0;

$student = $conn->query("SELECT * FROM students WHERE id = $student_id")->fetch_assoc();

$payments = $conn->query("SELECT * FROM payments WHERE student_id = $student_id ORDER BY payment_date DESC");

$total_paid = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE student_id = $student_id AND status = 'verified'")->fetch_assoc()['total'];
$pending_amount = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE student_id = $student_id AND status = 'pending'")->fetch_assoc()['total'];

$total_ukt = 15000000; 
$remaining = $total_ukt - $total_paid;
$paid_percentage = $total_ukt > 0 ? round(($total_paid / $total_ukt) * 100) : 0;
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Keuangan</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'S', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        <div class="max-w-5xl mx-auto">
            
            <header class="mb-8">
                <h1 class="text-2xl font-display font-bold text-slate-900">Status Pembayaran UKT</h1>
                <p class="text-slate-500">Informasi tagihan dan riwayat pembayaran kuliah.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-emerald-500">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Total Terbayar</p>
                    <h3 class="text-2xl font-display font-bold text-emerald-600">Rp <?= number_format($total_paid, 0, ',', '.') ?></h3>
                    <p class="text-xs text-slate-400 mt-2">Verified Payments</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-amber-500">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pending Verifikasi</p>
                    <h3 class="text-2xl font-display font-bold text-amber-500">Rp <?= number_format($pending_amount, 0, ',', '.') ?></h3>
                    <p class="text-xs text-slate-400 mt-2">Menunggu Konfirmasi</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 <?= $remaining > 0 ? 'border-red-500' : 'border-emerald-500' ?>">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sisa Pembayaran</p>
                    <h3 class="text-2xl font-display font-bold <?= $remaining > 0 ? 'text-red-500' : 'text-emerald-500' ?>">Rp <?= number_format($remaining, 0, ',', '.') ?></h3>
                    <p class="text-xs text-slate-400 mt-2">Tagihan Tersisa</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-8">
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <h3 class="font-bold text-slate-900">Progress Pelunasan</h3>
                        <p class="text-sm text-slate-500">Target: Rp <?= number_format($total_ukt, 0, ',', '.') ?></p>
                    </div>
                    <span class="text-2xl font-bold text-primary"><?= $paid_percentage ?>%</span>
                </div>
                <div class="h-4 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full transition-all duration-1000 ease-out" style="width: <?= min($paid_percentage, 100) ?>%"></div>
                </div>
                <?php if ($remaining <= 0): ?>
                    <div class="mt-4 flex items-center gap-2 text-emerald-600 font-bold bg-emerald-50 p-3 rounded-xl border border-emerald-100">
                        <ion-icon name="checkmark-done-circle" class="text-xl"></ion-icon>
                        Selamat! Pembayaran Anda sudah lunas.
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <ion-icon name="receipt-outline" class="text-primary"></ion-icon> Riwayat Transaksi
                    </h3>
                    <button onclick="alert('Fitur upload bukti bayar')" class="text-sm font-bold text-primary hover:text-blue-700">
                        + Konfirmasi Pembayaran
                    </button>
                </div>
                
                <?php if ($payments->num_rows == 0): ?>
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                            <ion-icon name="wallet-outline" class="text-2xl text-slate-400"></ion-icon>
                        </div>
                        <h3 class="text-lg font-medium text-slate-900">Belum Ada Transaksi</h3>
                        <p class="text-slate-500 mt-1">Riwayat pembayaran Anda akan muncul disini.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                                <tr>
                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Jumlah Transfer</th>
                                    <th class="px-6 py-4 text-center">Bukti</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php while ($row = $payments->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-slate-600 font-medium text-sm">
                                        <?= date('d M Y', strtotime($row['payment_date'])) ?>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        Rp <?= number_format($row['amount'], 0, ',', '.') ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ($row['proof_file']): ?>
                                            <a href="/crud_akademik/public/<?= $row['proof_file'] ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 text-xs font-bold hover:bg-blue-100 transition-colors">
                                                <ion-icon name="eye"></ion-icon> Lihat
                                            </a>
                                        <?php else: ?>
                                            <span class="text-slate-300">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php 
                                        $statusClass = match($row['status']) {
                                            'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            'verified' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'rejected' => 'bg-red-50 text-red-700 border-red-100',
                                            default => 'bg-slate-50 text-slate-600 border-slate-100'
                                        };
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border <?= $statusClass ?>">
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

            <div class="mt-6 bg-blue-50 rounded-2xl border border-blue-100 p-6 flex gap-4">
                <ion-icon name="information-circle" class="text-2xl text-blue-500 flex-shrink-0"></ion-icon>
                <div>
                    <h4 class="font-bold text-blue-900 text-sm mb-2">Informasi Pembayaran</h4>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Total UKT per semester adalah <strong>Rp <?= number_format($total_ukt, 0, ',', '.') ?></strong>.</li>
                        <li>Pembayaran dapat dicicil maksimal 3 kali dalam satu semester.</li>
                        <li>Pastikan mengunggah bukti transfer yang jelas agar proses verifikasi lebih cepat.</li>
                        <li>Verifikasi pembayaran memakan waktu 1-3 hari kerja.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
