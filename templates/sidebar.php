<?php
require_once __DIR__ . '/../config/routes.php';

$role = $_SESSION['role'] ?? 'guest';
$navItems = getNavigation($role);
?>
<div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>

<aside id="sidebar" class="sidebar-expanded fixed lg:static inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out flex flex-col h-full shadow-2xl lg:shadow-none border-r border-slate-800" data-collapsed="false">
    
    <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800 bg-slate-950">
        <a href="<?= getDashboardUrl($role) ?>" class="flex items-center group overflow-hidden">
            <span class="sidebar-text font-display font-bold text-xl tracking-wide text-slate-100 group-hover:text-white transition-colors whitespace-nowrap">LP3I<span class="text-primary ml-0.5">System</span></span>
        </a>
        <button id="collapse-btn" onclick="collapseSidebar()" class="hidden lg:flex w-8 h-8 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all" title="Toggle Sidebar">
            <ion-icon name="chevron-back-outline" class="text-lg collapse-icon transition-transform duration-300"></ion-icon>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <?php foreach ($navItems as $item): 
            $isActive = isActivePage($item['key'], $active ?? '');
        ?>
            <a href="<?= $item['url'] ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group <?= $isActive ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>" title="<?= $item['label'] ?>">
                <ion-icon name="<?= $item['icon'] ?>" class="text-lg shrink-0 <?= $isActive ? 'text-white' : 'text-slate-500 group-hover:text-white transition-colors' ?>"></ion-icon>
                <span class="sidebar-text whitespace-nowrap"><?= $item['label'] ?></span>
                <?php if ($isActive): ?>
                    <span class="sidebar-text ml-auto w-1.5 h-1.5 rounded-full bg-white/50"></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="p-4 border-t border-slate-800 bg-slate-950">
        <div class="flex items-center gap-3 mb-4 px-2">
            <div class="w-8 h-8 shrink-0 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 font-bold border border-slate-700">
                <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="sidebar-text flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate"><?= $_SESSION['full_name'] ?? 'User' ?></p>
                <p class="text-xs text-slate-500 truncate capitalize"><?= $_SESSION['role'] ?? 'Guest' ?></p>
            </div>
        </div>
        
        <button id="darkModeToggle" onclick="toggleDarkMode()" class="flex items-center justify-center gap-2 w-full px-4 py-2 mb-2 rounded-lg border border-slate-700 text-slate-400 text-xs font-bold hover:bg-slate-800 hover:text-white hover:border-slate-600 transition-all duration-200" title="Toggle Dark Mode">
            <ion-icon id="darkModeIcon" name="moon-outline" class="text-base shrink-0"></ion-icon>
            <span id="darkModeText" class="sidebar-text">DARK MODE</span>
        </button>
        
        <a href="/crud_akademik/src/auth/logout.php" class="flex items-center justify-center gap-2 w-full px-4 py-2 rounded-lg border border-slate-700 text-slate-400 text-xs font-bold hover:bg-red-500/10 hover:text-red-500 hover:border-red-500/50 transition-all duration-200" title="Logout">
            <ion-icon name="log-out-outline" class="text-base shrink-0"></ion-icon>
            <span class="sidebar-text">LOGOUT</span>
        </a>
    </div>
</aside>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const isMobile = window.innerWidth < 1024;
        
        if (isMobile) {
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }
    }

    function collapseSidebar() {
        const sidebar = document.getElementById('sidebar');
        const isCollapsed = sidebar.dataset.collapsed === 'true';
        const collapseIcon = document.querySelector('.collapse-icon');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        
        if (isCollapsed) {
            // Expand sidebar
            sidebar.classList.remove('w-20');
            sidebar.classList.add('w-64');
            sidebar.classList.remove('sidebar-collapsed');
            sidebar.classList.add('sidebar-expanded');
            collapseIcon.style.transform = 'rotate(0deg)';
            sidebarTexts.forEach(el => {
                el.classList.remove('opacity-0', 'w-0', 'overflow-hidden');
            });
            sidebar.dataset.collapsed = 'false';
            localStorage.setItem('sidebarCollapsed', 'false');
        } else {
            // Collapse sidebar
            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-20');
            sidebar.classList.remove('sidebar-expanded');
            sidebar.classList.add('sidebar-collapsed');
            collapseIcon.style.transform = 'rotate(180deg)';
            sidebarTexts.forEach(el => {
                el.classList.add('opacity-0', 'w-0', 'overflow-hidden');
            });
            sidebar.dataset.collapsed = 'true';
            localStorage.setItem('sidebarCollapsed', 'true');
        }
    }

    // Restore sidebar state on page load
    document.addEventListener('DOMContentLoaded', function() {
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true' && window.innerWidth >= 1024) {
            collapseSidebar();
        }
        
        // Update dark mode UI on load
        updateDarkModeUI();
    });

    // Dark mode toggle with Telegram-style animation
    function toggleDarkMode(event) {
        const html = document.documentElement;
        const button = document.getElementById('darkModeToggle');
        const willBeDark = !html.classList.contains('dark');
        
        // Get button position for animation origin
        const rect = button.getBoundingClientRect();
        const x = rect.left + rect.width / 2;
        const y = rect.top + rect.height / 2;
        
        // Calculate max radius needed to cover entire screen
        const maxRadius = Math.max(
            Math.hypot(x, y),
            Math.hypot(window.innerWidth - x, y),
            Math.hypot(x, window.innerHeight - y),
            Math.hypot(window.innerWidth - x, window.innerHeight - y)
        );
        
        // Create overlay element
        const overlay = document.createElement('div');
        
        if (willBeDark) {
            // Going to dark: expand from button to full screen
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                pointer-events: none;
                z-index: 99999;
                background: #0f172a;
                clip-path: circle(0px at ${x}px ${y}px);
                transition: clip-path 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            `;
            document.body.appendChild(overlay);
            
            requestAnimationFrame(() => {
                overlay.style.clipPath = `circle(${maxRadius}px at ${x}px ${y}px)`;
            });
            
            setTimeout(() => {
                html.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
                updateDarkModeUI();
            }, 300);
            
            setTimeout(() => { overlay.remove(); }, 650);
            
        } else {
            // Going to light: Apply light mode FIRST, then dark overlay shrinks to reveal it
            html.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
            updateDarkModeUI();
            
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                pointer-events: none;
                z-index: 99999;
                background: #0f172a;
                clip-path: circle(${maxRadius}px at ${x}px ${y}px);
                transition: clip-path 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            `;
            document.body.appendChild(overlay);
            
            requestAnimationFrame(() => {
                overlay.style.clipPath = `circle(0px at ${x}px ${y}px)`;
            });
            
            setTimeout(() => { overlay.remove(); }, 650);
        }
    }

    function updateDarkModeUI() {
        const isDark = document.documentElement.classList.contains('dark');
        const icon = document.getElementById('darkModeIcon');
        const text = document.getElementById('darkModeText');
        
        if (icon) {
            icon.setAttribute('name', isDark ? 'sunny-outline' : 'moon-outline');
            // Add rotation animation to icon
            icon.style.transition = 'transform 0.3s ease';
            icon.style.transform = 'rotate(360deg)';
            setTimeout(() => { icon.style.transform = ''; }, 300);
        }
        if (text) {
            text.textContent = isDark ? 'LIGHT MODE' : 'DARK MODE';
        }
    }
</script>
