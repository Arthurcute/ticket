<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Cho phép mọi nguồn gốc

include 'config.php'; // Đảm bảo file config.php chứa các thông tin kết nối cơ sở dữ liệu
include_once 'aes_util.php';


// Lấy dữ liệu từ request (đảm bảo dữ liệu là JSON)
$data = json_decode(file_get_contents("php://input"));

// Kiểm tra xem user_id có trong request không
if (!empty($data->user_id)) {
    $user_id = $conn->real_escape_string($data->user_id);

    // Truy vấn để lấy thông tin người dùng từ cơ sở dữ liệu
    $sql = "SELECT user_id, name, email, phone, birthdate, gender FROM users WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    // Kiểm tra xem truy vấn có thành công hay không
    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Giải mã email và phone trước khi trả về cho người dùng
            $email = decrypt_aes($row['email']);
            $phone = decrypt_aes($row['phone']);

            // Trả về thông tin người dùng, bao gồm dữ liệu đã giải mã
            echo json_encode(array(
                "success" => true,
                "user" => array(
                    "user_id" => $row['user_id'],
                    "name" => $row['name'],
                    "email" => $email, // Giải mã email
                    "phone" => $phone, // Giải mã phone
                    "birthdate" => $row['birthdate'],
                    "gender" => $row['gender']
                )
            ));
        } else {
            // Nếu không tìm thấy người dùng
            echo json_encode(array("success" => false, "message" => "Người dùng không tồn tại"));
        }
    } else {
        // Trả về lỗi nếu truy vấn thất bại
        echo json_encode(array("success" => false, "message" => "Lỗi truy vấn cơ sở dữ liệu: " . $conn->error));
    }
} else {
    // Trả về lỗi nếu thiếu thông tin
    echo json_encode(array("success" => false, "message" => "Thiếu thông tin: user_id không hợp lệ"));
}

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>
