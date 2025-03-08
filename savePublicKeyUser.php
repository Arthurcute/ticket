<?php
// Kết nối cơ sở dữ liệu
include 'config.php';
// Nhận dữ liệu từ client
$user_id = $_POST['user_id'];
$public_key = $_POST['public_key']; // Public key đã được mã hóa Base64

// Kiểm tra xem user_id đã tồn tại trong bảng user_public_keys chưa
$sql_check = "SELECT * FROM user_keys WHERE user_id = '$user_id'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    // Nếu user_id đã tồn tại, không thực hiện insert
    echo "User public key already exists";
} else {
    // Nếu chưa có user_id, thực hiện insert public key
    $sql_insert = "INSERT INTO user_keys (user_id, public_key) VALUES ('$user_id', '$public_key')";
    
    if ($conn->query($sql_insert) === TRUE) {
        echo "Public key saved successfully";
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }
}

// Đóng kết nối
$conn->close();
?>
