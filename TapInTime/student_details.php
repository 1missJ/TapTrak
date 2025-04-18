<?php
// Include database connection
include('db_connection.php');

// Fetch specific columns for students (without any grade-level restriction in the query)
$query = "SELECT lrn, CONCAT(first_name, ' ', middle_name, ' ', last_name) AS fullname, email, section, grade_level 
          FROM students";

$result = mysqli_query($conn, $query);

// Initialize the students array
$students = [];

// Check if any students were fetched
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

        <!-- Search Bar -->
        <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search by LRN or Name..." onkeypress="if(event.key === 'Enter') searchStudent();">
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
                    <th>LRN</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Section</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <?php
                // Check if there are students fetched
                foreach ($students as $student) {
                    $grade = $student['grade_level']; // Ensure grade_level is included
                    echo "<tr data-grade='$grade'>
                        <td>{$student['lrn']}</td>
                        <td>{$student['fullname']}</td>
                        <td>{$student['email']}</td>
                        <td>{$student['section']}</td>
                        <td>
                            <button class='edit-btn' onclick='redirectToStudentInfo(\"{$student['lrn']}\")'>Edit</button>
                            <button class='archive-btn'>Archive</button>
                            <button class='view-btn' onclick='redirectToStudentInfo(\"{$student['lrn']}\")'>
                                <ion-icon name='eye-outline'></ion-icon>
                            </button>
                        </td>
                    </tr>";
                }                
                ?>
            </tbody>
        </table>
    </div>

    <!-- JavaScript -->
    <script>
function searchStudent() {
    var input = document.getElementById("searchInput");
    var filter = input.value.toUpperCase();
    var table = document.getElementById("studentTableBody");
    var tr = table.getElementsByTagName("tr");

    // Show the table if it's hidden
    document.getElementById("studentTable").style.display = "table";
    document.querySelector(".year-levels").style.display = "none";

    let found = false;

    for (let i = 0; i < tr.length; i++) {
        const lrnCell = tr[i].getElementsByTagName("td")[0]; // LRN
        const nameCell = tr[i].getElementsByTagName("td")[1]; // Full Name

        if (lrnCell && nameCell) {
            const lrnText = lrnCell.textContent || lrnCell.innerText;
            const nameText = nameCell.textContent || nameCell.innerText;

            if (
                lrnText.toUpperCase().includes(filter) ||
                nameText.toUpperCase().includes(filter)
            ) {
                tr[i].style.display = "";
                found = true;
            } else {
                tr[i].style.display = "none";
            }
        }
    }

    if (!found) {
        alert("No students found.");
    }
}

        // Show students for a selected year level
        function showStudents(yearLevel) {
    document.querySelector(".year-levels").style.display = "none";
    const table = document.getElementById("studentTable");
    const rows = document.querySelectorAll("#studentTableBody tr");
    table.style.display = "table";

    rows.forEach(row => {
        const grade = row.getAttribute("data-grade").trim().toLowerCase();
        if (grade === yearLevel.toLowerCase()) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

        function redirectToStudentInfo(studentLrn) {
            window.location.href = `student_profile.php?lrn=${studentLrn}`;
        }
    </script>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
