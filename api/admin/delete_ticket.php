<?php
$method="POST";
$cache="no-cache";
include "../head.php";

if(isset($_POST['user_id'])){

    $user_id = cleanme(trim($_POST['user_id']));

    // validation
    if(input_is_invalid($user_id)){
        respondBadRequest("User ID is required");
    }
    else if(!is_numeric($user_id)){
        respondBadRequest("User ID must be numeric");
    }
    else{

        // check if user exists
        $checkuser = $connect->prepare("SELECT * FROM user WHERE user_id=?");
        $checkuser->bind_param("i",$user_id);
        $checkuser->execute();
        $result = $checkuser->get_result();

        if($result->num_rows > 0){

            // delete user/ticket
            $deleteticket = $connect->prepare("DELETE FROM user WHERE user_id=?");
            $deleteticket->bind_param("i",$user_id);

            if($deleteticket->execute()){

                respondOK(
                    [
                        "deleted_user_id"=>$user_id
                    ],
                    "Ticket deleted successfully."
                );

            }else{
                respondBadRequest("Unable to delete ticket.");
            }

        }else{
            respondBadRequest("Ticket not found.");
        }

    }

}
?>