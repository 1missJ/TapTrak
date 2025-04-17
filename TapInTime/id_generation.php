<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Verification - TapInTime</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/ids.css">
    <link rel="stylesheet" href="assets/css/rfid.css">

</head>
<body>
    <!-- Include Sidebar -->
    <?php include('sidebar.php'); ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <h2>ID Generation</h2>

        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search student...">
            <button onclick="searchStudent()">Search</button>
        </div>

        <?php
        if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['user_role'];
$allowed_pages_for_counselor = ['student_verification.php', 'student_details.php', 'id_generation.php'];
$current_page = basename($_SERVER['PHP_SELF']);

if ($role === 'counselor' && !in_array($current_page, $allowed_pages_for_counselor)) {
    echo "Access denied.";
    exit;
}

        // Include database connection
        include 'db_connection.php';

        // Fetch student data from the database
        $sql = "SELECT lrn, first_name, middle_name, last_name, email, rfid, created_at FROM students";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }
        ?>

        <table class="student-table">
            <thead>
                <tr>
                    <th>LRN</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>RFID</th>
                    <th>Registered Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php 
                        // Combine first_name, middle_name, and last_name into "Name"
                        $full_name = $row['first_name'] . ' ' . 
                                    (!empty($row['middle_name']) ? $row['middle_name'] . ' ' : '') . 
                                    $row['last_name']; 
                    ?>
                    <tr>
                        <td><?php echo $row['lrn']; ?></td>
                        <td><?php echo $full_name; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                        <span class="rfid-value" data-lrn="<?php echo $row['lrn']; ?>">
                        <?php echo $row['rfid'] ?? 'Not Assigned'; ?>
                        </span>
                         <button class="edit-rfid-btn" data-lrn="<?php echo $row['lrn']; ?>">
                          ✏️
                         </button>
                        </td>
                        <td><?php echo $row['created_at']; ?></td>

                        <td>
                            <button class="generate-btn" data-lrn="<?php echo $row['lrn']; ?>">Generate</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="idModal" class="modal">
    <div class="modal-content">
        <div class="modal-body">
            <div class="close-container">
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <iframe id="idFrame" style="width:100%; height:500px; border:none;"></iframe>
        </div>
    </div>
</div>

<!-- RFID Edit Modal -->
<div id="rfidModal" class="rfid-modal">
    <div class="rfid-modal-content">
        <div class="rfid-close-container">
            <span class="rfid-close" onclick="closeRfidModal()">&times;</span>
        </div>
        <h3>Assign/Edit RFID</h3>
        <form id="rfidForm">
            <input type="hidden" id="rfidLRN">
            <label for="rfidInput">RFID Number:</label>
            <input type="text" id="rfidInput" required>
            <button type="submit">Save RFID</button>
        </form>
    </div>
</div>


    <script>
document.addEventListener("DOMContentLoaded", function () {
    const generateButtons = document.querySelectorAll(".generate-btn");
    const modal = document.getElementById("idModal");
    const idFrame = document.getElementById("idFrame");
    const closeBtn = document.querySelector(".close");

    generateButtons.forEach(button => {
        button.addEventListener("click", function () {
            let studentLRN = button.getAttribute("data-lrn");
            if (studentLRN) {
                idFrame.src = "id_template.php?lrn=" + studentLRN;
                modal.style.display = "flex"; // Ensure the modal is visible
            } else {
                alert("LRN not found. Please try again.");
            }
        });
    });

    // Close modal when clicking "X"
    closeBtn.addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Close modal when clicking outside of modal-content
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});

        // Search Functionality
        function searchStudent() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("studentTableBody");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[1]; // Search by student name
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
                }
            }
        }


        const assignButtons = document.querySelectorAll(".assign-rfid-btn");

assignButtons.forEach(button => {
    button.addEventListener("click", function () {
        let studentLRN = button.getAttribute("data-lrn");
        let rfid = prompt("Enter RFID number for LRN: " + studentLRN);

        if (rfid !== null && rfid.trim() !== "") {
            // Send the RFID to a PHP script via fetch
            fetch("assign_rfid.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `lrn=${studentLRN}&rfid=${rfid}`
            })
            .then(response => response.text())
            .then(data => {
    alert(data);
    // Update the RFID cell in the table
    const rfidCell = button.parentElement.parentElement.querySelector(".rfid-value");
    if (rfidCell) {
        rfidCell.textContent = rfid;
    }
})

            .catch(error => {
                console.error("Error assigning RFID:", error);
            });
        }
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".edit-rfid-btn");
    const rfidModal = document.getElementById("rfidModal");
    const rfidInput = document.getElementById("rfidInput");
    const rfidLRN = document.getElementById("rfidLRN");

    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            const lrn = this.getAttribute("data-lrn");
            rfidLRN.value = lrn;
            const rfidCell = this.previousElementSibling.textContent.trim();
            rfidInput.value = rfidCell !== 'Not Assigned' ? rfidCell : '';
            rfidModal.style.display = "flex";
        });
    });

    document.getElementById("rfidForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const lrn = rfidLRN.value;
        const rfid = rfidInput.value;

        fetch("update_rfid.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `lrn=${lrn}&rfid=${rfid}`
        })
        .then(response => response.text())
        .then(result => {
            if (result === "success") {
                const cell = document.querySelector(`.rfid-value[data-lrn="${lrn}"]`);
                cell.textContent = rfid;
                closeRfidModal();
            } else {
                alert("Failed to update RFID. Try again.");
            }
        });
    });
});

function closeRfidModal() {
    document.getElementById("rfidModal").style.display = "none";
}
</script>


    </script>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>      
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>