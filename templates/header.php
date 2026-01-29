<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?> - LP3I System</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <style type="text/tailwindcss">
        @theme {
            /* Colors */
            --color-primary: #2563eb;
            --color-secondary: #3b82f6;
            --color-accent: #f59e0b;
            --color-dark: #0f172a;
            --color-success: #10b981;
            --color-danger: #ef4444;
            
            /* Dark Mode Colors */
            --color-dark-bg: #0f172a;
            --color-dark-card: #1e293b;
            --color-dark-border: #334155;
            
            /* Fonts */
            --font-sans: 'Inter', sans-serif;
            --font-display: 'Outfit', sans-serif;
        }
        
        /* Enable class-based dark mode */
        @custom-variant dark (&:where(.dark, .dark *));
        
        /* Base Styles */
        body { font-family: var(--font-sans); }
        h1, h2, h3, h4, .font-display { font-family: var(--font-display); }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Dark mode scrollbar */
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #64748b; }
        
        /* Fade In Animation */
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* ========== GLOBAL DARK MODE OVERRIDES ========== */
        /* Main content area */
        .dark main { background-color: var(--color-dark-bg) !important; }
        
        /* Cards and white backgrounds */
        .dark .bg-white { background-color: var(--color-dark-card) !important; }
        .dark .bg-slate-50 { background-color: var(--color-dark-bg) !important; }
        .dark .bg-slate-100 { background-color: #1e293b !important; }
        
        /* Borders */
        .dark .border-slate-100,
        .dark .border-slate-200 { border-color: var(--color-dark-border) !important; }
        
        /* Text colors */
        .dark .text-slate-900 { color: #f1f5f9 !important; }
        .dark .text-slate-800 { color: #e2e8f0 !important; }
        .dark .text-slate-700 { color: #cbd5e1 !important; }
        .dark .text-slate-600 { color: #94a3b8 !important; }
        .dark .text-slate-500 { color: #64748b !important; }
        
        /* Form inputs */
        .dark input, .dark select, .dark textarea {
            background-color: #1e293b !important;
            border-color: var(--color-dark-border) !important;
            color: #e2e8f0 !important;
        }
        .dark input::placeholder { color: #64748b !important; }
        
        /* Hover states */
        .dark .hover\:bg-slate-50:hover { background-color: #334155 !important; }
        .dark .hover\:bg-slate-100:hover { background-color: #334155 !important; }
        
        /* Mobile header */
        .dark header { background-color: var(--color-dark-card) !important; border-color: var(--color-dark-border) !important; }
    </style>
    
    <!-- Dark Mode Script (runs early to prevent flash) -->
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;700;800&display=swap" rel="stylesheet">
    
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body class="h-full bg-slate-50 dark:bg-dark-bg text-slate-900 dark:text-slate-100 transition-colors duration-300">
    <div class="flex h-screen overflow-hidden">

