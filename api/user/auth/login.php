<?php
$method="POST";
$cache="no-cache";
include "../../head.php";
if (isset($_POST['user_id']) && isset($_POST['password'])) {

    $user_id  = cleanme(trim($_POST['user_id']));
    $password = cleanme(trim($_POST['password']));

    // Validation
    if (input_is_invalid($user_id) || input_is_invalid($password)) {
        respondBadRequest("User ID and password are required.");
        exit;
    }

    if (!is_numeric($user_id)) {
        respondBadRequest("User ID must be numeric.");
        exit;
    }

   

    // Check if user exists

    $checkUser = $connect->prepare("SELECT * FROM user WHERE user_id = ?");
    $checkUser->bind_param("i", $user_id);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows == 0) {
        // User does not exist
        respondBadRequest("User not found.");
        exit;
    }


       //STEP 2: Check password
    
    $checkPassword = $connect->prepare("SELECT * FROM user WHERE user_id = ? AND password = ?");
    $checkPassword->bind_param("is", $user_id, $password);
    $checkPassword->execute();
    $passwordResult = $checkPassword->get_result();

    if ($passwordResult->num_rows == 0) {
        // Password incorrect
        respondBadRequest("Incorrect password.");
        //exit;
        
    }

    $accesstoken=getTokenToSendAPI($user_id);

    // Login successful
    respondOK( ['access_token'=>$accesstoken],"Login successful.");

} else {
    respondBadRequest("Invalid request. User ID and password are required.");
}
?>