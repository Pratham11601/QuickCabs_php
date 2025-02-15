<?php
include 'db_connection.php';

// Pagination settings
$records_per_page = 100;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of records
$total_records_query = "SELECT COUNT(*) as count FROM vendordetails";
$total_records_result = $conn->query($total_records_query);
$total_records = $total_records_result->fetch_assoc()['count'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch vendor details with pagination
$sql = "SELECT * FROM vendordetails ORDER BY id DESC LIMIT $offset, $records_per_page";
$result = $conn->query($sql);

// Add vendor functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vendor'])) {
    $fullname = $_POST['fullname'];
    $vendor_cat = $_POST['vendor_cat'];
    $phone = $_POST['phone'];
    $aadhaar_number = $_POST['aadhaar_number'];
    $email = $_POST['email'];
    $city = $_POST['city'];

    $sql = "INSERT INTO vendordetails (fullname, vendor_cat, phone, aadhaar_number, email, city, createdAt) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $fullname, $vendor_cat, $phone, $aadhaar_number, $email, $city);
    
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=added");
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=add_failed");
    }
    $stmt->close();
    exit();
}

// Delete vendor functionality
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM vendordetails WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=deleted");
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=delete_failed");
    }
    $stmt->close();
    exit();
}

// Update vendor functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_vendor'])) {
    $id = $_POST['vendor_id'];
    $fullname = $_POST['fullname'];
    $vendor_cat = $_POST['vendor_cat'];
    $phone = $_POST['phone'];
    $aadhaar_number = $_POST['aadhaar_number'];
    $email = $_POST['email'];
    $city = $_POST['city'];

    $sql = "UPDATE vendordetails 
            SET fullname = ?, vendor_cat = ?, phone = ?, aadhaar_number = ?, email = ?, city = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $fullname, $vendor_cat, $phone, $aadhaar_number, $email, $city, $id);
    
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=updated");
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=update_failed");
    }
    $stmt->close();
    exit();
}

// Toggle vendor status
if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    $sql = "UPDATE vendordetails SET status = CASE WHEN status = 0 THEN 1 ELSE 0 END WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=status_updated");
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=status_update_failed");
    }
    $stmt->close();
    exit();
}

// Live search functionality
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $sql = "SELECT * FROM vendordetails 
            WHERE fullname LIKE ? OR vendor_cat LIKE ? OR phone LIKE ? 
            OR aadhaar_number LIKE ? OR email LIKE ? OR city LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = '%' . $searchQuery . '%';
    $stmt->bind_param("ssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        include 'table_row.php'; // Create this file to store the table row template
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid mt-3">
    <div class="row">
        <div class="col">
        <h4 style="margin-top: 10px;" class="mb-4">Vendor Details</h4>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    $success = $_GET['success'];
                    switch ($success) {
                        case 'added':
                            echo 'Vendor added successfully!';
                            break;
                        case 'updated':
                            echo 'Vendor updated successfully!';
                            break;
                        case 'deleted':
                            echo 'Vendor deleted successfully!';
                            break;
                        case 'status_updated':
                            echo 'Vendor status updated successfully!';
                            break;
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    $error = $_GET['error'];
                    switch ($error) {
                        case 'add_failed':
                            echo 'Failed to add vendor!';
                            break;
                        case 'update_failed':
                            echo 'Failed to update vendor!';
                            break;
                        case 'delete_failed':
                            echo 'Failed to delete vendor!';
                            break;
                        case 'status_update_failed':
                            echo 'Failed to update vendor status!';
                            break;
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search vendors...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="vendorTable">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Category</th>
                                    <th>Phone</th>
                                    <th>Aadhaar Number</th>
                                    <th>Email</th>
                                    <th>City</th>
                                    <th>Subscription Name</th>
                                    <th>Subscription Date</th>
                                    <th>Status</th>

                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['fullname']; ?></td>
                                        <td><?php echo $row['vendor_cat']; ?></td>
                                        <td><?php echo $row['phone']; ?></td>
                                        <td><?php echo $row['aadhaar_number']; ?></td>
                                        <td><?php echo $row['email']; ?></td>
                                        <td><?php echo $row['city']; ?></td>
                                        <td><?php echo $row['subscriptionPlan']; ?></td>
                                        <td><?php echo $row['subscription_date']; ?></td>

                                        <td>
                                            <span class="badge <?php echo $row['status'] == 0 ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo $row['status'] == 0 ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                      
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="?toggle_status=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm <?php echo $row['status'] == 0 ? 'btn-warning' : 'btn-success'; ?>">
                                                    <i class="fas fa-power-off"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete(<?php echo $row['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="vendor_id" id="edit_vendor_id">
                    <input type="hidden" name="update_vendor" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="fullname" id="edit_fullname" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" class="form-control" name="vendor_cat" id="edit_vendor_cat" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" id="edit_phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Aadhaar Number</label>
                        <input type="text" class="form-control" name="aadhaar_number" id="edit_aadhaar_number" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="edit_email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="city" id="edit_city" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this vendor?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteButton" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const editModal = new bootstrap.Modal(document.getElementById('editModal'));
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

// Function to handle live search
const searchVendors = debounce(() => {
    const searchQuery = document.getElementById('searchInput').value;
    const tbody = document.querySelector('#vendorTable tbody');
    
    if (searchQuery.length > 0) {
        fetch(`?search=${encodeURIComponent(searchQuery)}`)
            .then(response => response.text())
            .then(html => {
                tbody.innerHTML = html;
            })
            .catch(error => console.error('Error:', error));
    } else {
        location.reload();
    }
}, 300);

// Debounce function to limit API calls
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Add event listener for search input
document.getElementById('searchInput').addEventListener('keyup', searchVendors);

// Function to open edit modal with vendor data
function openEditModal(vendorData) {
    document.getElementById('edit_vendor_id').value = vendorData.id;
    document.getElementById('edit_fullname').value = vendorData.fullname;
    document.getElementById('edit_vendor_cat').value = vendorData.vendor_cat;
    document.getElementById('edit_phone').value = vendorData.phone;
    document.getElementById('edit_aadhaar_number').value = vendorData.aadhaar_number;
    document.getElementById('edit_email').value = vendorData.email;
    document.getElementById('edit_city').value = vendorData.city;
    
    editModal.show();
}

// Function to handle delete confirmation
function confirmDelete(vendorId) {
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    confirmDeleteButton.href = `?delete_id=${vendorId}`;
    deleteModal.show();
}

// Close alerts automatically after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 5000);
    });
});

// Form validation
document.getElementById('editForm').addEventListener('submit', function(event) {
    const form = event.target;
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }
    form.classList.add('was-validated');
});

// Handle server-side errors
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    if (error) {
        const errorAlert = document.createElement('div');
        errorAlert.className = 'alert alert-danger alert-dismissible fade show';
        errorAlert.innerHTML = `
            Error: ${error.replace(/_/g, ' ')}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.container-fluid').prepend(errorAlert);
    }
});
</script>
</body>
</html>