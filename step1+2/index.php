<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>حساب المعدل الجامعي GPA</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>GPA Calculator</h1>
    
    <form action="index.php" method="POST">
        <div id="courses">
            <div class="course-row">
                <input type="text" name="course[]" placeholder="اسم المادة" required>
                <input type="number" name="credits[]" placeholder="المعامل" min="1" required style="width: 80px;">
                <select name="grade[]">
                    <option value="4.0">A (ممتاز)</option>
                    <option value="3.0">B (جيد جداً)</option>
                    <option value="2.0">C (جيد)</option>
                    <option value="1.0">D (مقبول)</option>
                    <option value="0.0">F (راسب)</option>
                </select>
            </div>
        </div>

        <button type="button" class="btn-add" onclick="addCourse()">+ إضافة مادة أخرى</button>
        <br><br>
        <input type="submit" name="calculate" value="احسب المعدل الآن" class="btn-calc">
    </form>

    <?php
    if (isset($_POST['calculate'])) {
        $credits = $_POST['credits'];
        $grades = $_POST['grade'];
        
        $total_points = 0;
        $total_credits = 0;

        for ($i = 0; $i < count($credits); $i++) {
            $c = (float)$credits[$i];
            $g = (float)$grades[$i];
            
            $total_points += ($g * $c);
            $total_credits += $c;
        }

        if ($total_credits > 0) {
            $gpa = $total_points / $total_credits;
            echo "<div class='result success'>";
            echo "مجموع المعاملات: $total_credits <br>";
            echo "معدلك النهائي هو: " . number_format($gpa, 2);
            echo "</div>";
        }
    }
    ?>
</div>

<script src="script.js"></script>

</body>
</html>
