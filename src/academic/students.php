<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('academic');
$pageTitle = 'Data Mahasiswa';
$active = 'students';
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Data Mahasiswa</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        <div class="max-w-7xl mx-auto">
            
            <header class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-display font-bold text-slate-900">Data Mahasiswa</h1>
                    <p class="text-slate-500">Database seluruh mahasiswa terdaftar.</p>
                </div>
                <div class="relative w-full md:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <ion-icon name="search-outline"></ion-icon>
                    </span>
                    <input type="text" placeholder="Cari mahasiswa..." class="pl-10 pr-4 py-2 w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary/20 bg-white shadow-sm">
                </div>
            </header>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 text-slate-500 text-xs uppercase font-bold border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Nama Mahasiswa</th>
                                <th class="px-6 py-4">Program Studi</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Tanggal Daftar</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php
                            $result = $conn->query("SELECT * FROM students ORDER BY registration_date DESC");
                            if ($result->num_rows == 0):
                            ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                                        Belum ada data mahasiswa.
                                    </td>
                                </tr>
                            <?php else:
                                while ($row = $result->fetch_assoc()):
                                    $statusClass = match($row['status']) {
                                        'active' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'registered' => 'bg-blue-50 text-blue-700 border-blue-100',
                                        'lead' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        default => 'bg-slate-50 text-slate-600 border-slate-100'
                                    };
                            ?>
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold">
                                            <?= strtoupper(substr($row['full_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 group-hover:text-primary transition-colors"><?= htmlspecialchars($row['full_name']) ?></div>
                                            <div class="text-xs text-slate-400"><?= htmlspecialchars($row['email'] ?? '-') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-slate-600 bg-slate-50 px-2 py-1 rounded border border-slate-100">
                                        <?= htmlspecialchars($row['program_choice']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border <?= $statusClass ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-slate-500">
                                    <?= date('d M Y', strtotime($row['registration_date'])) ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="student_detail.php?id=<?= $row['id'] ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 hover:border-slate-300 hover:text-primary transition-all shadow-sm">
                                        <ion-icon name="scan-outline"></ion-icon> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($result->num_rows > 10): ?>
                <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex justify-center">
                    <button class="text-sm text-slate-500 hover:text-primary font-medium">Load More...</button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
