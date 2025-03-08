<?php
require 'config.php';
$conn->set_charset("utf8");

$sql = "SELECT * FROM events WHERE status = 'sắp diễn ra'";
$result = $conn->query($sql);

$events = array();

while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode($events);

$conn->close();
?>
