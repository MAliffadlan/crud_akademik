<?php
require_once '../../config/functions.php';
requireRole('marketing');
$pageTitle = 'Input Data Calon Mahasiswa';
$active = 'input';
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Input Data</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'M', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        <div class="max-w-3xl mx-auto">
            
            <div class="flex items-center gap-4 mb-8">
                <a href="dashboard_staff.php" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-primary hover:border-primary transition-all shadow-sm">
                    <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                </a>
                <div>
                    <h1 class="text-2xl font-display font-bold text-slate-900">Input Calon Mahasiswa</h1>
                    <p class="text-slate-500">Masukkan data lengkap calon mahasiswa baru.</p>
                </div>
            </div>

            <form action="process.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="action" value="add_lead">
                
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
                    <h3 class="font-display font-bold text-lg text-slate-900 mb-6 flex items-center gap-2 pb-4 border-b border-slate-50">
                        <ion-icon name="person-outline" class="text-primary"></ion-icon> Data Pribadi
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="full_name" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm" required placeholder="Nama lengkap sesuai ijazah">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                            <input type="email" name="email" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm" required placeholder="email@example.com">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">No. Telepon / WA</label>
                            <input type="text" name="phone" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm" required placeholder="08xxxxxxxxxx">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Asal Sekolah</label>
                            <input type="text" name="school_origin" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm" required placeholder="Nama asal sekolah">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Pilihan Jurusan</label>
                            <div class="relative">
                                <select name="program_choice" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm appearance-none bg-none" required>
                                    <option value="">Pilih Jurusan</option>
                                    <option value="Teknologi Informasi">Teknologi Informasi</option>
                                    <option value="Administrasi Bisnis">Administrasi Bisnis</option>
                                    <option value="Akuntansi">Akuntansi</option>
                                    <option value="Hubungan Masyarakat">Hubungan Masyarakat</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <ion-icon name="chevron-down-outline"></ion-icon>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
                    <h3 class="font-display font-bold text-lg text-slate-900 mb-6 flex items-center gap-2 pb-4 border-b border-slate-50">
                        <ion-icon name="document-text-outline" class="text-primary"></ion-icon> Upload Dokumen & Pembayaran
                    </h3>
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Foto KTP / Kartu Pelajar</label>
                                <input type="file" name="doc_ktp" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all" accept="image/*,.pdf">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Ijazah / SKL</label>
                                <input type="file" name="doc_ijazah" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all" accept="image/*,.pdf">
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 animate-fade-in">
                            <h4 class="font-bold text-slate-800 text-sm mb-4">Informasi Pembayaran</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Bukti Pembayaran</label>
                                    <input type="file" name="doc_payment" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-all" accept="image/*,.pdf">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Jumlah Pembayaran (Rp)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-slate-500 text-sm font-bold">Rp</span>
                                        </div>
                                        <input type="number" name="payment_amount" class="w-full pl-10 rounded-xl border-slate-200 focus:border-primary focus:ring-primary/20 transition-all text-sm" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="dashboard_staff.php" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors">Batal</a>
                    <button type="submit" class="px-8 py-2.5 rounded-xl bg-primary text-white text-sm font-bold shadow-lg shadow-primary/30 hover:bg-blue-600 hover:scale-[1.02] transition-all flex items-center gap-2">
                        <ion-icon name="save-outline" class="text-lg"></ion-icon>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
