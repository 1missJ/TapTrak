<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_students'], $_POST['current_grade_level'])) {
    $selected = $_POST['selected_students'];
    $current_grade = $_POST['current_grade_level'];

    // Logic to determine the next grade level
    $grade_map = [
        'Grade 7' => 'Grade 8',
        'Grade 8' => 'Grade 9',
        'Grade 9' => 'Grade 10',
        'Grade 10' => 'Grade 11',
    ];

    $next_grade = $grade_map[$current_grade] ?? null;

    if (!$next_grade) {
        echo "Invalid grade level mapping.";
        exit;
    }

    $stmt = $conn->prepare("UPDATE students SET grade_level = ?, promotion_status = 'promoted' WHERE lrn = ?");

    foreach ($selected as $lrn) {
        $stmt->bind_param("ss", $next_grade, $lrn);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    echo "success";
    exit; // Make sure script stops here and does NOT render HTML
}
?>


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
        <input type="text" id="searchInput" placeholder="Search by LRN or Name..." onkeypress="handleKeyPress(event)">
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


<script>
function searchStudent() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toUpperCase();
    const table = document.getElementById("studentTableBody");
    const tr = table.getElementsByTagName("tr");

    for (let i = 0; i < tr.length; i++) {
        const lrnCell = tr[i].getElementsByTagName("td")[0]; // LRN
        const nameCell = tr[i].getElementsByTagName("td")[1]; // Name

        if (lrnCell && nameCell) {
            const lrnText = lrnCell.textContent || lrnCell.innerText;
            const nameText = nameCell.textContent || nameCell.innerText;

            if (
                lrnText.toUpperCase().includes(filter) ||
                nameText.toUpperCase().includes(filter)
            ) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function handleKeyPress(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        searchStudent();
    }
}

    function promoteStudents() {
    const checkboxes = document.querySelectorAll(".promote-checkbox:checked");
    const selected = Array.from(checkboxes).map(cb => cb.value);

    if (selected.length === 0) {
        alert("Please select students to promote.");
        return;
    }

    const gradeLevel = "<?php echo $grade_level; ?>"; // dynamically echoed from PHP

    const formData = new FormData();
    selected.forEach(lrn => formData.append("selected_students[]", lrn));
    formData.append("current_grade_level", gradeLevel);

    fetch("promote_students.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        if (result.trim() === "success") {
            alert("Selected students promoted successfully!");
            location.reload();
        } else {
            alert("Promotion failed:\n" + result);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while promoting students.");
    });
}

</script>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
