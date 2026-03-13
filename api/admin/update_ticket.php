<?php
$method="POST";
$cache="no-cache";
include "../head.php";

if(isset($_POST['ticket_id'],$_POST['status'])){



$ticket_id = cleanme($_POST['ticket_id']);
$status = cleanme($_POST['status']);

$valid = ['Open','In Progress','Resolved','Closed'];

if(!in_array($status,$valid)){
respondBadRequest("Invalid status");
exit;
}

$stmt = $connect->prepare("
UPDATE ticket
SET status = ?
WHERE ticket_id = ?
");

$stmt->bind_param("si",$status,$ticket_id);
$stmt->execute();

if($stmt->affected_rows > 0){

respondOK([],"Ticket status updated");

}else{

respondBadRequest("No changes made");

}

}else{

respondBadRequest("Ticket id and status required");

}
?>