<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Promotion</title>
    <link rel="stylesheet" href="assets/css/style.css">  
</head>

<body>
<!-- Include Sidebar -->
<?php include('sidebar.php'); ?>
    
    <!-- Main Content -->
    <div class="main-content">
    <h2>Student Promotion</h2>

    <div class="search-container" id="searchContainer" style="display: none;">
            <div class="left-search">
                <input type="text" id="searchInput" placeholder="Search by section...">
                <button onclick="searchStudent()">Search</button>
            </div>
        
            <div class="right-filter">
                <select id="studentTypeFilter" onchange="filterByStudentType()">
                    <option value="">Student Type</option>
                    <option value="Regular Student">Regular Student</option>
                    <option value="STI">STI</option>
                </select>
            </div>
        </div>        

        <!-- Year Level List -->
        <div class="year-levels">
            <div class="year-box" onclick="showStudents('Grade 7')">Grade 7</div>
            <div class="year-box" onclick="showStudents('Grade 8')">Grade 8</div>
            <div class="year-box" onclick="showStudents('Grade 9')">Grade 9</div>
            <div class="year-box" onclick="showStudents('Grade 10')">Grade 10</div>
        </div>


        <!-- Student Table -->
        <table class="student-table" id="studentTable" style="display:none;">
            <thead>
                <tr>
                    <th>Section</th>
                    <th>No. of Students</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
<?php
include 'db_connection.php';

$sql = "SELECT section, student_type, COUNT(*) as total_students 
        FROM students 
        WHERE grade_level = ? 
        GROUP BY section, student_type";

$grades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];
$all_data = [];

foreach ($grades as $grade_level) {
    $sql = "SELECT section, student_type, COUNT(*) as total_students 
            FROM students 
            WHERE grade_level = ? 
            GROUP BY section, student_type";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $grade_level);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $section = htmlspecialchars($row['section']);
        $studentType = htmlspecialchars($row['student_type']);
        $count = $row['total_students'];
    
        echo "<tr data-grade='{$grade_level}' data-type='{$studentType}' style='display: none;'>
                <td>{$section}</td>
                <td>{$count}</td>
                <td>
                    <button class='promote-btn' onclick=\"location.href='promote_students.php?section={$section}&grade_level={$grade_level}'\">Promote</button>
                </td>
              </tr>";
    }    

    $stmt->close();
}
?>
</tbody>
        </table>


    <!-- JavaScript -->
    <script>
function searchStudent() {
    var input = document.getElementById("searchInput");
    var filter = input.value.toUpperCase();
    var rows = document.querySelectorAll("#studentTableBody tr");

    rows.forEach(row => {
        // Only filter rows that are already visible (i.e., current grade level rows)
        if (row.style.display !== "none") {
            var td = row.getElementsByTagName("td")[0]; // Section column
            if (td) {
                var txtValue = td.textContent || td.innerText;
                row.style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            }
        }
    });
}

// Trigger search on pressing Enter
document.addEventListener("DOMContentLoaded", function () {
    var searchInput = document.getElementById("searchInput");

    searchInput.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault(); // Prevent form submission
            searchStudent();
        }
    });
});

function showStudents(yearLevel) {
    currentGrade = yearLevel;
    document.querySelector(".year-levels").style.display = "none";
    document.getElementById("searchContainer").style.display = "flex";
    const table = document.getElementById("studentTable");
    const rows = document.querySelectorAll("#studentTableBody tr");

    table.style.display = "table";

    rows.forEach(row => {
        const grade = row.getAttribute("data-grade");
        const type = row.getAttribute("data-type");
        const selectedType = document.getElementById("studentTypeFilter").value;

        const matchesType = selectedType === "" || type === selectedType;
        row.style.display = grade === yearLevel && matchesType ? "" : "none";
    });
}

function filterByStudentType() {
    const selectedType = document.getElementById("studentTypeFilter").value;
    const rows = document.querySelectorAll("#studentTableBody tr");

    rows.forEach(row => {
        const rowType = row.getAttribute("data-type");
        const rowGrade = row.getAttribute("data-grade");

        // Only filter if the row is part of the currently displayed grade
        if (row.style.display !== "none" || selectedType !== "") {
            const matchesType = selectedType === "" || rowType === selectedType;
            row.style.display = matchesType && rowGrade === currentGrade ? "" : "none";
        }
    });
}

        function promoteStudents() {
            var checkboxes = document.querySelectorAll(".promote-checkbox:checked");
            alert(checkboxes.length + " students promoted.");
        }

    </script>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
