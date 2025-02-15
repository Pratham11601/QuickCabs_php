<?php
include 'db_connection.php';

// Handle Add Request
if (isset($_POST['add'])) {
    $city_name = $_POST['city_name'];
    $state_name = $_POST['state_name'];

    $sql = "INSERT INTO cities (city_name, state_name) VALUES (?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'ss', $city_name, $state_name);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('City added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding city.');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $sql = "DELETE FROM cities WHERE city_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('City deleted successfully!');</script>";
        } else {
            echo "<script>alert('Error deleting city.');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle Edit Request
if (isset($_POST['edit'])) {
    $city_id = $_POST['city_id'];
    $city_name = $_POST['city_name'];
    $state_name = $_POST['state_name'];

    $sql = "UPDATE cities SET city_name = ?, state_name = ? WHERE city_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'ssi', $city_name, $state_name, $city_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('City updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating city.');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch cities with pagination (limit 25 rows per page)
$limit = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$sql = "SELECT * FROM cities LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Action icons style */
.action-icons {
    cursor: pointer; 
    font-size: 10px; 
    padding: 6px 7px; 
    margin-right: 5px; 
    border-radius: 50%; 
    color: white; 
    transition: all 0.3s ease;
}

/* Hover effects for action icons */
.action-icons:hover {
    background-color: #0056b3; /* Darker blue for delete icon on hover */
    transform: scale(1.1); /* Slightly enlarge icon on hover */
}

/* Specific style for the delete icon */
.action-icons.delete {
    background-color: #dc3545; /* Red background for delete icon */
}

.action-icons.delete:hover {
    background-color: #c82333; /* Darker red for delete icon on hover */
}

/* Full-width table */
.table {
    width: 100%;
    table-layout: fixed; /* Ensures columns use available width */
    margin-bottom: 30px; /* Remove margin below the table */
    text-align: center;
}

/* Reduce the space for the form */
.form-control {
    max-width: 300px;
}

/* Adjust container size */
.container-fluid {
    padding-left: 15px;
    padding-right: 15px;
    text-align: center;
}

/* Adjust the row heights for a compact look */
.table td, .table th {
    padding: 5px 10px; /* Reduced padding for compact view */
}

/* Remove margin below the last row */
.table tbody tr:last-child {
    margin-bottom: 0;
}

/* Mobile View */
@media (max-width: 768px) {
    /* Action icons size adjustments for mobile */
    

    /* Adjust table layout for smaller screens */
    .table {
        table-layout: auto; /* Allow table to resize automatically */
        margin-bottom: 20px; /* Adjust margin for mobile */
        overflow-x:auto;
    }

    /* Table cells padding for mobile */
    .table td, .table th {
        padding: 8px; /* Increase padding for easier readability on mobile */
        font-size: 14px; /* Adjust font size for readability */
    }

    .d-flex {
        display: flex;
        flex-direction: column; /* Stack the inputs and button vertically */
    }
    .input-group {
        width: 100%; /* Make the input group full width on mobile */
    }
    .form-control {
        margin-bottom: 10px; /* Add space between the inputs on small screens */
    }
    .btn {
        width: 100%; /* Make the button full width */
        padding: 10px; /* Increase button padding for easier tap */
    }
    .mt-2 {
        margin-top: 10px; /* Add top margin for buttons in mobile view */
    }

    /* Container adjustments for mobile */
    .container-fluid {
        padding: 10px;
        overflow-x: auto; /* Enable horizontal scrolling for table */
        -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS devices */
        margin-top: 20px;
    }

    /* Action icons spacing and alignment on mobile */
    .action-icons {
        margin-right: 8px; /* Increase spacing between icons */
    }
}

/* Very Small Mobile View */
@media (max-width: 480px) {
    /* Further adjustments for very small screens */
    

    /* Adjust table font size for small screens */
    .table td, .table th {
        font-size: 12px;
        padding: 8px; /* Adjust padding for smaller screens */
    }

    /* Form control adjustments for very small screens */
    .form-control {
        width: 100%;
        font-size: 14px;
        padding: 8px;
    }

    /* Container adjustments for very small screens */
    .container-fluid {
        padding: 10px;
        overflow-x: auto; /* Enable horizontal scrolling for table */
        -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS devices */
        margin-top: 20px;
    }

    /* Ensure better spacing for the table on small screens */
    .table {
        margin-bottom: 15px; /* Adjust margin for smaller screens */
    }
}


    </style>
</head>
<body>
<h4 style="margin-left:10px;">Manage Cities</h4>
    <div class="container-fluid mt-0">
        <hr>
        <form action="" method="POST" class="mb-3 d-flex flex-column">
    <div class="input-group">
        <input type="text" class="form-control mb-2" id="city_name" name="city_name" placeholder="Enter city name" required>
        <input type="text" class="form-control mb-2" id="state_name" name="state_name" placeholder="Enter state name" required>
        <button type="submit" name="add" class="btn btn-success">Add</button>
    </div>
</form>

        
        <hr>
        <!-- Display Cities Table -->
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th  style="width: 10%;">Sr.No.</th> <!-- Serial Number Column -->
                    <th  style="width: 35%;">City Name</th>
                    <th  style="width: 35%;">State Name</th>
                    <th  style="width: 20%;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $serial_no = $start + 1; // Start serial number from the current page's start value
                while ($row = mysqli_fetch_assoc($result)):
                ?>
                <tr>
                    <td><?php echo $serial_no++; ?></td> <!-- Display Serial Number -->
                    <td><?php echo htmlspecialchars($row['city_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['state_name']); ?></td>
                    <td>
                        <i class="fas fa-edit action-icons" data-bs-toggle="modal" data-bs-target="#editModal" onclick="openEditForm(<?php echo $row['city_id']; ?>, '<?php echo addslashes($row['city_name']); ?>', '<?php echo addslashes($row['state_name']); ?>')"></i>
                        <a href="?delete=<?php echo $row['city_id']; ?>" class="fa-solid fa-trash action-icons delete"></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        

    <!-- Edit Modal -->
    <div class="modal" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit City</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit-id" name="city_id">
                        <div class="mb-3">
                            <label for="edit-city-name" class="form-label">City Name</label>
                            <input type="text" class="form-control" id="edit-city-name" name="city_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-state-name" class="form-label">State Name</label>
                            <input type="text" class="form-control" id="edit-state-name" name="state_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="edit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditForm(id, cityName, stateName) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-city-name').value = cityName;
            document.getElementById('edit-state-name').value = stateName;
        }
    </script>
</body>
</html>
