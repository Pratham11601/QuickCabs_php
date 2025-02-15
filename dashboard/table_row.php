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