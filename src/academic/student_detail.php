<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('academic');
$pageTitle = 'Detail Mahasiswa';
$active = 'students';

$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle status change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_status'])) {
    $new_status = clean($_POST['new_status']);
    $conn->query("UPDATE students SET status='$new_status' WHERE id=$student_id");
    
    // If changing to 'active', create user account for student
    if ($new_status == 'active') {
        // Check if user account already exists
        $check = $conn->query("SELECT user_id FROM students WHERE id=$student_id")->fetch_assoc();
        if (!$check['user_id']) {
            // Get student data
            $student = $conn->query("SELECT * FROM students WHERE id=$student_id")->fetch_assoc();
            
            // Create username from email
            $username = explode('@', $student['email'])[0];
            $default_password = password_hash('mahasiswa123', PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, 'student')");
            $stmt->bind_param("sss", $username, $default_password, $student['full_name']);
            $stmt->execute();
            $new_user_id = $conn->insert_id;
            
            // Link user to student
            $conn->query("UPDATE students SET user_id=$new_user_id WHERE id=$student_id");
        }
    }
    
    header("Location: student_detail.php?id=$student_id&msg=updated");
    exit;
}

// Fetch student data
$student = $conn->query("SELECT s.*, u.username FROM students s LEFT JOIN users u ON s.user_id = u.id WHERE s.id=$student_id")->fetch_assoc();

if (!$student) {
    header("Location: students.php");
    exit;
}

// Fetch documents
$documents = $conn->query("SELECT * FROM documents WHERE student_id=$student_id");

