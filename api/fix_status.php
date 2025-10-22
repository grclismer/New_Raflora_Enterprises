<?php
// api/fix_all_status.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

// Fix ALL accounts that have deactivation dates but empty status
$result = $conn->query("UPDATE accounts_tbl SET status = 'deactivated' WHERE deactivation_date IS NOT NULL AND (status IS NULL OR status = '')");

$affected = $conn->affected_rows;
echo "Fixed $affected accounts. Status updated to 'deactivated' for all accounts with deactivation dates.";

$conn->close();
?>