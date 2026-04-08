<?php
$conn = new mysqli("localhost", "root", "", "gpa_db");
if ($conn->connect_error) { die("خطأ في الاتصال"); }

$success_msg = "";
$gpa = null;
$name = "";

if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=GPA_Report.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('اسم الطالب', 'الفصل', 'المعدل'));
    $query = $conn->query("SELECT student_name, semester, gpa FROM gpa_results ORDER BY id DESC");
    while($row = $query->fetch_assoc()) fputcsv($output, $row);
    exit();
}

if (isset($_POST['calculate'])) {
    $name = $_POST['student_name'];
    $sem = $_POST['semester'];
    $total_pts = 0; $total_cr = 0;
    
    if(isset($_POST['credits'])){
        foreach ($_POST['credits'] as $k => $v) {
            $total_pts += (float)$_POST['grade'][$k] * (float)$v;
            $total_cr += (float)$v;
        }
    }
    $gpa = ($total_cr > 0) ? ($total_pts / $total_cr) : 0;
    
    // حفظ في الداتابيز (Step 4)
    $stmt = $conn->prepare("INSERT INTO gpa_results (student_name, semester, gpa) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $sem, $gpa);
    if($stmt->execute()) { $success_msg = "تم حفظ نتيجة الطالب ($name) بنجاح! ✅"; }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>GPA Master - النسخة النهائية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma; padding: 40px 0; }
        .card { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
        .header-box { background: #0d6efd; color: white; padding: 20px; border-radius: 15px 15px 0 0; }
    </style>
</head>
<body>

<div class="container" style="max-width: 850px;">
    
    <div class="card mb-4">
        <div class="header-box text-center">
            <h3 class="m-0">🎓 نظام حساب وحفظ المعدلات</h3>
        </div>
        <div class="card-body p-4 text-start">
            <?php if($success_msg) echo "<div class='alert alert-success text-center'>$success_msg</div>"; ?>
            
            <form method="POST">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">اسم الطالب:</label>
                        <input type="text" name="student_name" class="form-control" required placeholder="مثلاً: زهراء">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">الفصل الدراسي:</label>
                        <input type="text" name="semester" class="form-control" required placeholder="S4">
                    </div>
                </div>

                <div id="courseContainer">
                    <label class="form-label fw-bold">المواد والمعاملات:</label>
                    <div class="row g-2 mb-2">
                        <div class="col-6"><input type="text" class="form-control" placeholder="اسم المادة"></div>
                        <div class="col-3"><input type="number" name="credits[]" class="form-control" placeholder="المعامل" required min="1"></div>
                        <div class="col-3">
                            <select name="grade[]" class="form-select">
                                <option value="4">A (4.0)</option><option value="3">B (3.0)</option>
                                <option value="2">C (2.0)</option><option value="0">F (0.0)</option>
                            </select>
                        </div>
                        </div>
                </div>
                
                <button type="button" class="btn btn-outline-primary btn-sm mt-3" onclick="addCourse()">+ إضافة مادة أخرى</button>
                <hr>
                <button type="submit" name="calculate" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">💾 احسب المعدل واحفظ في السجل</button>
            </form>
        </div>
    </div>

    <?php if ($gpa !== null): ?>
    <div class="card mb-4 p-4 text-center">
        <div class="row align-items-center">
            <div class="col-md-6 border-end">
                <h5 class="text-secondary">المعدل النهائي</h5>
                <div class="display-3 fw-bold text-primary"><?php echo number_format($gpa, 2); ?></div>
                <h4 class="<?php echo ($gpa >= 2) ? 'text-success' : 'text-danger'; ?>">
                    <?php echo ($gpa >= 2) ? "(احسنت يابطل(ة))ناجح ✅" : "للاسف راسب❌"; ?>
                    
                </h4>
            </div>
            <div class="col-md-6">
                <div style="max-width: 160px; margin: 0 auto;"><canvas id="gpaChart"></canvas></div>
            </div>
        </div>
    </div>
    <script>
        new Chart(document.getElementById('gpaChart'), {
            type: 'doughnut',
            data: { datasets: [{ data: [<?php echo $gpa; ?>, <?php echo 4-$gpa; ?>], backgroundColor: ['#0d6efd', '#e9ecef'], borderWidth: 0 }] },
            options: { cutout: '80%' }
        });
    </script>
    <?php endif; ?>

    <div class="card p-4 text-start">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0">📜 سجل الطلاب في قاعدة البيانات</h5>
            <a href="?export=true" class="btn btn-success btn-sm">تصدير CSV</a>
        </div>
        <table class="table table-hover text-center">
            <thead class="table-light">
                <tr><th>الطالب</th><th>الفصل</th><th>المعدل</th></tr>
            </thead>
            <tbody>
                <?php
                $res = $conn->query("SELECT * FROM gpa_results ORDER BY id DESC");
                while($row = $res->fetch_assoc()) {
                    echo "<tr><td>{$row['student_name']}</td><td>{$row['semester']}</td><td class='fw-bold'>{$row['gpa']}</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function addCourse() {
    var container = document.getElementById('courseContainer');
    var row = document.createElement('div');
    row.className = 'row g-2 mb-2';
    row.innerHTML = '<div class="col-6"><input type="text" class="form-control" placeholder="المادة"></div><div class="col-3"><input type="number" name="credits[]" class="form-control" required></div><div class="col-3"><select name="grade[]" class="form-select"><option value="4">A</option><option value="3">B</option><option value="2">C</option><option value="0">F</option></select></div>';
    container.appendChild(row);
}
</script>

</body>
</html>