// Fetch payments
$payments = $conn->query("SELECT * FROM payments WHERE student_id=$student_id ORDER BY payment_date DESC");
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
            <span class="font-display font-bold text-lg text-slate-800">Detail Mahasiswa</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        <div class="max-w-6xl mx-auto">
            
            <!-- Header & Actions -->
            <div class="mb-8 flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div>
                     <a href="students.php" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-primary mb-2 transition-colors">
                        <ion-icon name="arrow-back-outline"></ion-icon> Kembali ke Data Mahasiswa
                    </a>
                    <h1 class="text-2xl font-display font-bold text-slate-900 flex items-center gap-2">
                        <?= htmlspecialchars($student['full_name']) ?>
                        <?php 
                        $statusClass = match($student['status']) {
                            'active' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'registered' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'lead' => 'bg-amber-100 text-amber-700 border-amber-200',
                            default => 'bg-slate-100 text-slate-600 border-slate-200'
                        };
                        ?>
                        <span class="text-xs px-2.5 py-1 rounded-full border <?= $statusClass ?>">
                            <?= ucfirst($student['status']) ?>
                        </span>
                    </h1>
                </div>
                
                <div class="flex gap-3">
                    <?php if ($student['status'] == 'active'): ?>
                        <a href="print_letter.php?id=<?= $student_id ?>" target="_blank" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-emerald-200 text-emerald-700 font-bold shadow-sm hover:bg-emerald-50 transition-all">
                            <ion-icon name="print-outline" class="text-lg"></ion-icon>
                            Cetak Surat
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center gap-3 shadow-sm animate-fade-in">
                    <ion-icon name="checkmark-circle" class="text-xl"></ion-icon>
                    <div><span class="font-bold">Sukses!</span> Status mahasiswa berhasil diperbarui.</div>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Main Info Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Data Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                                <ion-icon name="person-outline" class="text-primary"></ion-icon> Data Pribadi
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email</label>
                                    <div class="font-medium text-slate-700 break-all"><?= htmlspecialchars($student['email']) ?></div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Telepon</label>
                                    <div class="font-medium text-slate-700"><?= htmlspecialchars($student['phone']) ?></div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Asal Sekolah</label>
                                    <div class="font-medium text-slate-700"><?= htmlspecialchars($student['school_origin']) ?></div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pilihan Program</label>
                                    <div class="font-medium text-slate-700"><?= htmlspecialchars($student['program_choice']) ?></div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Daftar</label>
                                    <div class="font-medium text-slate-700"><?= date('d F Y', strtotime($student['registration_date'])) ?></div>
                                </div>
                                <?php if ($student['username']): ?>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Akun Login</label>
                                    <div class="font-mono text-sm bg-slate-100 px-2 py-1 rounded inline-block text-slate-600">
                                        <?= $student['username'] ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Update Status Form -->
                            <div class="mt-8 pt-6 border-t border-slate-100">
                                <form method="POST" class="flex flex-col md:flex-row items-end gap-4">
                                    <div class="w-full">
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Update Status Mahasiswa</label>
                                        <div class="relative">
                                            <select name="new_status" class="appearance-none w-full pl-4 pr-10 py-3 rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary/20 bg-white shadow-sm cursor-pointer hover:border-primary/50 transition-all">
                                                <option value="lead" <?= $student['status'] == 'lead' ? 'selected' : '' ?>>Lead (Prospek)</option>
                                                <option value="registered" <?= $student['status'] == 'registered' ? 'selected' : '' ?>>Registered (Sudah Bayar)</option>
                                                <option value="active" <?= $student['status'] == 'active' ? 'selected' : '' ?>>Active (Mahasiswa Resmi)</option>
                                                <option value="rejected" <?= $student['status'] == 'rejected' ? 'selected' : '' ?>>Rejected (Ditolak)</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                                <ion-icon name="chevron-down-outline"></ion-icon>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full md:w-auto px-6 py-3 rounded-xl bg-primary text-white font-bold shadow-lg shadow-primary/30 hover:bg-blue-600 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2 whitespace-nowrap">
                                        <ion-icon name="save-outline"></ion-icon>
                                        Simpan Perubahan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Payment History Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                                <ion-icon name="receipt-outline" class="text-emerald-500"></ion-icon> Riwayat Pembayaran
                            </h3>
                        </div>
                        <?php if ($payments->num_rows == 0): ?>
                            <div class="p-8 text-center text-slate-400 italic">Belum ada data pembayaran.</div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                                        <tr>
                                            <th class="px-6 py-3">Tanggal</th>
                                            <th class="px-6 py-3">Jumlah</th>
                                            <th class="px-6 py-3 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php while ($pay = $payments->fetch_assoc()): ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-3 text-sm text-slate-600"><?= $pay['payment_date'] ?></td>
                                            <td class="px-6 py-3 font-bold text-slate-700">Rp <?= number_format($pay['amount'], 0, ',', '.') ?></td>
                                            <td class="px-6 py-3 text-center">
                                                <?php 
                                                $payStatus = match($pay['status']) {
                                                    'verified' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                    'rejected' => 'bg-red-50 text-red-700 border-red-100',
                                                    default => 'bg-amber-50 text-amber-700 border-amber-100'
                                                };
                                                ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold border <?= $payStatus ?>">
                                                    <?= ucfirst($pay['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar Column (Documents) -->
                <div>
                     <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden sticky top-24">
                        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                                <ion-icon name="folder-open-outline" class="text-blue-500"></ion-icon> Dokumen Upload
                            </h3>
                        </div>
                        <div class="p-2">
                            <?php if ($documents->num_rows == 0): ?>
                                <div class="p-6 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 mb-3">
                                        <ion-icon name="document-text-outline" class="text-xl text-slate-300"></ion-icon>
                                    </div>
                                    <p class="text-sm text-slate-400">Belum ada dokumen yang diunggah.</p>
                                </div>
                            <?php else: ?>
                                <ul class="space-y-1">
                                    <?php while ($doc = $documents->fetch_assoc()): ?>
                                    <li>
                                        <a href="/crud_akademik/public/<?= $doc['file_path'] ?>" target="_blank" class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                                    <ion-icon name="document"></ion-icon>
                                                </div>
                                                <span class="text-sm font-medium text-slate-700 group-hover:text-primary transition-colors">
                                                    <?= ucfirst(str_replace('_', ' ', $doc['type'])) ?>
                                                </span>
                                            </div>
                                            <ion-icon name="open-outline" class="text-slate-400 group-hover:text-primary"></ion-icon>
                                        </a>
                                    </li>
                                    <?php endwhile; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                     </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
