<?php
require_once '../../config/functions.php';
requireRole('admin');
$pageTitle = 'Admin Dashboard';
$active = 'dashboard';
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
    
    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Admin Dashboard</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto">
        <div class="max-w-7xl mx-auto p-4 lg:p-8 animate-fade-in">
            
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-display font-bold text-slate-900 mb-2">Dashboard</h1>
                    <p class="text-slate-500">Selamat datang kembali, <span class="font-semibold text-primary"><?= $_SESSION['full_name'] ?></span> 👋</p>
                </div>
                <div class="text-sm text-slate-500 bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-100 flex items-center gap-2">
                    <ion-icon name="calendar-outline"></ion-icon>
                    <?= date('l, d F Y') ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            <ion-icon name="people"></ion-icon>
                        </div>
                        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full flex items-center gap-1">
                            <ion-icon name="trending-up"></ion-icon> +12%
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Total Users</p>
                        <h3 class="text-3xl font-display font-bold text-slate-900">24</h3>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            <ion-icon name="school"></ion-icon>
                        </div>
                        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">Active</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Mahasiswa Aktif</p>
                        <h3 class="text-3xl font-display font-bold text-slate-900">142</h3>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            <ion-icon name="library"></ion-icon>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Program Studi</p>
                        <h3 class="text-3xl font-display font-bold text-slate-900">5</h3>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            <ion-icon name="cash"></ion-icon>
                        </div>
                        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">Lunas</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Revenue (Bulan Ini)</p>
                        <h3 class="text-3xl font-display font-bold text-slate-900">Rp 45jt</h3>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h3 class="font-display font-bold text-lg text-slate-900 mb-6 flex items-center gap-2">
                        <ion-icon name="flash-outline" class="text-primary"></ion-icon> Quick Actions
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="users.php?action=add" class="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-100 hover:border-primary/50 hover:bg-primary/5 transition-all text-slate-600 hover:text-primary group">
                            <div class="w-10 h-10 rounded-full bg-slate-50 group-hover:bg-primary group-hover:text-white flex items-center justify-center mb-2 transition-colors">
                                <ion-icon name="person-add-outline"></ion-icon>
                            </div>
                            <span class="text-sm font-medium">Add User</span>
                        </a>
                        <a href="config.php" class="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-100 hover:border-primary/50 hover:bg-primary/5 transition-all text-slate-600 hover:text-primary group">
                            <div class="w-10 h-10 rounded-full bg-slate-50 group-hover:bg-primary group-hover:text-white flex items-center justify-center mb-2 transition-colors">
                                <ion-icon name="settings-outline"></ion-icon>
                            </div>
                            <span class="text-sm font-medium">Settings</span>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:col-span-2">
                    <h3 class="font-display font-bold text-lg text-slate-900 mb-6 flex items-center gap-2">
                        <ion-icon name="server-outline" class="text-primary"></ion-icon> System Status
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                                <span class="font-medium text-slate-700">Database Connection</span>
                            </div>
                            <span class="text-sm text-green-600 font-bold">Connected</span>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                                <span class="font-medium text-slate-700">Server Time</span>
                            </div>
                            <span class="text-sm text-slate-500 font-mono"><?= date('H:i:s T') ?></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
