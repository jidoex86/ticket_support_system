<?php
$method="POST";
$cache="no-cache";
include "../../head.php";

if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'],$_POST['phone'])) {
$datasentin=ValidateAPITokenSentIN();
$user_id=$datasentin->usertoken;
    $first_name = cleanme(trim($_POST['first_name']));
    $last_name  = cleanme(trim($_POST['last_name']));
    $email      = cleanme(trim($_POST['email']));
    $phone      = cleanme(trim($_POST['phone'])) ;
    $password   = cleanme(trim($_POST['password']));

    // Validation
    if (input_is_invalid($first_name) || input_is_invalid($last_name) || input_is_invalid($email) || input_is_invalid($password)|| input_is_invalid($phone)) {
        respondBadRequest("All required fields must be filled.");
        exit;
    }

    if (strlen($first_name) < 3) {
        respondBadRequest("First name must be at least 3 characters.");
        exit;
    }

    if (strlen($last_name) < 3) {
        respondBadRequest("Last name must be at least 3 characters.");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respondBadRequest("Invalid email format.");
        exit;
    }

    if (strlen($password) < 8) {
        respondBadRequest("Password must be at least 8 characters.");
        exit;
    }

    if (!preg_match("/[A-Z]/", $password)) {
        respondBadRequest("Password must contain at least one uppercase letter.");
        exit;
    }

    if (!preg_match("/[\W]/", $password)) {
        respondBadRequest("Password must contain at least one special character.");
        exit;
    }

    // Check if email already exists
    $checkUser = $connect->prepare("
        SELECT user_id, first_name, last_name, email, phone, role, status, created_at, updated_at 
        FROM user 
        WHERE email = ?
    ");
    
    $checkUser->bind_param("s", $email);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        $existingUser = $result->fetch_assoc();
        respondBadRequest("User with this email already exists.", $existingUser);
        exit;
    }

    // Hash password
    //$hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $insertUser = $connect->prepare("
        INSERT INTO user (first_name, last_name, email, phone, password, role, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, 'Customer', 'Active', NOW(), NOW())
    ");

    $insertUser->bind_param("sssss", $first_name, $last_name, $email, $phone, $hashed_password);
    $insertUser->execute();

    if ($insertUser->affected_rows > 0) {

        $user_id = $connect->insert_id;

        // Fetch newly created user
        $getUser = $connect->prepare("
            SELECT user_id, first_name, last_name, email, phone, role, status, created_at, updated_at
            FROM user
            WHERE user_id = ?
        ");

        $getUser->bind_param("i", $user_id);
        $getUser->execute();
        $userDetails = $getUser->get_result()->fetch_assoc();

        respondOK($userDetails, "User created successfully");

    } else {
        respondBadRequest("User creation failed.");
    }

} else {
    respondBadRequest("Invalid request. Required fields missing.");
}
?>