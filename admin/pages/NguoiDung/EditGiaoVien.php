<?php
session_start();
include(__DIR__ . '/../../../config.php');

// 1. CHỈ SUPER ADMIN MỚI ĐƯỢC VÀO TRANG NÀY
// Vì trang này cho phép đổi quyền hạn, nên phải bảo mật kỹ
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admins') {
    die("Truy cập bị từ chối! Chỉ Admin mới có quyền sửa đổi cấp bậc thành viên.");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';

// 2. LẤY THÔNG TIN USER
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("Người dùng không tồn tại!");
}

// 3. XỬ LÝ FORM CẬP NHẬT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $role_update = $_POST['role']; // Lấy quyền mới từ form
    
    // Validate cơ bản
    if (empty($fullname) || empty($email)) {
        $message = "<div class='alert alert-danger'>Vui lòng điền đầy đủ tên và email!</div>";
    } else {
        // CẬP NHẬT DATABASE (Bao gồm cả cột ROLE)
        $sql = "UPDATE users SET fullname = ?, email = ?, role = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql);
        $stmt_update->bind_param("sssi", $fullname, $email, $role_update, $id);
        
        if ($stmt_update->execute()) {
            $_SESSION['status_message'] = "Cập nhật thành công! {$user['fullname']} giờ là: $role_update";
            
            // Điều hướng về đúng danh sách dựa trên role mới
            if ($role_update == 'student') header("Location: ListHocVien.php");
            else header("Location: ListGiaoVien.php"); // Teacher hoặc Admin thì về đây
            exit;
        } else {
            $message = "<div class='alert alert-danger'>Lỗi: " . $conn->error . "</div>";
        }
    }
}
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/header.php"; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Chỉnh sửa thông tin & Cấp quyền </h3>
            </div>
            
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Hồ sơ: <?= htmlspecialchars($user['fullname']) ?></h4>
                            <?= $message ?>

                            <form class="forms-sample" method="POST">
                                <div class="form-group">
                                    <label>Họ và Tên</label>
                                    <input type="text" class="form-control" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Email (Tài khoản đăng nhập)</label>
                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>

                                <div class="form-group bg-light p-3 border rounded">
                                    <label class="font-weight-bold text-primary">Cấp bậc / Vai trò (Role)</label>
                                    
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="role" value="student" <?= ($user['role'] == 'student') ? 'checked' : '' ?>>
                                            Học viên (Student)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="role" value="teacher" <?= ($user['role'] == 'teacher') ? 'checked' : '' ?>>
                                            Giáo viên (Teacher)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="role" value="admins" <?= ($user['role'] == 'admins') ? 'checked' : '' ?>>
                                            Quản trị viên (Admin)
                                        </label>
                                    </div>
                                <button type="submit" class="btn btn-gradient-primary me-2">Lưu thay đổi</button>
                                <a href="ListHocVien.php" class="btn btn-light">Hủy</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/footer.php"; ?>
    </div>
</div>