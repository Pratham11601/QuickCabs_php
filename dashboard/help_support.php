<?php
include 'db_connection.php'; // Include your existing database connection file

// Insert the remark and date into the existing table when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query_id = $_POST['query_id'];  // The ID of the query to add remarks
    $remark = $_POST['remark'];  // The remark entered by the admin
    $remark_date = $_POST['remark_date'];  // The date entered by the admin (automatically set)

    // SQL query to update the query with the new remark and date
    $sql = "UPDATE help_supports SET remark = ?, remark_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $remark, $remark_date, $query_id);

    // Execute the query
    if ($stmt->execute()) {
        $message = "Remark added successfully!";
    } else {
        $message = "Error adding remark.";
    }
}

// Fetch data from the help_supports table to display
$sql = "SELECT * FROM help_supports ORDER BY id DESC";  // Replace with your actual table name
$result = $conn->query($sql);
$data = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leads</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
 /* General Styles */
.message {
    margin-bottom: 20px;
    padding: 15px;
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    border-radius: 4px;
    font-size: 16px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: left;
    color: #555;
    font-size: 16px;
}

textarea {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 10px;
    box-sizing: border-box;
    resize: vertical;
}

button {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #218838;
}

.action-column {
    text-align: center;
}

.action-column button {
    width: auto;
}

.container-fluid {
    text-align: center;
}

/* Mobile View */
@media (max-width: 768px) {
    .message {
        font-size: 14px;
        padding: 15px;
    }

    table, th, td {
        padding: 8px;
    }

    th, td {
        font-size: 14px;
    }

    textarea {
        font-size: 14px;
        padding: 12px;
    }

    button {
        width: 100%;
        padding: 12px;
        font-size: 18px;
    }

    .action-column button {
        width: 100%; /* Full width on mobile */
        padding: 12px;
    }

    .container-fluid {
        padding: 10px;
        overflow-x: auto; /* Enable horizontal scrolling for table */
        -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS devices */
        margin-top: 20px;
    }

   

    table {
        width: 100%; /* Allow table width to expand based on content */
       /* Set a minimum width to allow scroll if the table is too wide */
    }
}

/* Very Small Mobile View */
@media (max-width: 480px) {
    .message {
        font-size: 13px;
    }

    .table-container {
        overflow-x: auto; /* Keep horizontal scroll for table */
    }

    table, th, td {
        padding: 6px;
    }

    textarea {
        font-size: 14px;
    }

    button {
        padding: 12px;
        font-size: 16px;
    }
}

    </style>

</head>
<body>
<h4 style="margin-left:10px;">Manage Help & Support</h4>
<hr>
<div class="container-fluid ">

   

    <?php if (isset($message)) { ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>

    <table class="table-container">
        <thead>
            <tr>
                <th>ID</th>
                <th>Received On</th>
                <th>Query Message</th>
                <th>Vendor Category</th>
                <th>Vendor Name</th>
                <th>Vendor Contact</th>
                <th>Remarks</th>
                <th>Remark Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($data) {
                foreach ($data as $row) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>{$row['createdAt']}</td>";
                    echo "<td>{$row['query_message']}</td>";
                    echo "<td>{$row['vendor_category']}</td>";
                    echo "<td>{$row['vendor_name']}</td>";
                    echo "<td>{$row['vendor_contact']}</td>";

                    // Display the remark and date in the respective columns (but not inside a form)
                    echo "<td>{$row['remark']}</td>";
                    echo "<td>{$row['remark_date']}</td>";

                    // Action column with a Save button (the form will be placed outside the table)
                    echo "<td class='action-column'>
                            <form method='POST' action=''>
                                <input type='hidden' name='query_id' value='{$row['id']}'>
                                <textarea name='remark' placeholder='Enter remark'></textarea>
                               
                                <input type='hidden' name='remark_date' value='<?php echo date('Y-m-d'); ?>
                                <button type='submit'>Save</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No data found</td></tr>";
            }
            ?>
        </tbody>
    </table>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
