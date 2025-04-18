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

        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search student...">
            <button onclick="searchStudent()">Search</button>
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

$grades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];
$all_data = [];

foreach ($grades as $grade_level) {
    $sql = "SELECT section, COUNT(*) as total_students 
            FROM students 
            WHERE grade_level = ? 
            GROUP BY section";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $grade_level);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $section = htmlspecialchars($row['section']);
        $count = $row['total_students'];
        $all_data[] = [
            'grade' => $grade_level,
            'section' => $section,
            'count' => $count
        ];

        echo "<tr data-grade='{$grade_level}' style='display: none;'>
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
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("studentTableBody");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[1];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
                }
            }
        }

        // Show students for a selected year level
        function showStudents(yearLevel) {
    document.querySelector(".year-levels").style.display = "none";
    const table = document.getElementById("studentTable");
    const rows = document.querySelectorAll("#studentTableBody tr");

    table.style.display = "table";

    rows.forEach(row => {
        if (row.getAttribute("data-grade") === yearLevel) {
            row.style.display = "";
        } else {
            row.style.display = "none";
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
