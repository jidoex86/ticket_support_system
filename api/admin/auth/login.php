<?php
$method="POST";
$cache="no-cache";
include "../../head.php";
if (isset($_POST['admin_id']) && isset($_POST['password'])) {

    $admin_id  = cleanme(trim($_POST['admin_id']));
    $password = cleanme(trim($_POST['password']));

    // Validation
    if (input_is_invalid($admin_id) || input_is_invalid($password)) {
        respondBadRequest("admin ID and password are required.");
        exit;
    }

    if (!is_numeric($admin_id)) {
        respondBadRequest("admin ID must be numeric.");
        exit;
    }

   

    // Check if admin exists

    $checkadmin = $connect->prepare("SELECT * FROM admin WHERE admin_id = ?");
    $checkadmin->bind_param("i", $admin_id);
    $checkadmin->execute();
    $result = $checkadmin->get_result();

    if ($result->num_rows == 0) {
        // admin does not exist
        respondBadRequest("admin not found.");
        exit;
    }


       //STEP 2: Check password
    
    $checkPassword = $connect->prepare("SELECT * FROM admin WHERE admin_id = ? AND password = ?");
    $checkPassword->bind_param("is", $admin_id, $password);
    $checkPassword->execute();
    $passwordResult = $checkPassword->get_result();

    if ($passwordResult->num_rows == 0) {
        // Password incorrect
        respondBadRequest("Incorrect password.");
        //exit;
        
    }

    $accesstoken=getTokenToSendAPI($admin_id);

    // Login successful
    respondOK( ['access_token'=>$accesstoken],"Login successful.");

} else {
    respondBadRequest("Invalid request. admin ID and password are required.");
}
?>