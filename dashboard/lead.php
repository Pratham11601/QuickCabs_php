<?php
include 'db_connection.php';

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $sql = "DELETE FROM leads WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Lead deleted successfully!');</script>";
        } else {
            echo "<script>alert('Error deleting lead.');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch leads with search and filter functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_date = isset($_GET['filter_date']) ? mysqli_real_escape_string($conn, $_GET['filter_date']) : '';
$filter_created_at = isset($_GET['filter_created_at']) ? mysqli_real_escape_string($conn, $_GET['filter_created_at']) : '';

// Add conditions based on search and filter
$where_clauses = [];

if ($search) {
    // Use LIKE for search functionality in the relevant fields
    $where_clauses[] = "(vendor_name LIKE '%$search%' OR location_from LIKE '%$search%' OR to_location LIKE '%$search%' OR vendor_contact LIKE '%$search%')";
}

// Adjust the date filtering to match `YYYY-MM-DD` format in the database
if ($filter_date) {
    // Convert dd/mm/yyyy to yyyy-mm-dd format for filter comparison
    $filter_date = DateTime::createFromFormat('d/m/Y', $filter_date)->format('Y-m-d');
    $where_clauses[] = "date = '$filter_date'"; // Simple equality check
}

if ($filter_created_at) {
    // Convert dd/mm/yyyy to yyyy-mm-dd format for filter comparison
    $filter_created_at = DateTime::createFromFormat('d/m/Y', $filter_created_at)->format('Y-m-d');
    $where_clauses[] = "createdAt LIKE '$filter_created_at%'"; // Using LIKE for partial date match (e.g., YYYY-MM)
}

$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
}

$sql = "SELECT id, createdAt, vendor_name, location_from, to_location, date, time, vendor_contact, is_active FROM leads $where_sql ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error in query: " . mysqli_error($conn));
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leads</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
      body {
    font-size: 16px;
}

.action-icons {
    cursor: pointer;
    font-size: 10px;
    padding: 6px 7px;
    margin-right: 5px;
    border-radius: 50%;
    color: white;
    transition: all 0.3s ease;
}

.action-icons.delete {
    background-color: #dc3545;
}

.action-icons.delete:hover {
    background-color: #c82333;
    transform: scale(1.2);
}

.active-yes {
    color: green;
    text-align: center;
    font-size: 20px;
}

.active-no {
    color: grey;
    text-align: center;
    font-size: 20px;
}

.table th, .table td {
    text-align: center;
    padding: 5px;
}

table {
    table-layout: auto;
    width: 100%;
    font-size: 14px;
}
/* Style for the sorting button */
.sort-btn {
    background: none;
    border: none;
    color: inherit;
    font-size: 18px;
    cursor: pointer;
    padding: 0;
    margin-left: 5px;
    transition: color 0.3s ease;
}

/* Optional: Change color on hover */
.sort-btn:hover {
    color:rgb(234, 114, 54);
}


/* Mobile View */
@media (max-width: 768px) {
    .table th, .table td {
        padding: 8px;
        font-size: 14px;
    }

    .action-icons {
        font-size: 12px;
        padding: 6px 10px;
    }

    .action-icons.delete {
        font-size: 12px;
        padding: 6px 10px;
    }

    /* Adjust table font size for smaller devices */
    table {
        font-size: 12px;
    }

    /* Adjust table padding for better readability on mobile */
    .table th, .table td {
        padding: 10px;
    }

    /* Form control adjustments for mobile */
    .form-control {
        font-size: 14px;
        padding: 8px;
    }
    .filter-form {
        flex-direction: column; /* Stack the filters vertically */
        padding: 10px;
    }

    .filter-group {
        margin-right: 0;
        margin-bottom: 15px;
        width: 100%; /* Full width for each filter group */
        
        
    }

    .filter-group label {
        font-size: 14px;
    }

    .filter-group input,
    {
        width: 100%; /* Make input and button full width */
    }

    .divider {
        display: none; /* Hide vertical divider on mobile */
    }
    .table-container {
        overflow-x: auto;  /* Enable horizontal scroll on small screens */
    }
}

