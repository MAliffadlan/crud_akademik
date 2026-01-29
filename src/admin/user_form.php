<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('admin');
$pageTitle = 'Form User';
$active = 'users';

$user = null;
$isEdit = false;

// If editing, fetch user data
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = $conn->query("SELECT * FROM users WHERE id = $id");
    $user = $result->fetch_assoc();
    $isEdit = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username']);
    $full_name = clean($_POST['full_name']);
    $role = clean($_POST['role']);
    $password = $_POST['password'];
    
    if ($isEdit) {
        // Update
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username=?, full_name=?, role=?, password=? WHERE id=?");
            $stmt->bind_param("ssssi", $username, $full_name, $role, $hashed, $_POST['id']);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, full_name=?, role=? WHERE id=?");
            $stmt->bind_param("sssi", $username, $full_name, $role, $_POST['id']);
        }
    } else {
        // Insert
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed, $full_name, $role);
    }
    
    if ($stmt->execute()) {
        header("Location: users.php?msg=saved");
        exit;
    }
}
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
            <span class="font-display font-bold text-lg text-slate-800">Form User</span>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        <div class="max-w-2xl mx-auto">
            
            <div class="flex items-center gap-4 mb-8">
                <a href="users.php" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-primary hover:border-primary transition-all shadow-sm">
                    <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                </a>
                <div>
                    <h1 class="text-2xl font-display font-bold text-slate-900"><?= $isEdit ? 'Edit User' : 'Tambah User Baru' ?></h1>
                    <p class="text-slate-500">Lengkapi form di bawah ini.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
                <form method="POST" class="space-y-6">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                            <input type="text" name="username" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm" required value="<?= $user['username'] ?? '' ?>" placeholder="Contoh: admin123">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Role</label>
                            <div class="relative">
                                <select name="role" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm appearance-none bg-none" required>
                                    <option value="">Pilih Role</option>
                                    <option value="admin" <?= ($user['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="marketing" <?= ($user['role'] ?? '') == 'marketing' ? 'selected' : '' ?>>Marketing Staff</option>
                                    <option value="manager" <?= ($user['role'] ?? '') == 'manager' ? 'selected' : '' ?>>Marketing Manager</option>
                                    <option value="academic" <?= ($user['role'] ?? '') == 'academic' ? 'selected' : '' ?>>Academic</option>
                                    <option value="student" <?= ($user['role'] ?? '') == 'student' ? 'selected' : '' ?>>Student</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <ion-icon name="chevron-down-outline"></ion-icon>
                                </div>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="full_name" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm" required value="<?= $user['full_name'] ?? '' ?>" placeholder="Nama lengkap user">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Password 
                                <?php if ($isEdit): ?>
                                    <span class="text-xs font-normal text-slate-400 ml-1">(Kosongkan jika tidak ingin mengubah password)</span>
                                <?php endif; ?>
                            </label>
                            <input type="password" name="password" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm" <?= $isEdit ? '' : 'required' ?> placeholder="••••••••">
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="users.php" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors">Batal</a>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-bold shadow-lg shadow-primary/30 hover:bg-blue-600 hover:scale-[1.02] transition-all">Simpan Data</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
