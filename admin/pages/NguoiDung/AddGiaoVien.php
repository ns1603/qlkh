<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Truy cập bị từ chối! Bạn không có quyền thêm Giáo viên.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($fullname) || empty($email) || empty($password)) {
        $error = "Vui lòng điền đầy đủ thông tin!";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email này đã được sử dụng bởi người khác!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'teacher'; 

            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $_SESSION['status_message'] = "Thêm Giáo viên thành công!";
                header("Location: ListGiaoVien.php");
                exit;
            } else {
                $error = "Lỗi hệ thống: " . $conn->error;
            }
        }
    }
}
?>

<?php 
include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/header.php"; 
include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/navbar.php"; 
?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Thêm Giáo viên mới </h3>
            </div>
            
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Thông tin Giáo viên</h4>
                            <p class="card-description"> Tài khoản này sẽ có quyền tạo khóa học và quản lý học viên. </p>

                            <?php if($error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <form class="forms-sample" method="POST">
                                <div class="form-group">
                                    <label>Họ và Tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="fullname" placeholder="Nguyễn Văn A" required>
                                </div>
                                <div class="form-group">
                                    <label>Email đăng nhập <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" placeholder="teacher@example.com" required>
                                </div>
                                <div class="form-group">
                                    <label>Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                
                                <button type="submit" class="btn btn-gradient-danger me-2">Tạo tài khoản</button>
                                <a href="ListGiaoVien.php" class="btn btn-light">Hủy</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
    </div>
</div>
