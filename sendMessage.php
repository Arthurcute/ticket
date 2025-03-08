<?php
require 'config.php';

$conversation_id = $_POST['conversation_id'];
$sender_type = $_POST['sender_type'];
$message_content = $_POST['message_content'];

$query = "INSERT INTO messages (conversation_id, sender_type, message_content) VALUES ('$conversation_id', '$sender_type', '$message_content')";
if ($conn->query($query) === TRUE) {
    echo json_encode(['status' => 'message_sent']);
} else {
    echo json_encode(['error' => 'Error sending message']);
}
?>
