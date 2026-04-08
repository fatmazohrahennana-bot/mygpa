function addCourse() {
    const coursesDiv = document.getElementById('courses');
    const newRow = document.createElement('div');
    newRow.className = 'course-row';
    
    newRow.innerHTML = 
        <input type="text" name="course[]" placeholder="اسم المادة" required>
        <input type="number" name="credits[]" placeholder="المعامل" min="1" required style="width: 80px;">
        <select name="grade[]">
            <option value="4.0">A</option>
            <option value="3.0">B</option>
            <option value="2.0">C</option>
            <option value="1.0">D</option>
            <option value="0.0">F</option>
        </select>
    ;
    
    coursesDiv.appendChild(newRow);
}
