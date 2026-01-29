<?php
require_once '../../config/functions.php';
requireRole('marketing');
$pageTitle = 'Marketing Dashboard';
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
            <span class="font-display font-bold text-lg text-slate-800">Marketing</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'M', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-display font-bold text-slate-900 mb-2">Operasional Marketing</h1>
                <p class="text-slate-500">Selamat bekerja, <span class="font-semibold text-primary"><?= $_SESSION['full_name'] ?></span> 👋</p>
            </div>
            <div class="text-sm text-slate-500 bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-100 flex items-center gap-2">
                <ion-icon name="calendar-outline"></ion-icon>
                <?= date('l, d F Y') ?>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h3 class="font-display font-bold text-lg text-slate-900 mb-6 flex items-center gap-2">
                        <ion-icon name="flash-outline" class="text-primary"></ion-icon> Quick Actions
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="input_leads.php" class="group flex items-center gap-4 p-4 rounded-xl border border-slate-100 hover:border-primary/50 hover:bg-primary/5 transition-all">
                            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                <ion-icon name="person-add-outline"></ion-icon>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 group-hover:text-primary transition-colors">Input Calon Mahasiswa</h4>
                                <p class="text-xs text-slate-500 mt-1">Tambah data leads baru</p>
                            </div>
                            <ion-icon name="arrow-forward-outline" class="ml-auto text-slate-300 group-hover:text-primary transition-colors"></ion-icon>
                        </a>
                        
                        <a href="#" onclick="alert('Fitur segera hadir!')" class="group flex items-center gap-4 p-4 rounded-xl border border-slate-100 hover:border-orange-200 hover:bg-orange-50 transition-all">
                            <div class="w-12 h-12 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                <ion-icon name="chatbubbles-outline"></ion-icon>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 group-hover:text-orange-600 transition-colors">Follow Up</h4>
                                <p class="text-xs text-slate-500 mt-1">Catat aktivitas follow up</p>
                            </div>
                            <ion-icon name="arrow-forward-outline" class="ml-auto text-slate-300 group-hover:text-orange-600 transition-colors"></ion-icon>
                        </a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 h-full">
                    <h3 class="font-display font-bold text-lg text-slate-900 mb-6 flex items-center gap-2">
                        <ion-icon name="time-outline" class="text-slate-400"></ion-icon> Activity Log
                    </h3>
                    
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                            <ion-icon name="file-tray-outline" class="text-3xl text-slate-300"></ion-icon>
                        </div>
                        <p class="text-slate-500 font-medium">Belum ada aktivitas hari ini</p>
                        <p class="text-xs text-slate-400 mt-1">Aktivitas terbaru akan muncul di sini</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
