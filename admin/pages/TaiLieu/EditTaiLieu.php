<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

$sql = "SELECT m.*, c.teacher_id 
        FROM lesson_materials m 
        JOIN lessons l ON m.lesson_id = l.id 
        JOIN courses c ON l.course_id = c.id 
        WHERE m.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$material = $stmt->get_result()->fetch_assoc();

if (!$material) die("Tài liệu không tồn tại!");
if ($role == 'teacher' && $material['teacher_id'] != $user_id) die("Không có quyền truy cập!");

$sql_lessons = "SELECT l.id, l.title, c.title as course_name 
                FROM lessons l JOIN courses c ON l.course_id = c.id";
if ($role == 'teacher') $sql_lessons .= " WHERE c.teacher_id = $user_id";
$sql_lessons .= " ORDER BY c.id DESC";
$lessons = $conn->query($sql_lessons);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lesson_id = intval($_POST['lesson_id']);
    $file_name = trim($_POST['file_name']);
    $db_path = $material['file_path'];
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $old_file = $_SERVER['DOCUMENT_ROOT'] . '/qlkh/' . $material['file_path'];
        if (file_exists($old_file)) unlink($old_file);
        $target_dir = "../../../uploads/materials/";
        $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . uniqid() . '.' . $ext;
        
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_dir . $new_filename)) {
            $db_path = "uploads/materials/" . $new_filename;
        }
    }
    $update = $conn->prepare("UPDATE lesson_materials SET lesson_id=?, file_name=?, file_path=? WHERE id=?");
    $update->bind_param("issi", $lesson_id, $file_name, $db_path, $id);

    if ($update->execute()) {
        $_SESSION['status_message'] = "Cập nhật tài liệu thành công!";
        header("Location: ListTaiLieu.php");
        exit;
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
                <h3 class="page-title"> Sửa Tài liệu </h3>
            </div>
            
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <form class="forms-sample" method="POST" enctype="multipart/form-data">
                                
                                <div class="form-group">
                                    <label>Thuộc Bài giảng</label>
                                    <select class="form-select" name="lesson_id">
                                        <?php while($l = $lessons->fetch_assoc()): ?>
                                            <option value="<?= $l['id'] ?>" <?= $l['id'] == $material['lesson_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($l['course_name']) ?> - <?= htmlspecialchars($l['title']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tên hiển thị</label>
                                    <input type="text" class="form-control" name="file_name" value="<?= htmlspecialchars($material['file_name']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>File đính kèm (Để trống nếu không muốn đổi)</label>
                                    <input type="file" name="attachment" class="form-control mb-2">
                                    <small class="text-muted">Đang dùng: <a href="/qlkh/<?= $material['file_path'] ?>" target="_blank">Xem file hiện tại</a></small>
                                </div>

                                <button type="submit" class="btn btn-gradient-warning me-2">Cập nhật</button>
                                <a href="ListTaiLieu.php" class="btn btn-light">Hủy</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
    </div>
</div>
