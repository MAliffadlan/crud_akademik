<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('academic');
$pageTitle = 'Validasi Pembayaran';
$active = 'validation';

// Handle Approve/Reject
if (isset($_GET['action']) && isset($_GET['id'])) {
    $payment_id = (int)$_GET['id'];
    $action = $_GET['action'];
    $verified_by = $_SESSION['user_id'];
    
    if ($action == 'approve') {
        $conn->query("UPDATE payments SET status='verified', verified_by=$verified_by WHERE id=$payment_id");
        
        // Also update student status to 'registered'
        $result = $conn->query("SELECT student_id FROM payments WHERE id=$payment_id");
        $row = $result->fetch_assoc();
        if ($row) {
            $conn->query("UPDATE students SET status='registered' WHERE id=" . $row['student_id']);
        }
        
        header("Location: validation.php?msg=approved");
        exit;
    } elseif ($action == 'reject') {
        $conn->query("UPDATE payments SET status='rejected', verified_by=$verified_by WHERE id=$payment_id");
        header("Location: validation.php?msg=rejected");
        exit;
    }
}

// Fetch pending payments
$query = "SELECT p.*, s.full_name, s.email, s.program_choice 
          FROM payments p 
          JOIN students s ON p.student_id = s.id 
          WHERE p.status = 'pending' 
          ORDER BY p.payment_date DESC";
$result = $conn->query($query);
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<!-- Main Wrapper -->
<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <!-- Mobile Header -->
    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Validasi</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        <div class="max-w-6xl mx-auto">
            
            <header class="mb-8">
                <h1 class="text-2xl font-display font-bold text-slate-900">Validasi Pembayaran</h1>
                <p class="text-slate-500">Verifikasi bukti pembayaran masuk dari mahasiswa.</p>
            </header>

            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] == 'approved'): ?>
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center gap-3 shadow-sm animate-fade-in">
                    <ion-icon name="checkmark-circle" class="text-xl"></ion-icon>
                    <div><span class="font-bold">Sukses!</span> Pembayaran berhasil diverifikasi.</div>
                </div>
                <?php elseif ($_GET['msg'] == 'rejected'): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 flex items-center gap-3 shadow-sm animate-fade-in">
                    <ion-icon name="alert-circle" class="text-xl"></ion-icon>
                    <div><span class="font-bold">Ditolak!</span> Pembayaran telah ditolak.</div>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <ion-icon name="hourglass-outline" class="text-amber-500"></ion-icon> Antrian Validasi
                    </h3>
                    <span class="text-xs font-bold text-slate-500 bg-white border border-slate-200 px-2 py-1 rounded-lg shadow-sm">
                        <?= $result->num_rows ?> Pending
                    </span>
                </div>

                <?php if ($result->num_rows == 0): ?>
                    <div class="text-center py-20">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-50 mb-6 animate-pulse">
                            <ion-icon name="checkmark-done-circle-outline" class="text-3xl text-emerald-300"></ion-icon>
                        </div>
                        <h3 class="text-lg font-bold text-emerald-900">Semua Beres!</h3>
                        <p class="text-emerald-600/70 mt-2">Tidak ada pembayaran yang perlu divalidasi saat ini.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                                <tr>
                                    <th class="px-6 py-4">Mahasiswa</th>
                                    <th class="px-6 py-4">Program</th>
                                    <th class="px-6 py-4">Jumlah Transfer</th>
                                    <th class="px-6 py-4 text-center">Bukti</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900"><?= htmlspecialchars($row['full_name']) ?></div>
                                        <div class="text-xs text-slate-500"><?= htmlspecialchars($row['email']) ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-slate-600 bg-slate-100 px-2 py-1 rounded">
                                            <?= htmlspecialchars($row['program_choice']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-mono font-bold text-slate-700">Rp <?= number_format($row['amount'], 0, ',', '.') ?></div>
                                        <div class="text-xs text-slate-400 mt-0.5"><?= date('d M Y', strtotime($row['payment_date'])) ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ($row['proof_file']): ?>
                                            <a href="/crud_akademik/public/<?= $row['proof_file'] ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 text-xs font-bold hover:bg-blue-100 transition-colors border border-blue-100">
                                                <ion-icon name="eye"></ion-icon> Lihat
                                            </a>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400 italic">Tanpa Bukti</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="validation.php?action=approve&id=<?= $row['id'] ?>" class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold shadow-sm hover:bg-emerald-700 transition-all flex items-center gap-1" onclick="return confirm('Approve pembayaran ini?')">
                                                <ion-icon name="checkmark"></ion-icon> Approve
                                            </a>
                                            <a href="validation.php?action=reject&id=<?= $row['id'] ?>" class="px-3 py-2 rounded-lg bg-white border border-red-200 text-red-600 text-xs font-bold hover:bg-red-50 transition-all flex items-center gap-1" onclick="return confirm('Tolak pembayaran ini?')">
                                                <ion-icon name="close"></ion-icon> Reject
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
