<?php
// Include database connection
include('db_connection.php');

// Fetch the data including grade level and student type
$query = "SELECT section, grade_level, student_type, COUNT(*) AS student_count 
          FROM students 
          GROUP BY section, grade_level, student_type";

$result = mysqli_query($conn, $query);

// Initialize the students array
$students = [];

// Check if any data was fetched
// Check if any data was fetched
if (mysqli_num_rows($result) > 0) {
    // Fetch the results into the array
    $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<!-- Include Sidebar -->
<?php include('sidebar.php'); ?>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Student Details</h2>

        <div class="search-container" style="display:none;">
        <div class="left-search">
    <form id="searchForm" onsubmit="return handleSearch(event)">
        <input type="text" id="searchInput" placeholder="Search by section...">
        <button type="submit">Search</button>
    </form>
</div>

    <div class="right-filter" id="studentTypeFilterContainer" style="display: none;"></div>
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
                    <th>Student Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
<?php
if (!empty($students)) {
    foreach ($students as $student) {
        // Store grade level and student type for filtering
        echo "<tr data-grade='{$student['grade_level']}' data-type='{$student['student_type']}'>
            <td>{$student['section']}</td>
            <td>{$student['student_count']}</td>
            <td>{$student['student_type']}</td>
            <td>
                <button class='view-btn' onclick=\"location.href='details_student.php?section=" . urlencode($student['section']) . "'\">
                    <ion-icon name='eye-outline'></ion-icon>
                </button>
            </td>
        </tr>";
    }
}
?>
            </tbody>
        </table>
    </div>

    <!-- JavaScript -->
    <script>
    function handleSearch(event) {
        event.preventDefault(); // Prevent form from submitting normally
        searchStudent();
        return false;
    }

    function searchStudent() {
        var input = document.getElementById("searchInput");
        var filter = input.value.toUpperCase();
        var table = document.getElementById("studentTableBody");
        var tr = table.getElementsByTagName("tr");

        for (var i = 0; i < tr.length; i++) {
            var td = tr[i].getElementsByTagName("td")[0]; // Index 0 = Section
            if (td) {
                var txtValue = td.textContent || td.innerText;
                tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            }
        }
    }

    function showStudents(yearLevel) {
    document.querySelector(".year-levels").style.display = "none";
    document.querySelector(".search-container").style.display = "flex";

    const table = document.getElementById("studentTable");
    const tbody = document.getElementById("studentTableBody");
    const rows = tbody.querySelectorAll("tr");
    table.style.display = "table";

    let hasMatch = false;

    // Remove existing "no data" message if any
    const noDataRow = document.getElementById("noDataRow");
    if (noDataRow) noDataRow.remove();

    // Filter rows by grade level
    rows.forEach(row => {
        const grade = row.getAttribute("data-grade").trim().toLowerCase();
        if (grade === yearLevel.toLowerCase()) {
            row.style.display = "";
            hasMatch = true;
        } else {
            row.style.display = "none";
        }
    });

    // If no matching rows, insert a "No data available" row
    if (!hasMatch) {
        const newRow = document.createElement("tr");
        newRow.id = "noDataRow";
        newRow.innerHTML = `<td>No data available.</td>`;
        tbody.appendChild(newRow);
    }

    // Populate dropdown
    const dropdownHTML = ` 
        <select id="studentTypeFilter" onchange="filterByStudentType('${yearLevel}')">
            <option value="">Student Type</option>
            <option value="Regular Student">Regular Student</option>
            <option value="STI">STI</option>
        </select>
    `;
    const filterContainer = document.getElementById("studentTypeFilterContainer");
    filterContainer.innerHTML = dropdownHTML;
    filterContainer.style.display = "block";
}

        function filterByStudentType(selectedGrade) {
            const selectedType = document.getElementById("studentTypeFilter").value.toLowerCase();
            const rows = document.querySelectorAll("#studentTableBody tr");

            rows.forEach(row => {
                const rowType = (row.getAttribute("data-type") || "").toLowerCase();
                const rowGrade = (row.getAttribute("data-grade") || "").toLowerCase();

                const isGradeMatch = rowGrade === selectedGrade.toLowerCase();
                const isTypeMatch = !selectedType || rowType === selectedType;

                row.style.display = (isGradeMatch && isTypeMatch) ? "" : "none";
            });
        }
    </script>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
