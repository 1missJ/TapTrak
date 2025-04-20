<?php
// Include database connection
include('db_connection.php');

// Check if a section filter is applied
$sectionFilter = isset($_GET['section']) ? $_GET['section'] : '';

// Modify query based on the section filter
$query = "SELECT lrn, CONCAT(first_name, ' ', middle_name, ' ', last_name) AS fullname, email, student_type 
          FROM students";
          
if ($sectionFilter) {
    $query .= " WHERE section = '" . mysqli_real_escape_string($conn, $sectionFilter) . "'";
}

$result = mysqli_query($conn, $query);

// Initialize the students array
$students = [];
if (mysqli_num_rows($result) > 0) {
    $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Details</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include('sidebar.php'); ?>

<div class="main-content">
    <h2>Student List</h2>

    <div class="search-container">
    <div class="left-search">
    <form id="searchForm" onsubmit="return handleSearch(event)">
        <input type="text" id="searchInput" placeholder="Search by LRN or Name...">
        <button type="submit">Search</button>
    </form>
</div>

    <!-- Student Table -->
    <table class="student-table" id="studentTable">
        <thead>
            <tr>
                <th>LRN</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="studentTableBody">
            <?php foreach ($students as $student): ?>
                <tr data-type="<?= strtolower($student['student_type']) ?>">
                    <td><?= $student['lrn'] ?></td>
                    <td><?= $student['fullname'] ?></td>
                    <td><?= $student['email'] ?></td>
                    <td>
                        <button class='edit-btn' onclick='redirectToStudentInfo("<?= $student['lrn'] ?>")'><ion-icon name="create-outline"></ion-icon></button>
                        <button class='archive-btn'><ion-icon name="archive-outline"></ion-icon></button>
                        <button class='views-btn' onclick='redirectToStudentInfo("<?= $student['lrn'] ?>")'>
                            <ion-icon name='eye-outline'></ion-icon>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- JavaScript -->
<script>
    function handleSearch(event) {
        event.preventDefault(); // Prevent form from submitting
        searchStudent();
        return false;
    }

    function searchStudent() {
        const input = document.getElementById("searchInput").value.toUpperCase();
        const rows = document.querySelectorAll("#studentTableBody tr");

        rows.forEach(row => {
            const lrn = row.cells[0].textContent.toUpperCase();
            const name = row.cells[1].textContent.toUpperCase();
            const match = lrn.includes(input) || name.includes(input);
            row.style.display = match ? "" : "none";
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
