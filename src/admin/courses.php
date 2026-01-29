<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('admin');
$pageTitle = 'Master Data Mata Kuliah';
$active = 'config';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM courses WHERE id = $id");
    header("Location: courses.php?msg=deleted");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = clean($_POST['code']);
    $name = clean($_POST['name']);
    $credits = (int)$_POST['credits'];
    $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : 0;
    
    try {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE courses SET code=?, name=?, credits=? WHERE id=?");
            $stmt->bind_param("ssii", $code, $name, $credits, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO courses (code, name, credits) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $code, $name, $credits);
        }
        $stmt->execute();
        header("Location: courses.php?msg=saved");
        exit;
    } catch (mysqli_sql_exception $e) {
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}

$courses = $conn->query("SELECT * FROM courses ORDER BY code");

$edit_course = null;
$edit_id = 0;

if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
} elseif (isset($_POST['id']) && !empty($_POST['id'])) {
    $edit_id = (int)$_POST['id'];
}

if ($edit_id > 0) {
    $edit_course = $conn->query("SELECT * FROM courses WHERE id = $edit_id")->fetch_assoc();
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
            <span class="font-display font-bold text-lg text-slate-800">Mata Kuliah</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        
        <div class="flex items-center gap-4 mb-8">
            <a href="config.php" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-primary hover:border-primary transition-all shadow-sm">
                <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
            </a>
            <div>
                <h1 class="text-2xl font-display font-bold text-slate-900">Master Data Mata Kuliah</h1>
                <p class="text-slate-500">Kelola daftar mata kuliah dan bobot SKS.</p>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="mb-6 p-4 rounded-xl border border-green-200 bg-green-50 text-green-700 flex items-center gap-3 animate-fade-in">
                <ion-icon name="checkmark-circle" class="text-xl"></ion-icon>
                <span class="font-medium">
                    <?= $_GET['msg'] == 'deleted' ? 'Data berhasil dihapus!' : 'Data mata kuliah berhasil disimpan!' ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sticky top-8">
                    <h3 class="font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <ion-icon name="<?= $edit_course ? 'create-outline' : 'add-circle-outline' ?>" class="text-primary"></ion-icon>
                        <?= $edit_course ? 'Edit' : 'Tambah' ?> Mata Kuliah
                    </h3>
                    
                    <?php if (isset($error)): ?>
                        <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200 flex items-center gap-2">
                            <ion-icon name="alert-circle"></ion-icon>
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="space-y-5">
                        <?php if ($edit_course): ?>
                            <input type="hidden" name="id" value="<?= $edit_course['id'] ?>">
                        <?php endif; ?>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Ruangan</label>
                            <input type="text" name="code" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm" required value="<?= $edit_course['code'] ?? '' ?>" placeholder="Contoh: LAB A, R.201">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Nama Mata Kuliah</label>
                            <input type="text" name="name" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm" required value="<?= $edit_course['name'] ?? '' ?>" placeholder="Nama lengkap mata kuliah">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Bobot SKS</label>
                            <input type="number" name="credits" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm" required value="<?= $edit_course['credits'] ?? 3 ?>" min="1" max="6">
                        </div>
                        
                        <div class="pt-4 flex items-center gap-3">
                            <button type="submit" class="flex-1 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-bold shadow-lg shadow-primary/30 hover:bg-blue-600 hover:scale-[1.02] transition-all">
                                Simpan
                            </button>
                            <?php if ($edit_course): ?>
                                <a href="courses.php" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-900">Daftar Mata Kuliah</h3>
                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-bold"><?= $courses->num_rows ?> Data</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                                <th class="px-6 py-4">Ruangan</th>
                                    <th class="px-6 py-4">Nama Mata Kuliah</th>
                                    <th class="px-6 py-4 text-center">SKS</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php while ($row = $courses->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded border border-slate-200"><?= htmlspecialchars($row['code']) ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-slate-900"><?= htmlspecialchars($row['name']) ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-600 font-bold text-xs"><?= $row['credits'] ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                            <a href="courses.php?edit=<?= $row['id'] ?>" class="p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/5 transition-colors" title="Edit">
                                                <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                            </a>
                                            <a href="courses.php?delete=<?= $row['id'] ?>" onclick="return confirm('Hapus mata kuliah ini?')" class="p-2 rounded-lg text-slate-400 hover:text-danger hover:bg-red-50 transition-colors" title="Hapus">
                                                <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include '../../templates/footer.php'; ?>
