<?php
include 'db_connection.php';

// Handle add request
if (isset($_POST['add'])) {
    $cat_name = $_POST['cat_name'];

    // Secure the INSERT query using a prepared statement
    $sql = "INSERT INTO category (cat_name) VALUES (?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $cat_name);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Category added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding category.');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Secure the DELETE query using a prepared statement
    $sql = "DELETE FROM category WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Category deleted successfully!');</script>";
        } else {
            echo "<script>alert('Error deleting category.');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch data from category table
$sql = "SELECT * FROM category";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
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
    /* Action icons adjustments for mobile */
    

    /* Full-width table on mobile */
    .table {
        table-layout: auto; /* Allow table to resize automatically */
        margin-bottom: 20px; /* Adjust margin for mobile */
    }

    /* Table cell padding for mobile */
    .table td, .table th {
        padding: 8px; /* Increase padding for easier readability */
        font-size: 14px; /* Adjust font size for readability */
    }

    /* Form control width for mobile */
    .form-control {
        width: 100%; /* Make form control full width on mobile */
        font-size: 14px; /* Adjust font size */
    }

    /* Container adjustments */
    .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }

    /* Action icons margin adjustments */
    
}

/* Very Small Mobile View */
@media (max-width: 480px) {
    /* Further adjustments for very small screens */
    

    /* Adjust table font size for very small screens */
    .table td, .table th {
        font-size: 12px;
        padding: 8px; /* Adjust padding for small screens */
    }

    /* Form control adjustments for very small screens */
    .form-control {
        font-size: 14px;
        padding: 8px;
    }

    /* Container adjustments for very small screens */
    .container-fluid {
        padding-left: 5px;
        padding-right: 5px;
    }

    /* Ensure better spacing for the table */
    .table {
        margin-bottom: 15px; /* Adjust margin for very small screens */
    }
}


    </style>
</head>
<body>
<h4 style="margin-left:10px;">Manage Categories</h4>

    <div class="container-fluid mt-0 ">
    <hr>  

        <!-- Add New Category Form -->
        <form action="" method="POST" class="mb-3 d-flex">
            <div class="input-group">
                <input type="text" class="form-control" id="cat_name" name="cat_name" placeholder="Enter category name" required>
                <button type="submit" name="add" class="btn btn-success">Add</button>
            </div>
        </form>
        <hr>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10%;">Sr. No.</th>
                        <th  style="width: 70%;">Category Name</th>
                        <th  style="width: 20%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $serialNo = 1; // Start serial number from 1
                    while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $serialNo++; ?></td> <!-- Display the serial number -->
                        <td><?php echo htmlspecialchars($row['cat_name']); ?></td>
                        <td>
                            <!-- Delete Icon -->
                            <a href="?delete=<?php echo $row['id']; ?>" class="fa-solid fa-trash action-icons delete"></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
