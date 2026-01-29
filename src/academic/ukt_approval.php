<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('academic');
$pageTitle = 'Pengajuan UKT';
$active = 'ukt';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        $conn->query("UPDATE payments SET status = 'verified', verified_by = {$_SESSION['user_id']}, verification_date = NOW() WHERE id = $id");
        header("Location: ukt_approval.php?msg=approved");
    } elseif ($action == 'reject') {
        $conn->query("UPDATE payments SET status = 'rejected', verified_by = {$_SESSION['user_id']}, verification_date = NOW() WHERE id = $id");
        header("Location: ukt_approval.php?msg=rejected");
    }
    exit;
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'pending';

$query = "SELECT p.*, s.full_name, s.email, s.phone, s.program_choice 
    FROM payments p
    JOIN students s ON p.student_id = s.id
    WHERE p.status = ?
    ORDER BY p.payment_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $status_filter);
$stmt->execute();
$payments = $stmt->get_result();

$pending_count = $conn->query("SELECT COUNT(*) as c FROM payments WHERE status = 'pending'")->fetch_assoc()['c'];
$verified_count = $conn->query("SELECT COUNT(*) as c FROM payments WHERE status = 'verified'")->fetch_assoc()['c'];
$rejected_count = $conn->query("SELECT COUNT(*) as c FROM payments WHERE status = 'rejected'")->fetch_assoc()['c'];
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Approval UKT</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        <div class="max-w-6xl mx-auto">
            
            <header class="mb-8">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <ion-icon name="wallet-outline" class="text-xl"></ion-icon>
                    </div>
                    <h1 class="text-2xl font-display font-bold text-slate-900">Validasi UKT</h1>
                </div>
                <p class="text-slate-500 ml-11">Kelola persetujuan pembayaran uang kuliah tunggal mahasiswa.</p>
            </header>

            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] == 'approved'): ?>
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center gap-3 shadow-sm animate-fade-in">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <ion-icon name="checkmark-outline" class="text-lg"></ion-icon>
                    </div>
                    <div>
                        <span class="font-bold">Berhasil!</span> Pembayaran telah disetujui.
                    </div>
                </div>
                <?php elseif ($_GET['msg'] == 'rejected'): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 flex items-center gap-3 shadow-sm animate-fade-in">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <ion-icon name="close-outline" class="text-lg"></ion-icon>
                    </div>
                    <div>
                        <span class="font-bold">Pembayaran Ditolak.</span> Status pembayaran telah diperbarui.
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                
                <div class="px-6 pt-6 border-b border-slate-100 flex gap-6 overflow-x-auto">
                    <a href="?status=pending" class="pb-4 px-2 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap <?= $status_filter == 'pending' ? 'border-primary text-primary font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">
                        Pending Review
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $status_filter == 'pending' ? 'bg-primary/10' : 'bg-slate-100' ?>">
                            <?= $pending_count ?>
                        </span>
                    </a>
                    <a href="?status=verified" class="pb-4 px-2 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap <?= $status_filter == 'verified' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">
                        Approved
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $status_filter == 'verified' ? 'bg-emerald-100' : 'bg-slate-100' ?>">
                            <?= $verified_count ?>
                        </span>
                    </a>
                    <a href="?status=rejected" class="pb-4 px-2 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap <?= $status_filter == 'rejected' ? 'border-red-500 text-red-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">
                        Rejected
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $status_filter == 'rejected' ? 'bg-red-100' : 'bg-slate-100' ?>">
                            <?= $rejected_count ?>
                        </span>
                    </a>
                </div>

                <div class="p-0">
                    <?php if ($payments->num_rows == 0): ?>
                        <div class="text-center py-20 px-4">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 mb-6">
                                <ion-icon name="folder-open-outline" class="text-3xl text-slate-300"></ion-icon>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900">Tidak ada data pembayaran.</h3>
                            <p class="text-slate-500 mt-2 max-w-sm mx-auto">
                                Belum ada pengajuan pembayaran dengan status 
                                <span class="font-bold text-slate-700">"<?= ucfirst($status_filter) ?>"</span> saat ini.
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50/50 text-slate-500 text-xs uppercase font-bold border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-4">Mahasiswa</th>
                                        <th class="px-6 py-4">Program</th>
                                        <th class="px-6 py-4">Detail Pembayaran</th>
                                        <th class="px-6 py-4 text-center">Bukti</th>
                                        <?php if ($status_filter == 'pending'): ?>
                                            <th class="px-6 py-4 text-center w-48">Aksi</th>
                                        <?php else: ?>
                                            <th class="px-6 py-4 text-center">Status</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php while ($row = $payments->fetch_assoc()): ?>
                                    <tr class="hover:bg-slate-50 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-900 group-hover:text-primary transition-colors">
                                                <?= htmlspecialchars($row['full_name']) ?>
                                            </div>
                                            <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                                <ion-icon name="mail-outline"></ion-icon> <?= htmlspecialchars($row['email']) ?>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100">
                                                <?= htmlspecialchars($row['program_choice']) ?>
                                            </span>
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <div class="font-mono font-bold text-slate-700">
                                                Rp <?= number_format($row['amount'], 0, ',', '.') ?>
                                            </div>
                                            <div class="text-xs text-slate-400 mt-0.5">
                                                <?= date('d M Y, H:i', strtotime($row['payment_date'])) ?>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 text-center">
                                            <?php if ($row['proof_file']): ?>
                                                <a href="/crud_akademik/public/<?= $row['proof_file'] ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                                                    <ion-icon name="image-outline"></ion-icon> Lihat Bukti
                                                </a>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-400 italic">Tidak ada</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <?php if ($status_filter == 'pending'): ?>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="?action=approve&id=<?= $row['id'] ?>&status=pending" class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center hover:bg-emerald-200 hover:scale-110 transition-all" title="Approve" onclick="return confirm('Konfirmasi: Setujui pembayaran ini?')">
                                                    <ion-icon name="checkmark-outline" class="text-xl"></ion-icon>
                                                </a>
                                                <a href="?action=reject&id=<?= $row['id'] ?>&status=pending" class="w-9 h-9 rounded-lg bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-200 hover:scale-110 transition-all" title="Reject" onclick="return confirm('Konfirmasi: Tolak pembayaran ini?')">
                                                    <ion-icon name="close-outline" class="text-xl"></ion-icon>
                                                </a>
                                            </div>
                                        </td>
                                        <?php else: ?>
                                        <td class="px-6 py-4 text-center">
                                            <?php if ($row['status'] == 'verified'): ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                                                    <ion-icon name="checkmark-circle"></ion-icon> Verified
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 text-red-700 text-xs font-bold border border-red-100">
                                                    <ion-icon name="ban"></ion-icon> Rejected
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
