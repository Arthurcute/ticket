<?php
include 'config.php';

$email = $_POST['email'];
$response = array();

$query = "SELECT userId FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $response['exists'] = true;
    $response['userId'] = $row['userId'];
} else {
    $response['exists'] = false;
}

echo json_encode($response);
?>
