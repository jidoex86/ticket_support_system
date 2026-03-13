<?php
$method="POST";
$cache="no-cache";
include "../../head.php";

if(isset(
    $_POST['first_name'],
    $_POST['last_name'],
    $_POST['email'],
    $_POST['password']
)){

    $first_name = cleanme(trim($_POST['first_name']));
    $last_name  = cleanme(trim($_POST['last_name']));
    $email      = cleanme(trim($_POST['email']));
    $phone      = isset($_POST['phone']) ? cleanme(trim($_POST['phone'])) : null;
    $password   = cleanme(trim($_POST['password']));

    // validation
    if(
        input_is_invalid($first_name) ||
        input_is_invalid($last_name) ||
        input_is_invalid($email) ||
        input_is_invalid($password)
    ){

        respondBadRequest("All required fields must be filled");

    }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

        respondBadRequest("Invalid email format");

    }else{

        // check if email already exists
        $checkUser = $connect->prepare("SELECT user_id FROM user WHERE email=?");
        $checkUser->bind_param("s",$email);
        $checkUser->execute();
        $result = $checkUser->get_result();

        if($result->num_rows > 0){

            respondBadRequest("Email already registered");

        }else{

            // hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // insert user
            $insertUser = $connect->prepare("
                INSERT INTO user 
                (first_name,last_name,email,phone,password) 
                VALUES (?,?,?,?,?)
            ");

            $insertUser->bind_param(
                "sssss",
                $first_name,
                $last_name,
                $email,
                $phone,
                $hashed_password
            );

            $insertUser->execute();

            if($insertUser->affected_rows > 0){

                $user_id = $connect->insert_id;

                // get created user
                $getUser = $connect->prepare("
                    SELECT user_id,first_name,last_name,email,phone,role,status,created_at 
                    FROM user 
                    WHERE user_id=?
                ");

                $getUser->bind_param("i",$user_id);
                $getUser->execute();
                $userDetails = $getUser->get_result()->fetch_assoc();

                respondOK($userDetails,"Registration successful.");

            }else{

                respondBadRequest("Registration failed");

            }

        }

    }

}else{

    respondBadRequest("Invalid request. Required fields missing.");

}

?>