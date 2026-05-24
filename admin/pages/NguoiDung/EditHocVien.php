<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

if ($_SESSION['user_role'] == 'admins') {
    die("Bạn không có quyền thực hiện hành động này!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
if(!$user) die("Không tồn tại!");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET fullname=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $fullname, $email, $hash, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET fullname=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $fullname, $email, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['status_message'] = "Cập nhật thành công!";
        header("Location: ListHocVien.php");
        exit;
    }
}
?>
<?php include ROOT_PATH . "/admin/header.php"; ?>
<?php include ROOT_PATH . "/admin/navbar.php"; ?>
<div class="container-fluid page-body-wrapper">
    <?php include ROOT_PATH . "/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Sửa thông tin Học viên</h4>
                            <form class="forms-sample" method="POST">
                                <div class="form-group">
                                    <label>Họ và Tên</label>
                                    <input type="text" class="form-control" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Mật khẩu mới (Để trống nếu không đổi)</label>
                                    <input type="password" class="form-control" name="password">
                                </div>
                                <button type="submit" class="btn btn-gradient-warning me-2">Cập nhật</button>
                                <a href="ListHocVien.php" class="btn btn-light">Hủy</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>
