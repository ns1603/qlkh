<?php
session_start();
include(__DIR__ . '/../config.php');

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($email) || empty($password) || empty($role)) {
        $message = "Vui lòng điền đầy đủ thông tin.";
    } else {
        $checkSql = ($role === 'admins') 
            ? "SELECT id FROM admins WHERE username = ?" 
            : "SELECT id FROM users WHERE email = ?";

        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $message = "Email đã tồn tại. Vui lòng dùng email khác.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            if ($role === 'admins') {
                $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $email, $hashedPassword);
            } else {
                $sql = "INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $fullname, $email, $hashedPassword, $role);
            }

            if ($stmt->execute()) {
                // Thiết lập session sau khi đăng ký
                $_SESSION['user_role'] = $role;
                $_SESSION['user_id'] = $conn->insert_id;

                // Chuyển hướng
                if ($role === 'admins' || $role === 'teacher') {
                    // Đã thêm dấu chấm phẩy
                    header("Location: /Learning/admin/index.php"); 
                } else {
                    header("Location: ../home.php");
                }
                exit;
            } else {
                $message = "Lỗi khi tạo tài khoản: " . $stmt->error;
            }
    }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Đăng ký tài khoản</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .register-box {
            width: 400px; margin: 100px auto; padding: 20px;
            background: white; border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,.2);
        }
        input, select { width: 100%; padding: 10px; margin: 10px 0; }
        button { width: 100%; padding: 10px; background: #28a745; border: none; color: white; }
        .message { text-align: center; color: red; }
    </style>
</head>
<body>

<div class="register-box">
    <h2>Đăng ký tài khoản</h2>
    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
    <form method="POST">
        <input type="text" name="fullname" placeholder="Họ và tên" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <select name="role" required>
            <option value="">-- Chọn vai trò --</option>
            <option value="student">Học viên</option>
            <option value="teacher">Giáo viên</option>
            <option value="admins">Quản trị viên</option>
        </select>
        <button type="submit">Tạo tài khoản</button>
    </form>
</div>

</body>
</html>