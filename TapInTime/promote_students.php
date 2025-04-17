<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promote Students</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<!-- Include Sidebar -->
<?php include('sidebar.php'); ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <h2>Promote Students</h2>

        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search student...">
            <button onclick="searchStudent()">Search</button>
        </div>

        <!-- Student Table -->
        <table class="student-table" id="studentTable">
            <thead>
                <tr>
                    <th>LRN</th>
                    <th>Name</th>
                    <th>Student Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
<?php
include 'db_connection.php';

$section = isset($_GET['section']) ? $_GET['section'] : '';
$grade_level = isset($_GET['grade_level']) ? $_GET['grade_level'] : '';

$sql = "SELECT lrn, CONCAT(first_name, ' ', middle_name, ' ', last_name) AS fullname, student_type 
        FROM students 
        WHERE section = ? AND grade_level = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $section, $grade_level);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['lrn']}</td>
            <td>{$row['fullname']}</td>
            <td>{$row['student_type']}</td>
            <td><input type='checkbox' class='promote-checkbox' name='selected_students[]' value='{$row['lrn']}'></td>
          </tr>";
}

$stmt->close();
?>
</tbody>
        </table>

<!-- Promote Button fixed to bottom right -->
<div style="position: fixed; bottom: 20px; right: 30px; z-index: 1000;">
    <button onclick="promoteStudents()" style="
    background-color: #32cd32; 
    color: white; 
    border: none; 
    padding: 10px 20px; 
    border-radius: 5px; 
    box-shadow: 0 2px 6px rgba(0,0,0,0.3); 
    font-weight: bold; 
    font-size: 16px; 
    cursor: pointer;">
        Submit
    </button>
</div>



    <!-- Scripts -->
    <script>
        function searchStudent() {
            var input = document.getElementById("searchInput");
            var filter = input.value.toUpperCase();
            var table = document.getElementById("studentTableBody");
            var tr = table.getElementsByTagName("tr");

            for (var i = 0; i < tr.length; i++) {
                var td = tr[i].getElementsByTagName("td")[1];
                if (td) {
                    var txtValue = td.textContent || td.innerText;
                    tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
                }
            }
        }

        function promoteStudents() {
            var checkboxes = document.querySelectorAll(".promote-checkbox:checked");
            alert(checkboxes.length + " student(s) promoted successfully.");
        }
    </script>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
