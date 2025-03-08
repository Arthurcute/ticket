<?php
require 'config.php';
$conn->set_charset("utf8");
header('Content-Type: application/json; charset=utf-8');

$section_id = $_GET['section_id'];

// Kiểm tra section_id có tồn tại
if (isset($section_id)) {
    $sql = "SELECT * FROM sections WHERE section_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $section_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo json_encode($row);
        } else {
            echo json_encode(array("error" => "No section found"));
        }

        $stmt->close();
    } else {
        echo json_encode(array("error" => "Failed to prepare SQL statement"));
    }
} else {
    echo json_encode(array("error" => "Missing section_id"));
}

$conn->close();
?>
