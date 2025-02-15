<?php
// Include the database connection
include('db_connection.php');

// Queries
$totalVendorsQuery = "SELECT COUNT(*) AS total_vendors FROM vendordetails";
$totalLeadsQuery = "SELECT COUNT(*) AS total_leads FROM leads";
$activeLeadsQuery = "SELECT COUNT(*) AS active_leads FROM leads WHERE is_active = 1";
$inactiveLeadsQuery = "SELECT COUNT(*) AS inactive_leads FROM leads WHERE is_active = 0";

// Execute queries with error checking
$totalVendorsResult = mysqli_query($conn, $totalVendorsQuery);
if (!$totalVendorsResult) {
    die("Query Error: " . mysqli_error($conn));
}
$totalLeadsResult = mysqli_query($conn, $totalLeadsQuery);
if (!$totalLeadsResult) {
    die("Query Error: " . mysqli_error($conn));
}
$activeLeadsResult = mysqli_query($conn, $activeLeadsQuery);
if (!$activeLeadsResult) {
    die("Query Error: " . mysqli_error($conn));
}
$inactiveLeadsResult = mysqli_query($conn, $inactiveLeadsQuery);
if (!$inactiveLeadsResult) {
    die("Query Error: " . mysqli_error($conn));
}

// Fetch results
$totalVendors = mysqli_fetch_assoc($totalVendorsResult)['total_vendors'];
$totalLeads = mysqli_fetch_assoc($totalLeadsResult)['total_leads'];
$activeLeads = mysqli_fetch_assoc($activeLeadsResult)['active_leads'];
$inactiveLeads = mysqli_fetch_assoc($inactiveLeadsResult)['inactive_leads'];

// Prepare response
$response = [
    'total_vendors' => $totalVendors,
    'total_leads' => $totalLeads,
    'active_leads' => $activeLeads,
    'inactive_leads' => $inactiveLeads
];

// Return JSON
echo json_encode($response);
?>
