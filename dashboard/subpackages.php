<?php
session_start();
include 'db_connection.php';

// Handle Add Request
if (isset($_POST['add'])) {
    $package_name = $_POST['package_name'];
    $duration_in_days = $_POST['duration_in_days'];
    $price_per_day = $_POST['price_per_day'];
    $total_price = $duration_in_days * $price_per_day;

    $sql = "INSERT INTO sub_packages (package_name, duration_in_days, price_per_day, total_price, createdAt, updatedAt) VALUES (?, ?, ?, ?, NOW(), NOW())";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'siii', $package_name, $duration_in_days, $price_per_day, $total_price);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['message'] = "Package added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add package.";
    }
    header("Location: subpackages.php");
    exit();
}

// Handle Edit Request
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $package_name = $_POST['package_name'];
    $duration_in_days = $_POST['duration_in_days'];
    $price_per_day = $_POST['price_per_day'];
    $total_price = $duration_in_days * $price_per_day;

    $sql = "UPDATE sub_packages SET package_name = ?, duration_in_days = ?, price_per_day = ?, total_price = ?, updatedAt = NOW() WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'siiii', $package_name, $duration_in_days, $price_per_day, $total_price, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['message'] = "Package updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update package.";
    }
    header("Location: subpackages.php");
    exit();
}

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM sub_packages WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['message'] = "Package deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete package.";
    }
    header("Location: subpackages.php");
    exit();
}

// Fetch packages with pagination (limit 25 rows per page)
$limit = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Get total number of records for pagination
$total_records_sql = "SELECT COUNT(id) AS total FROM sub_packages";
$total_records_result = mysqli_query($conn, $total_records_sql);
$total_records_row = mysqli_fetch_assoc($total_records_result);
$total_records = $total_records_row['total'];
$total_pages = ceil($total_records / $limit);

$sql = "SELECT * FROM sub_packages LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sub-Packages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<!-- Display Success or Error Messages -->
<?php if (isset($_SESSION['message'])): ?>
    <script>
        alert("<?php echo $_SESSION['message']; ?>");
    </script>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <script>
        alert("<?php echo $_SESSION['error']; ?>");
    </script>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<h4 class="text-center mt-4">Manage Sub-Packages</h4>
<div class="container mt-4">
    <form action="" method="POST" class="mb-3">
        <input type="text" class="form-control mb-2" name="package_name" placeholder="Enter package name" required>
        <input type="number" class="form-control mb-2" name="duration_in_days" placeholder="Enter duration in days" required>
        <input type="number" class="form-control mb-2" name="price_per_day" placeholder="Enter price per day" required>
        <button type="submit" name="add" class="btn btn-success">Add Package</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Package Name</th>
                <th>Duration (Days)</th>
                <th>Price Per Day</th>
                <th>Total Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['package_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['duration_in_days']); ?></td>
                    <td><?php echo htmlspecialchars($row['price_per_day']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_price']); ?></td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal"
                            onclick="openEditForm(<?php echo $row['id']; ?>, '<?php echo addslashes($row['package_name']); ?>', <?php echo $row['duration_in_days']; ?>, <?php echo $row['price_per_day']; ?>)">
                            <i class="fas fa-edit"></i>
                        </button>

                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="edit-id" name="id">
                    <label>Package Name</label>
                    <input type="text" class="form-control mb-2" id="edit-package-name" name="package_name" required>

                    <label>Duration (Days)</label>
                    <input type="number" class="form-control mb-2" id="edit-duration" name="duration_in_days" required>

                    <label>Price Per Day</label>
                    <input type="number" class="form-control mb-2" id="edit-price" name="price_per_day" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="edit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditForm(id, packageName, duration, price) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-package-name').value = packageName;
        document.getElementById('edit-duration').value = duration;
        document.getElementById('edit-price').value = price;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>