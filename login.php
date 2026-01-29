<?php
require_once 'config/database.php';
require_once 'config/functions.php';
redirectIfLoggedIn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi LP3I</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#3b82f6',
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
        .login-bg {
            background-image: url('https://images.unsplash.com/photo-1497294815431-9365093b7331?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="h-screen font-sans bg-slate-50 overflow-hidden">

    <div class="flex h-full w-full">
        <div class="hidden lg:flex w-1/2 login-bg relative flex-col justify-between p-12">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 to-slate-900/80 backdrop-blur-[2px]"></div>
            
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-primary font-bold text-xl shadow-lg">L</div>
                <span class="font-display font-bold text-2xl text-white tracking-wide">LP3I JAKARTA</span>
            </div>

            <div class="relative z-10 max-w-lg">
                <h1 class="text-5xl font-display font-bold text-white mb-6 leading-tight">Membangun Profesional Muda</h1>
                <p class="text-blue-100 text-lg leading-relaxed">Bergabunglah dengan ekosistem pendidikan kami yang terintegrasi. Kelola data akademik, marketing, dan administrasi dalam satu platform.</p>
            </div>

            <div class="relative z-10 text-sm text-blue-200">
                &copy; <?= date('Y') ?> LP3I Jakarta. All rights reserved.
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white relative">

            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50 rounded-bl-[100px] -z-0"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-slate-50 rounded-tr-[100px] -z-0"></div>

            <div class="w-full max-w-md space-y-8 relative z-10">
                <div class="text-center">
                    <h2 class="text-3xl font-display font-bold text-slate-900">Selamat Datang</h2>
                    <p class="mt-2 text-slate-500">Silakan login untuk mengakses akun Anda</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md animate-pulse">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700 font-medium"><?= htmlspecialchars($_GET['error']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form class="mt-8 space-y-6" action="src/auth/login_process.php" method="POST">
                    <div class="space-y-5">
                        <div>
                            <label for="username" class="block text-sm font-medium leading-6 text-slate-900">Username</label>
                            <div class="mt-2 text-align: left;">
                                <input id="username" name="username" type="text" autocomplete="username" required 
                                    class="block w-full rounded-xl border-0 py-3 px-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition-all bg-slate-50 focus:bg-white"
                                    placeholder="Masukkan username">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between">
                                <label for="password" class="block text-sm font-medium leading-6 text-slate-900">Password</label>
                            </div>
                            <div class="mt-2">
                                <input id="password" name="password" type="password" autocomplete="current-password" required 
                                    class="block w-full rounded-xl border-0 py-3 px-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition-all bg-slate-50 focus:bg-white"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-xl bg-primary px-4 py-3.5 text-sm font-bold leading-6 text-white shadow-lg hover:bg-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition-all hover:scale-[1.02] active:scale-100">
                            Masuk ke Sistem
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm text-slate-500">
                        Kembali ke <a href="index.php" class="font-semibold text-primary hover:text-blue-600 transition-colors">Beranda</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
