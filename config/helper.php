<?php
function log_activity($conn, $action, $description = "") {
    $user_id = $_SESSION['user_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'];
    $action = mysqli_real_escape_string($conn, $action);
    $description = mysqli_real_escape_string($conn, $description);
    
    $query = "INSERT INTO audit_log (user_id, action, description, ip_address) 
              VALUES (" . ($user_id ? "'$user_id'" : "NULL") . ", '$action', '$description', '$ip')";
    mysqli_query($conn, $query);
}
?>
