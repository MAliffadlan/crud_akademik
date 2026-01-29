<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';
requireRole('student');
$pageTitle = 'KRS Online';
$active = 'krs';

$user_id = $_SESSION['user_id'];
$student_result = $conn->query("SELECT id FROM students WHERE user_id = $user_id");
$student_row = $student_result->fetch_assoc();
$student_id = $student_row['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['courses'])) {
    $semester = (int)$_POST['semester'];
    
    $conn->query("DELETE FROM krs WHERE student_id = $student_id AND semester = $semester");
    
    foreach ($_POST['courses'] as $course_id) {
        $course_id = (int)$course_id;
        $stmt = $conn->prepare("INSERT INTO krs (student_id, course_id, semester) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $student_id, $course_id, $semester);
        $stmt->execute();
    }
    
    header("Location: krs.php?msg=saved&semester=$semester");
    exit;
}

$current_semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 1;

$courses = $conn->query("SELECT * FROM courses ORDER BY code");

$krs_result = $conn->query("SELECT course_id FROM krs WHERE student_id = $student_id AND semester = $current_semester");
$selected_courses = [];
while ($row = $krs_result->fetch_assoc()) {
    $selected_courses[] = $row['course_id'];
}
?>
<?php include '../../templates/header.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">

    <header class="bg-white border-b border-slate-200 lg:hidden flex items-center justify-between p-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
            </button>
            <span class="font-display font-bold text-lg text-slate-800">KRS Online</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
            <?= strtoupper(substr($_SESSION['username'] ?? 'S', 0, 1)) ?>
        </div>
    </header>

    <div class="flex-1 overflow-auto p-4 lg:p-8 animate-fade-in">
        <div class="max-w-4xl mx-auto">
            
            <header class="mb-8">
                <h1 class="text-2xl font-display font-bold text-slate-900">Kartu Rencana Studi</h1>
                <p class="text-slate-500">Pilih mata kuliah yang akan diambil semester ini.</p>
            </header>

            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center gap-3 shadow-sm animate-fade-in">
                    <ion-icon name="checkmark-circle" class="text-xl"></ion-icon>
                    <div>
                        <span class="font-bold">Berhasil!</span> Data KRS Anda telah disimpan.
                    </div>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center gap-4">
                    <label class="text-sm font-bold text-slate-700">Semester:</label>
                    <div class="relative">
                        <select onchange="window.location.href='krs.php?semester='+this.value" class="appearance-none pl-4 pr-10 py-2 rounded-lg border-slate-200 text-sm font-medium focus:border-primary focus:ring-primary/20 bg-white shadow-sm transition-all cursor-pointer hover:border-primary/50">
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                                <option value="<?= $i ?>" <?= $current_semester == $i ? 'selected' : '' ?>>Semester <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                            <ion-icon name="chevron-down-outline"></ion-icon>
                        </div>
                    </div>
                </div>

                <form method="POST" class="p-6">
                    <input type="hidden" name="semester" value="<?= $current_semester ?>">
                    
                    <?php if ($courses->num_rows == 0): ?>
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                                <ion-icon name="library-outline" class="text-2xl text-slate-400"></ion-icon>
                            </div>
                            <h3 class="text-lg font-medium text-slate-900">Mata Kuliah Kosong</h3>
                            <p class="text-slate-500 mt-1">Belum ada data mata kuliah yang tersedia saat ini.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto rounded-xl border border-slate-100 mb-6">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                                    <tr>
                                        <th class="px-6 py-4 w-16 text-center">Pilih</th>
                                        <th class="px-6 py-4 w-32">Kode</th>
                                        <th class="px-6 py-4">Mata Kuliah</th>
                                        <th class="px-6 py-4 text-center w-24">SKS</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php while ($course = $courses->fetch_assoc()): ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors group cursor-pointer" onclick="document.getElementById('check_<?= $course['id'] ?>').click()">
                                        <td class="px-6 py-4 text-center relative" onclick="event.stopPropagation()">
                                            <input type="checkbox" id="check_<?= $course['id'] ?>" name="courses[]" value="<?= $course['id'] ?>" 
                                                class="w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary/30 cursor-pointer transition-all"
                                                <?= in_array($course['id'], $selected_courses) ? 'checked' : '' ?>>
                                        </td>
                                        <td class="px-6 py-4 font-mono text-sm text-slate-600 font-bold group-hover:text-primary transition-colors">
                                            <?= htmlspecialchars($course['code']) ?>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-slate-900 group-hover:text-primary transition-colors">
                                            <?= htmlspecialchars($course['name']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded bg-slate-100 text-slate-600 text-xs font-bold">
                                                <?= $course['credits'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="flex items-center justify-end pt-2">
                            <button type="submit" class="px-6 py-3 rounded-xl bg-primary text-white font-bold shadow-lg shadow-primary/30 hover:bg-blue-600 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                                <ion-icon name="save-outline" class="text-lg"></ion-icon>
                                Simpan Rencana Studi
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>
