<?php
$method="POST";
$cache="no-cache";
include "../../head.php";

if(isset($_POST['user_id'])){

    $user_id = cleanme(trim($_POST['user_id']));

    // validation
    if(input_is_invalid($user_id)){
        respondBadRequest("user ID is required");
    }else if(!is_numeric($user_id)){ 
        respondBadRequest("user ID must be numeric");
    }else{

        // check if user exists
        $checkuser = $connect->prepare("SELECT * FROM user WHERE user_id=?");
        $checkuser->bind_param("i", $user_id);
        $checkuser->execute();
        $result = $checkuser->get_result();

        if($result->num_rows > 0){

           
    $accesstoken=getTokenToSendAPI($user_id);

    // Logout successful
    respondOK( [],"Logout successful.");

} else {
    respondBadRequest("Invalid request. user ID and password are required.");
}

    }

}

?>