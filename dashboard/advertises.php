<?php
include 'db_connection.php';

// Handle the form submission for editing
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/advertise/";
    $target_file = $target_dir . basename($image);

    // If a new image is uploaded, move it to the target directory
    if ($image) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $sql = "UPDATE advertises SET name = ?, image = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, 'ssi', $name, $target_file, $id);
                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Record updated successfully!');</script>";
                } else {
                    echo "<script>alert('Error updating record.');</script>";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            echo "<script>alert('Error uploading image.');</script>";
        }
    } else {
        // If no new image is uploaded, just update the name
        $sql = "UPDATE advertises SET name = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, 'si', $name, $id);
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Record updated successfully!');</script>";
            } else {
                echo "<script>alert('Error updating record.');</script>";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Fetch data
$sql = "SELECT * FROM advertises";
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if (!$result) {
    die("Error fetching data: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Advertises</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include the correct Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .table img {
            width: 90px;
            height: 70px;
            object-fit: cover;
        }

        .action-icons {
            cursor: pointer; 
            font-size: 10px; 
            padding:6px 7px; 
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
    </style>
</head>
<body>
<h4 style="margin-left:10px;">Manage Advertises</h4>

<h4 style="margin-left:10px;">Manage Advertises</h4>

    <div class="container-fluid mt-0">
    <hr> 
        <form action="" method="POST" class="mb-3 d-flex" enctype="multipart/form-data">
            <div class="input-group w-50">
                <input type="file" class="form-control" id="image" name="image" required>
                <button type="submit" name="add" class="btn btn-success">Add</button>
            </div>
        </form>
        <hr>
        <table class="table table-bordered">
            <thead>
                <tr>
                <th style="width: 10%;">Sr. No.</th>
                <th style="width: 70%;">Image</th>
                <th style="width: 20%;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $serialNo = 1;
                while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $serialNo++; ?></td>
                    <td><img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Advertise Image"></td>
                    <td>
                        <i class="fas fa-edit action-icons" onclick="openEditForm(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>')"></i>
                        <a href="?delete=<?php echo $row['id']; ?>" class="fa-solid fa-trash action-icons delete"></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit Advertise</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="editForm" method="POST" action="" enctype="multipart/form-data">
              <input type="hidden" id="edit-id" name="id">
              <div class="mb-3">
                <label for="edit-name" class="form-label">Name</label>
                <input type="text" class="form-control" id="edit-name" name="name">
              </div>
              <div class="mb-3">
                <label for="edit-image" class="form-label">Image</label>
                <input type="file" class="form-control" id="edit-image" name="image">
              </div>
              <button type="submit" name="edit" class="btn btn-primary">Save changes</button>
            </form>
          </div>
        </div>
      </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
