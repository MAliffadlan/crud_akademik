<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('admin');
$pageTitle = 'Master Data Program Studi';
$active = 'config';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $programs = array_filter($_POST['programs'], fn($p) => trim($p) !== '');
    $programs_json = json_encode(array_values($programs));
    
    file_put_contents(__DIR__ . '/../../config/programs.json', $programs_json);
    header("Location: programs.php?msg=saved");
    exit;
}

$programs_file = __DIR__ . '/../../config/programs.json';
$programs = file_exists($programs_file) ? json_decode(file_get_contents($programs_file), true) : [
    'Teknologi Informasi',
    'Administrasi Bisnis',
    'Akuntansi',
    'Hubungan Masyarakat'
];
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Program Studi</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        <div class="max-w-3xl mx-auto">
            
            <div class="flex items-center gap-4 mb-8">
                <a href="config.php" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-primary hover:border-primary transition-all shadow-sm">
                    <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                </a>
                <div>
                    <h1 class="text-2xl font-display font-bold text-slate-900">Master Data Program Studi</h1>
                    <p class="text-slate-500">Kelola daftar program studi yang tersedia.</p>
                </div>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-6 p-4 rounded-xl border border-green-200 bg-green-50 text-green-700 flex items-center gap-3 animate-fade-in">
                    <ion-icon name="checkmark-circle" class="text-xl"></ion-icon>
                    <span class="font-medium">Data program studi berhasil disimpan!</span>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
                <form method="POST" id="programForm">
                    <div id="programList" class="space-y-4">
                        <?php foreach ($programs as $index => $program): ?>
                        <div class="flex items-center gap-3 animate-fade-in">
                            <div class="flex-1">
                                <input type="text" name="programs[]" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm" value="<?= htmlspecialchars($program) ?>" placeholder="Nama Program Studi">
                            </div>
                            <button type="button" class="p-2.5 rounded-xl border border-slate-200 text-slate-400 hover:text-red-500 hover:bg-red-50 hover:border-red-200 transition-all" onclick="this.closest('.flex').remove()">
                                <ion-icon name="trash-outline" class="text-lg block"></ion-icon>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <button type="button" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-dashed border-slate-300 text-slate-600 text-sm font-medium hover:bg-slate-50 hover:border-primary hover:text-primary transition-all flex items-center justify-center gap-2" onclick="addProgram()">
                            <ion-icon name="add-circle-outline" class="text-lg"></ion-icon>
                            Tambah Program
                        </button>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-bold shadow-lg shadow-primary/30 hover:bg-blue-600 hover:scale-[1.02] transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
function addProgram() {
    const list = document.getElementById('programList');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-3 animate-fade-in';
    div.innerHTML = `
        <div class="flex-1">
            <input type="text" name="programs[]" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm" placeholder="Nama Program Studi">
        </div>
        <button type="button" class="p-2.5 rounded-xl border border-slate-200 text-slate-400 hover:text-red-500 hover:bg-red-50 hover:border-red-200 transition-all" onclick="this.closest('.flex').remove()">
            <ion-icon name="trash-outline" class="text-lg block"></ion-icon>
        </button>
    `;
    list.appendChild(div);
}
</script>

<?php include '../../templates/footer.php'; ?>
