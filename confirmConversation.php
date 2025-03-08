<?php
require 'config.php';

$conversation_id = $_POST['conversation_id'];
$admin_id = $_POST['admin_id'];
$status = 'confirmed';

$query = "UPDATE conversations SET admin_id='$admin_id', statusConver ='$status' WHERE conversation_id='$conversation_id'";
if ($conn->query($query) === TRUE) {
    echo json_encode(['status' => 'confirmed']);
} else {
    echo json_encode(['error' => 'Error confirming conversation']);
}
?>
