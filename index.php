<?php
require_once 'config/database.php';
require_once 'config/functions.php';

$recent_news = [
    ['title' => 'Tegaskan Mutu Pendidikan Vokasi, Program Studi D3', 'date' => date('d M Y'), 'category' => 'Admission', 'img' => './assets/1.jpeg', 'desc' => 'Daftarkan dirimu sekarang dan dapatkan potongan biaya pendaftaran gelombang pertama.'],
    ['title' => 'Perkuat Resiliensi Pendidik, Politeknik LP3I Jakarta', 'date' => date('d M Y', strtotime('-2 days')), 'category' => 'Event', 'img' => './assets/2.jpeg', 'desc' => 'Menyadari peran krusial Guru Bimbingan dan Konseling (BK) dalam membentuk ketangguhan mental siswa.'],
    ['title' => 'Kunjungan Industri Google', 'date' => date('d M Y', strtotime('-1 week')), 'category' => 'Activity', 'img' => './assets/3.jpg', 'desc' => 'Mahasiswa Prodi TI melakukan kunjungan belajar langsung ke kantor Google Indonesia.'],
];
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
<meta property="og:title" content="LP3I Jakarta - Cepat Kerja Tepat">
<meta property="og:description" content="Aplikasi CRUD Akademik berbasis web untuk manajemen mahasiswa, mata kuliah, dan nilai.">
<meta property="og:image" content="./assets/banner.png">
<meta property="og:url" content="https://mectov.diskon.com/crud_akademik/">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LP3I Jakarta - Cepat Kerja Tepat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#3b82f6',
                        accent: '#f59e0b',
                        dark: '#0f172a',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, .font-display { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.3); }
        .text-gradient { background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .blob { position: absolute; filter: blur(40px); z-index: -1; opacity: 0.5; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-primary/20 selection:text-primary">

    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="blob bg-blue-400 w-96 h-96 rounded-full top-0 left-0 -translate-x-1/2 -translate-y-1/2 opacity-20"></div>
        <div class="blob bg-purple-400 w-96 h-96 rounded-full bottom-0 right-0 translate-x-1/2 translate-y-1/2 opacity-20"></div>
    </div>

    <nav class="fixed w-full z-50 glass transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center gap-3 cursor-pointer">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">L</div>
                    <span class="font-display font-bold text-2xl tracking-tight text-slate-800">LP3I<span class="text-primary">JAKARTA</span></span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#beranda" class="text-sm font-medium text-slate-600 hover:text-primary transition-colors">Beranda</a>
                    <a href="#tentang" class="text-sm font-medium text-slate-600 hover:text-primary transition-colors">Tentang Kami</a>
                    <a href="#prodi" class="text-sm font-medium text-slate-600 hover:text-primary transition-colors">Prodi</a>
                    <a href="#berita" class="text-sm font-medium text-slate-600 hover:text-primary transition-colors">Berita</a>
                    <a href="#contact" class="text-sm font-medium text-slate-600 hover:text-primary transition-colors">Contact</a>
                    <a href="login.php" class="px-6 py-2.5 rounded-full bg-slate-900 text-white text-sm font-semibold hover:bg-primary hover:shadow-lg hover:shadow-primary/30 transition-all duration-300 transform hover:-translate-y-0.5">
                        Login Portal
                    </a>
                </div>
                <div class="md:hidden">
                    <button class="text-slate-600 focus:outline-none">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <section id="beranda" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div class="text-center lg:text-left animate-fade-in-up">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 border border-blue-100 text-primary text-xs font-bold uppercase tracking-wide mb-6">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        Penerimaan Mahasiswa Baru 2026
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-display font-extrabold leading-tight mb-6 text-slate-900">
                        Siap Kerja, <br>
                        <span class="text-gradient">Tepat Cepat!</span>
                    </h1>
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Bergabunglah dengan ekosistem pendidikan vokasi modern. Kami mencetak profesional muda yang siap berkompetisi di industri global dengan skill yang relevan.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="login.php" class="px-8 py-4 rounded-full bg-primary text-white font-bold text-lg shadow-xl shadow-primary/30 hover:bg-blue-700 hover:scale-105 transition-all duration-300 text-center">
                            Daftar Sekarang
                        </a>
                        <a href="#prodi" class="px-8 py-4 rounded-full bg-white text-slate-700 border border-slate-200 font-bold text-lg hover:border-primary hover:text-primary transition-all duration-300 flex items-center justify-center gap-2 group">
                            <span class="group-hover:translate-x-1 transition-transform">Jelajahi Prodi</span> →
                        </a>
                    </div>
                    
                    <div class="mt-12 flex items-center justify-center lg:justify-start gap-8 opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
                        <div class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Trusted by Industry Leaders</div>
                    </div>
                </div>
                
                <div class="relative lg:h-[600px] flex items-center justify-center">
                    <div class="relative w-full aspect-[4/5] rounded-[2.5rem] overflow-hidden shadow-2xl shadow-blue-900/20 group">
                        <img src="./assets/R.jpg" alt="Students" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-8 left-8 right-8 text-white">
                            <div class="font-display font-bold text-2xl mb-2">Campus Life</div>
                            <p class="text-white/80 text-sm">Pengalaman belajar yang kolaboratif dan menyenangkan.</p>
                        </div>
                    </div>
                    
                    <div class="absolute -left-12 bottom-24 bg-white p-4 rounded-2xl shadow-xl animate-bounce duration-[3000ms] hidden lg:block">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <div class="font-bold text-slate-800">98% Employed</div>
                                <div class="text-xs text-slate-500">within 6 months</div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -right-8 top-32 bg-white p-4 rounded-2xl shadow-xl animate-pulse hidden lg:block">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                            <div>
                                <div class="font-bold text-slate-800">Active Learning</div>
                                <div class="text-xs text-slate-500">Project Based</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="p-4">
                    <div class="text-4xl font-display font-bold text-primary mb-1">34+</div>
                    <div class="text-sm text-slate-500 font-medium">Tahun Pengalaman</div>
                </div>
                <div class="p-4">
                    <div class="text-4xl font-display font-bold text-primary mb-1">50+</div>
                    <div class="text-sm text-slate-500 font-medium">Cabang Kampus</div>
                </div>
                <div class="p-4">
                    <div class="text-4xl font-display font-bold text-primary mb-1">1000+</div>
                    <div class="text-sm text-slate-500 font-medium">Mitra Perusahaan</div>
                </div>
                <div class="p-4">
                    <div class="text-4xl font-display font-bold text-primary mb-1">95%</div>
                    <div class="text-sm text-slate-500 font-medium">Terserap Kerja</div>
                </div>
            </div>
        </div>
    </section>

    <section id="tentang" class="py-24 bg-slate-50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-display font-bold text-slate-900 mb-4">Kenapa Memilih LP3I?</h2>
                <p class="text-slate-600 text-lg">Kami tidak hanya memberikan gelar, tapi juga memastikan masa depan karirmu cerah dengan kompetensi yang dibutuhkan industri.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border border-slate-100">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-primary mb-6 text-2xl">
                        🎓
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Kurikulum Vokasi</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Pembelajaran 70% praktek dan 30% teori memastikan skill kamu terasah dengan baik.
                    </p>
                </div>
                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border border-slate-100 scale-105 z-10 relative ring-4 ring-primary/5">
                    <div class="absolute top-0 right-0 bg-accent text-white text-xs font-bold px-3 py-1 rounded-bl-xl rounded-tr-2xl">UNGGULAN</div>
                    <div class="w-14 h-14 rounded-2xl bg-orange-100 flex items-center justify-center text-orange-600 mb-6 text-2xl">
                        🤝
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Penempatan Kerja</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Kami menjamin bantuan penempatan kerja di ribuan perusahaan relasi LP3I sebelum wisuda.
                    </p>
                </div>
                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border border-slate-100">
                    <div class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center text-purple-600 mb-6 text-2xl">
                        🚀
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Fasilitas Modern</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Lab komputer spek tinggi, coworking space, dan lingkungan belajar digital yang kondusif.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="berita" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl md:text-4xl font-display font-bold text-slate-900 mb-2">Berita & Event</h2>
                    <p class="text-slate-600">Update terbaru seputar kegiatan kampus</p>
                </div>
                <a href="#" class="hidden md:flex items-center gap-2 text-primary font-bold hover:gap-3 transition-all">Lihat Semua →</a>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($recent_news as $news): ?>
                <div class="group cursor-pointer">
                    <div class="relative overflow-hidden rounded-2xl mb-4 aspect-[4/3]">
                        <img src="<?= $news['img'] ?>" alt="<?= $news['title'] ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-slate-800 shadow-sm">
                            <?= $news['category'] ?>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                        <span>🗓️ <?= $news['date'] ?></span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary transition-colors line-clamp-2"><?= $news['title'] ?></h3>
                    <p class="text-slate-600 text-sm line-clamp-2"><?= $news['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-8 text-center md:hidden">
                <a href="#" class="text-primary font-bold">Lihat Semua Berita →</a>
            </div>
        </div>
    </section>

    <section class="py-20 bg-dark relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary/30 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        
        <div class="max-w-5xl mx-auto px-4 relative z-10 text-center">
            <h2 class="text-3xl md:text-5xl font-display font-bold text-white mb-6">Siap Memulai Masa Depanmu?</h2>
            <p class="text-slate-300 text-lg mb-10 max-w-2xl mx-auto">Jangan tunda lagi. Jadilah bagian dari ribuan alumni sukses LP3I yang kini berkarir di perusahaan top nasional.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#" class="px-8 py-4 rounded-full bg-white text-dark font-bold text-lg hover:bg-slate-100 transition-colors">Daftar Online</a>
                <a href="#contact" class="px-8 py-4 rounded-full bg-transparent border border-white/30 text-white font-bold text-lg hover:bg-white/10 transition-colors">Hubungi Kami</a>
            </div>
        </div>
    </section>

    <footer id="contact" class="bg-slate-900 text-white pt-20 pb-10 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center font-bold text-sm">L</div>
                        <span class="font-display font-bold text-2xl">LP3I JAKARTA</span>
                    </div>
                    <p class="text-slate-400 leading-relaxed max-w-sm mb-8">
                        Kampus vokasi pencetak SDM profesional siap kerja. Terakreditasi dan memiliki jaringan kerjasama industri yang luas di seluruh Indonesia.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors text-white">IG</a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors text-white">FB</a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors text-white">YT</a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-6">Link Cepat</h4>
                    <ul class="space-y-4 text-slate-400">
                        <li><a href="#beranda" class="hover:text-primary transition-colors">Beranda</a></li>
                        <li><a href="#tentang" class="hover:text-primary transition-colors">Tentang Kami</a></li>
                        <li><a href="#prodi" class="hover:text-primary transition-colors">Program Studi</a></li>
                        <li><a href="login.php" class="hover:text-primary transition-colors">Login Portal</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-6">Kontak</h4>
                    <ul class="space-y-4 text-slate-400">
                        <li class="flex items-start gap-3">
                            <span class="mt-1">📍</span>
                            <span>Jl. Kramat Raya No. 7-9<br>Jakarta Pusat, 10450</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span>📞</span>
                            <span>(021) 314-5555</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span>✉️</span>
                            <span>info@lp3i.ac.id</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-800 text-center text-slate-500 text-sm">
                &copy; <?= date('Y') ?> LP3I Jakarta. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
