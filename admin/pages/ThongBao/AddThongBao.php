<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_id'], $_SESSION['user_role'])) {
    die("Truy cập bị từ chối!");
}

$user_id = (int)$_SESSION['user_id'];
$role    = $_SESSION['user_role'];
$message_status = '';

/* ===== CHẶN STUDENT ===== */
if ($role === 'student') {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title        = trim($_POST['title'] ?? '');
    $content      = trim($_POST['message'] ?? '');
    $target_group = $_POST['target_group'] ?? '';
    $priority     = $_POST['priority'] ?? 'normal';
    $is_pinned    = isset($_POST['is_pinned']) ? 1 : 0;

    if ($title === '' || $content === '') {
        $message_status = "⚠️ Vui lòng nhập tiêu đề và nội dung!";
    } else {

        $receivers = [];

        /* ================= ADMIN ================= */
        if ($role === 'admin' || $role === 'admins') {

            if ($target_group === 'all_teachers') {
                $sqlR = "SELECT id FROM users WHERE role = 'teacher'";
            } elseif ($target_group === 'all_students') {
                $sqlR = "SELECT id FROM users WHERE role = 'student'";
            } else {
                $sqlR = '';
            }

            if ($sqlR !== '') {
                $res = $conn->query($sqlR);
                while ($row = $res->fetch_assoc()) {
                    $receivers[] = (int)$row['id'];
                }
            }
        }

        /* ================= TEACHER ================= */
        if ($role === 'teacher' && $target_group === 'my_students') {

            $sqlR = "
                SELECT DISTINCT e.user_id
                FROM enrollments e
                JOIN courses c ON e.course_id = c.id
                WHERE c.teacher_id = ?
            ";

            $stmtR = $conn->prepare($sqlR);
            $stmtR->bind_param("i", $user_id);
            $stmtR->execute();
            $res = $stmtR->get_result();

            while ($row = $res->fetch_assoc()) {
                $receivers[] = (int)$row['user_id'];
            }
        }

        /* ===== XÁC ĐỊNH NGƯỜI GỬI (HƯỚNG 2) ===== */
        if ($role === 'admin' || $role === 'admins') {
            $sender_id   = $user_id;   // ID từ bảng admins
            $sender_type = 'admin';
        } else {
            $sender_id   = $user_id;   // ID từ bảng users
            $sender_type = 'user';
        }

        /* ================= INSERT ================= */
        if (count($receivers) > 0) {

            $stmt = $conn->prepare("
                INSERT INTO notifications
                (sender_id, sender_type, receiver_id, title, message, priority, is_pinned)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            foreach ($receivers as $r_id) {
                $stmt->bind_param(
                    "isisssi",
                    $sender_id,
                    $sender_type,
                    $r_id,
                    $title,
                    $content,
                    $priority,
                    $is_pinned
                );
                $stmt->execute();
            }

            $message_status = "✅ Đã gửi thông báo cho " . count($receivers) . " người!";
        } else {
            $message_status = "⚠️ Không tìm thấy người nhận phù hợp.";
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
    <h3 class="page-title">Tạo thông báo mới</h3>
</div>

<div class="row">
<div class="col-md-8 grid-margin stretch-card">
<div class="card">
<div class="card-body">

<h4 class="card-title">Soạn tin nhắn</h4>

<?php if ($message_status): ?>
    <div class="alert alert-info"><?= $message_status ?></div>
<?php endif; ?>

<form method="POST">

<div class="form-group">
    <label>Gửi đến</label>
    <select class="form-select" name="target_group" required>
        <?php if ($role === 'admin' || $role === 'admins'): ?>
            <option value="all_students">Toàn bộ Học viên</option>
            <option value="all_teachers">Toàn bộ Giáo viên</option>
        <?php endif; ?>

        <?php if ($role === 'teacher'): ?>
            <option value="my_students">Học viên của tôi</option>
        <?php endif; ?>
    </select>
</div>

<div class="form-group">
    <label>Tiêu đề</label>
    <input type="text" name="title" class="form-control" required>
</div>

<div class="form-group">
    <label>Nội dung</label>
    <textarea name="message" class="form-control" rows="6" required></textarea>
</div>

<div class="form-group">
    <label>Mức độ</label>
    <select name="priority" class="form-select">
        <option value="normal">Bình thường</option>
        <option value="important">Ưu tiên</option>
    </select>
</div>

<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="is_pinned" value="1">
    <label class="form-check-label">Ghim thông báo</label>
</div>

<button type="submit" class="btn btn-gradient-primary">Gửi thông báo</button>
<a href="ListThongBao.php" class="btn btn-light">Hủy</a>

</form>

</div>
</div>
</div>
</div>

</div>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/footer.php"; ?>
</div>
</div>
