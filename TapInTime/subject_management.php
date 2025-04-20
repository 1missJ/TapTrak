<?php
// Include database connection
include('db_connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/subject.css">
    <style>
        /* Basic styling for the buttons */
        .subject-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 16px;
    height: 40px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border-radius: 6px;
    color: #fff;
    background-color: #4CAF50;
    margin: 5px 8px 5px 0;
    transition: background-color 0.3s ease;
    min-width: 140px; /* makes sure all buttons are same width */
    box-sizing: border-box;
}

.view-btn {
    background-color: #66bb6a;
}

.subject-btn:hover {
    background-color: #388e3c;
}

.view-btn:hover {
    background-color: #558b2f;
}

.subject-btn ion-icon {
    font-size: 18px;
    margin-right: 6px;
    vertical-align: middle;
}


    </style>
</head>
<body>
    <!-- Include Sidebar -->
    <?php include('sidebar.php'); ?>

    <div class="subject-manager">
        <div class="subject-card">
            <form method="POST" action="">
                <label>Subject Names (comma separated):</label>
                <input type="text" name="subject_names" required>

                <label>Student Type:</label>
                <select name="student_type" required>
                    <option value="">-- Select Type --</option>
                    <option value="Regular">Regular</option>
                    <option value="STI">STI</option>
                    <option value="Both">Both</option>
                </select>

                <input type="submit" name="add_subject" class="subject-btn" value="Add Subjects">
            </form>
        </div>

       <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 10px;">
    <div>
        <a href="assign_subjects_to_grade.php?type=Regular" class="subject-btn" style="background-color: #2196F3;">Assign to Regular</a>
        <a href="assign_subjects_to_grade.php?type=STI" class="subject-btn" style="background-color: #9C27B0;">Assign to STI</a>
    </div>
</div>


        <table class="subject-table" style="margin-top:50px";>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Student Type</th>
                    <th style="padding-left: 130px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['add_subject'])) {
                    // Get the comma-separated subjects and split them into an array
                    $subjects = explode(',', $_POST['subject_names']);
                    $type = $_POST['student_type'];

                    // Loop through each subject and insert it into the database
                    foreach ($subjects as $subject) {
                        $subject = trim($subject); // Remove extra spaces
                        if (!empty($subject)) {
                            $stmt = $conn->prepare("INSERT INTO subjects (subject_name, student_type) VALUES (?, ?)");
                            $stmt->bind_param("ss", $subject, $type);
                            $stmt->execute();
                        }
                    }

                    echo "<script>window.location.href='subject_management.php';</script>";
                }

                $result = $conn->query("SELECT id, subject_name, student_type FROM subjects ORDER BY id DESC");
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                        $studentTypeDisplay = $row['student_type'] === 'Both' ? 'STI, Regular' : htmlspecialchars($row['student_type']);
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['subject_name']) ?></td>
                    <td><?= $studentTypeDisplay ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="assign_subjects.php?subject_id=<?= $row['id'] ?>" class="subject-btn">Assign Teachers</a>
                            <a href="view_assigned_teachers.php?subject_id=<?= $row['id'] ?>" class="subject-btn view-btn">
                                <ion-icon name="eye-outline"></ion-icon>
                            </a>
                        </div>
                    </td>
                <?php endwhile; else: ?>
                <tr><td colspan="3">No subjects added yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>    
</body>
</html>
