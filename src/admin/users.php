<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('admin');
$pageTitle = 'Manajemen User';
$active = 'users';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: users.php?msg=deleted");
    exit;
}

// Fetch all users
$result = $conn->query("SELECT * FROM users ORDER BY role, full_name");
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
            <span class="font-display font-bold text-lg text-slate-800">Manajemen User</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-display font-bold text-slate-900 mb-1">Manajemen User</h1>
                <p class="text-slate-500">Kelola data pengguna dan hak akses sistem.</p>
            </div>
            <a href="user_form.php" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-600 shadow-lg shadow-blue-500/30 transition-all hover:scale-105">
                <ion-icon name="add-circle-outline" class="text-xl"></ion-icon> Tambah User
            </a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="mb-6 p-4 rounded-xl border border-green-200 bg-green-50 text-green-700 flex items-center gap-3 animate-fade-in">
                <ion-icon name="checkmark-circle" class="text-xl"></ion-icon>
                <span class="font-medium">
                    <?php 
                    if ($_GET['msg'] == 'deleted') echo 'User berhasil dihapus dari sistem.';
                    if ($_GET['msg'] == 'saved') echo 'Data user berhasil disimpan.';
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <!-- Content Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Username</th>
                            <th class="px-6 py-4">Nama Lengkap</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4 text-sm text-slate-500">#<?= $row['id'] ?></td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-slate-900"><?= htmlspecialchars($row['username']) ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <?php 
                                // Random avatar colors based on user ID (consistent per user)
                                $avatarColors = [
                                    ['bg' => 'bg-red-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-orange-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-amber-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-yellow-500', 'text' => 'text-slate-900'],
                                    ['bg' => 'bg-lime-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-green-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-emerald-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-teal-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-cyan-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-sky-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-blue-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-indigo-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-violet-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-purple-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-fuchsia-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-pink-500', 'text' => 'text-white'],
                                    ['bg' => 'bg-rose-500', 'text' => 'text-white'],
                                ];
                                $colorIndex = $row['id'] % count($avatarColors);
                                $avatarColor = $avatarColors[$colorIndex];
                                ?>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full <?= $avatarColor['bg'] ?> <?= $avatarColor['text'] ?> flex items-center justify-center text-xs font-bold ring-2 ring-white shadow-sm">
                                        <?= strtoupper(substr($row['full_name'], 0, 1)) ?>
                                    </div>
                                    <span class="text-sm text-slate-700"><?= htmlspecialchars($row['full_name']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php 
                                $bgClass = match($row['role']) {
                                    'admin' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                    'marketing' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'manager' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                    'academic' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'student' => 'bg-pink-50 text-pink-700 border-pink-100',
                                    default => 'bg-slate-50 text-slate-600 border-slate-100'
                                };
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $bgClass ?>">
                                    <?= ucfirst($row['role']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="user_form.php?id=<?= $row['id'] ?>" class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-colors" title="Edit">
                                        <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                    </a>
                                    <a href="users.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus user ini?')" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition-colors" title="Hapus">
                                        <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($result->num_rows == 0): ?>
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                        <ion-icon name="search-outline" class="text-2xl text-slate-400"></ion-icon>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900">Belum ada data user</h3>
                    <p class="text-slate-500 mt-1">Silakan tambahkan user baru untuk memulai.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php include '../../templates/footer.php'; ?>
