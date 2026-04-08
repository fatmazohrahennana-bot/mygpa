<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>GPA Pro Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-top: 50px; }
        .card { border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container">
    <div class="card p-4">
        <h2 class="text-center mb-4 text-primary">GPA Pro Calculator 🎓</h2>
        
        <form action="" method="POST" id="gpaForm">
            <div id="courseContainer">
                <div class="row g-2 mb-2">
                    <div class="col-md-6"><input type="text" name="course[]" class="form-control" placeholder="اسم المادة" required></div>
                    <div class="col-md-3"><input type="number" name="credits[]" class="form-control" placeholder="المعامل" required min="1"></div>
                    <div class="col-md-3">
                        <select name="grade[]" class="form-select">
                            <option value="4.0">A (4.0)</option>
                            <option value="3.0">B (3.0)</option>
                            <option value="2.0">C (2.0)</option>
                            <option value="1.0">D (1.0)</option>
                            <option value="0.0">F (0.0)</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="addCourseRow()">+ إضافة مادة</button>
            <hr>
            <button type="submit" name="calculate" class="btn btn-primary w-100 py-2">احسب النتيجة الآن</button>
        </form>

        <?php
        if (isset($_POST['calculate'])) {
            $total_pts = 0; $total_cr = 0;
            for ($i=0; $i < count($_POST['credits']); $i++) {
                $total_pts += (float)$_POST['grade'][$i] * (float)$_POST['credits'][$i];
                $total_cr += (float)$_POST['credits'][$i];
            }
            $gpa = $total_pts / $total_cr;
            $percent = ($gpa / 4) * 100;
            
            $color = "bg-danger";
            if($gpa >= 3.5) $color = "bg-success";
            elseif($gpa >= 2.5) $color = "bg-info";
            elseif($gpa >= 2.0) $color = "bg-warning";

            echo "<div class='mt-4 p-3 border rounded'>";
            echo "<h5>المعدل: " . number_format($gpa, 2) . " / 4.0</h5>";
            echo "<div class='progress' style='height: 25px;'>
                    <div class='progress-bar $color' role='progressbar' style='width: $percent%'></div>
                  </div>";
            echo "</div>";
        }
        ?>
    </div>
</div>
<script>
function addCourse() {
    var container = document.getElementById('courseContainer');
    
    var div = document.createElement('div');
    div.className = 'row g-2 mb-2';
    
    div.innerHTML = '<div class="col-6"><input type="text" class="form-control" placeholder="المادة"></div><div class="col-3"><input type="number" name="credits[]" class="form-control" placeholder="المعامل" required min="1"></div><div class="col-3"><select name="grade[]" class="form-select"><option value="4">A (4.0)</option><option value="3">B (3.0)</option><option value="2">C (2.0)</option><option value="0">F (0.0)</option></select></div>';
    
    container.appendChild(div);
}
</script>
</body>
</html>