/* Very Small Mobile View */
@media (max-width: 480px) {
    body {
        font-size: 14px;
    }

    .action-icons {
        font-size: 12px;
        padding: 6px 8px;
    }

    table {
        font-size: 12px;
    }
    .table-container {
        overflow-x: auto;  /* Enable horizontal scroll on small screens */
    }

    .table th, .table td {
        padding: 8px;
        font-size: 12px;
    }

    .form-control {
        font-size: 14px;
        padding: 6px;
    }

    .action-icons.delete {
        padding: 5px 8px;
        font-size: 12px;
    }
}

.search-container {
    padding: 10px;
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.filter-form {
    padding: 10px;
    background-color: #f1f3f5;
    border: 1px solid #ccc;
    border-radius: 8px;
}


    </style>
</head>
<body>
<div class="container-fluid">
    <h4 class=" mb-4">Manage Leads</h4>

    <!-- Search and Filter Form -->
    <div class="search-container mb-4">
        <form action="" method="GET" class="d-flex align-items-center">
            <div class="input-group">
                <input type="text" id="search" name="search" class="form-control" placeholder="Search by anything" value="<?php echo htmlspecialchars($search); ?>">
                
            </div>
        </form>
    </div>

    <div class="filter-form mb-4">
    <form action="" method="GET" class="row g-3 justify-content-center">

        <!-- Created At Filter -->
        <div class="col-md-6 col-lg-5 d-flex align-items-center">
            <label for="filter_created_at" class="me-2">Received On:</label>
            <input type="text" name="filter_created_at" id="filter_created_at" class="form-control" placeholder="dd/mm/yyyy" value="<?php echo htmlspecialchars($filter_created_at); ?>">
            <button type="submit" class="btn btn-success ms-3">Filter</button>
        </div>

        <!-- Trip Date Filter -->
        <div class="col-md-6 col-lg-5 d-flex align-items-center">
            <label for="filter_date" class="me-2">Trip Date:</label>
            <input type="text" name="filter_date" id="filter_date" class="form-control" placeholder="dd/mm/yyyy" value="<?php echo htmlspecialchars($filter_date); ?>" >
            <button type="submit" class="btn btn-success ms-3">Filter</button>
        </div>

    </form>
</div>





</div>


    <div class="table-container text-center mt-4">
        <table class="table table-bordered" id="dataTable">
            <thead>
            <tr>
            <th>
    Sr. No.
    <button onclick="toggleSort(this)" class="sort-btn">
        <i class="fa-solid fa-caret-up"></i>
    </button>
</th>

                <th>Received On</th>
                <th>Vendor Name</th>
                <th>Location From</th>
                <th>Location To</th>
                <th>Trip Date</th>
                <th>Trip Time</th>
                <th>Contact</th>
                <th>Active</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody id="leads-tbody">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['createdAt'])); ?></td>
                    <td><?php echo htmlspecialchars($row['vendor_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['location_from']); ?></td>
                    <td><?php echo htmlspecialchars($row['to_location']); ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td><?php echo date('h:i A', strtotime($row['time'])); ?></td>
                    <td><?php echo htmlspecialchars($row['vendor_contact']); ?></td>
                    <td>
                        <?php if ($row['is_active']): ?>
                            <i class="fa-solid fa-check-circle active-yes"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-times-circle active-no"></i>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?delete=<?php echo $row['id']; ?>">
                            <i class="fa-solid fa-trash action-icons delete"></i>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#search').on('input', function () {
            const searchValue = $(this).val().toLowerCase();
            $('#leads-tbody tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
            });
        });
    });
    

</script>
<script>
        let sortOrder = "asc"; // Initial sort order

function toggleSort(button) {
    const columnIndex = 0; // Sr. No. column index
    const table = document.getElementById("dataTable");
    const tbody = table.tBodies[0];
    const rows = Array.from(tbody.rows);

    // Sort rows based on the current sort order
    rows.sort((rowA, rowB) => {
        const cellA = parseInt(rowA.cells[columnIndex].textContent.trim(), 10);
        const cellB = parseInt(rowB.cells[columnIndex].textContent.trim(), 10);

        if (sortOrder === "asc") {
            return cellA > cellB ? 1 : -1;
        } else {
            return cellA < cellB ? 1 : -1;
        }
    });

    // Append sorted rows back to the table body
    rows.forEach(row => tbody.appendChild(row));

    // Toggle the sort order
    sortOrder = sortOrder === "asc" ? "desc" : "asc";

    // Update the button icon
    const icon = button.querySelector("i");
    icon.className = sortOrder === "asc" ? "fa-solid fa-caret-down" : "fa-solid fa-caret-up";
}
    </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
