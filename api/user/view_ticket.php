<?php
$method="POST";
$cache="no-cache";
include "../head.php";

if(isset($_POST['user_id'])){

    $user_id = cleanme(trim($_POST['user_id']));

    // validation
    if(input_is_invalid($user_id)){
        respondBadRequest("User ID is required");
    }else if(!is_numeric($user_id)){ 
        respondBadRequest("User ID must be numeric");
    }else{

        // check if user exists
        $checkuser = $connect->prepare("SELECT user_id,first_name,last_name,email,phone,role,status,created_at,updated_at FROM user WHERE user_id=?");
        $checkuser->bind_param("i", $user_id);
        $checkuser->execute();
        $result = $checkuser->get_result();

        if($result->num_rows > 0){

            $userDetails = $result->fetch_assoc();

            respondOK($userDetails,"User details fetched successfully.");

        } else {

            respondBadRequest("User not found.");

        }

    }

}else{

    respondBadRequest("Invalid request. User ID is required.");

}

?>