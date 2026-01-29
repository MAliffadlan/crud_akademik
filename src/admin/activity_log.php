<?php
require_once '../../config/functions.php';
require_once '../../config/activity_log.php';
requireRole('admin');

$pageTitle = 'Activity Log';
$active = 'activity_log';

$filters = [];
if (!empty($_GET['user_id'])) $filters['user_id'] = (int)$_GET['user_id'];
if (!empty($_GET['action'])) $filters['action'] = clean($_GET['action']);
if (!empty($_GET['date_from'])) $filters['date_from'] = clean($_GET['date_from']);
if (!empty($_GET['date_to'])) $filters['date_to'] = clean($_GET['date_to']);

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$logs = getActivityLogs($filters, $limit, $offset);
$totalLogs = countActivityLogs($filters);
$totalPages = ceil($totalLogs / $limit);

$actions = getUniqueActions();

$usersQuery = $conn->query("SELECT id, full_name, username FROM users ORDER BY full_name");
$users = $usersQuery->fetch_all(MYSQLI_ASSOC);
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
    
    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">Activity Log</span>
        </div>
    </header>

    <div class="flex-1 overflow-auto">
        <div class="max-w-7xl mx-auto p-4 lg:p-8 animate-fade-in">
            
            <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-display font-bold text-slate-900 mb-2">Activity Log</h1>
                    <p class="text-slate-500">Riwayat aktivitas semua user dalam sistem</p>
                </div>
                <div class="text-sm text-slate-500 bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-100 flex items-center gap-2">
                    <ion-icon name="time-outline"></ion-icon>
                    Total: <span class="font-bold text-slate-700"><?= number_format($totalLogs) ?></span> aktivitas
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">User</label>
                        <select name="user_id" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="">Semua User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= ($filters['user_id'] ?? '') == $user['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Action</label>
                        <select name="action" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="">Semua Action</option>
                            <?php foreach ($actions as $action): ?>
                                <option value="<?= $action ?>" <?= ($filters['action'] ?? '') == $action ? 'selected' : '' ?>>
                                    <?= ucfirst(str_replace('_', ' ', $action)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Dari Tanggal</label>
                        <input type="date" name="date_from" value="<?= $filters['date_from'] ?? '' ?>" 
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="<?= $filters['date_to'] ?? '' ?>" 
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition flex items-center justify-center gap-2">
                            <ion-icon name="search-outline"></ion-icon>
                            Filter
                        </button>
                        <a href="activity_log.php" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-200 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                                        <ion-icon name="document-outline" class="text-4xl mb-2 block mx-auto"></ion-icon>
                                        Tidak ada data aktivitas
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm text-slate-900"><?= date('d M Y', strtotime($log['created_at'])) ?></div>
                                            <div class="text-xs text-slate-400"><?= date('H:i:s', strtotime($log['created_at'])) ?></div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs">
                                                    <?= strtoupper(substr($log['user_name'] ?? 'U', 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-slate-900"><?= htmlspecialchars($log['user_name'] ?? 'Unknown') ?></div>
                                                    <div class="text-xs text-slate-400">@<?= htmlspecialchars($log['username'] ?? 'unknown') ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium <?= getActionBadgeClass($log['action']) ?>">
                                                <ion-icon name="<?= getActionIcon($log['action']) ?>"></ion-icon>
                                                <?= ucfirst(str_replace('_', ' ', $log['action'])) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-600 max-w-xs truncate">
                                            <?= htmlspecialchars($log['description'] ?? '-') ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-500 font-mono">
                                            <?= htmlspecialchars($log['ip_address'] ?? '-') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($totalPages > 1): ?>
                    <div class="px-4 py-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-slate-500">
                            Menampilkan <?= $offset + 1 ?>-<?= min($offset + $limit, $totalLogs) ?> dari <?= $totalLogs ?> aktivitas
                        </div>
                        <div class="flex gap-1">
                            <?php 
                            $queryParams = $_GET;
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            ?>
                            
                            <?php if ($page > 1): ?>
                                <?php $queryParams['page'] = $page - 1; ?>
                                <a href="?<?= http_build_query($queryParams) ?>" class="px-3 py-1 rounded border border-slate-200 text-sm text-slate-600 hover:bg-slate-50">
                                    <ion-icon name="chevron-back-outline"></ion-icon>
                                </a>
                            <?php endif; ?>
                            
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <?php $queryParams['page'] = $i; ?>
                                <a href="?<?= http_build_query($queryParams) ?>" 
                                   class="px-3 py-1 rounded text-sm <?= $i == $page ? 'bg-primary text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <?php $queryParams['page'] = $page + 1; ?>
                                <a href="?<?= http_build_query($queryParams) ?>" class="px-3 py-1 rounded border border-slate-200 text-sm text-slate-600 hover:bg-slate-50">
                                    <ion-icon name="chevron-forward-outline"></ion-icon>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
