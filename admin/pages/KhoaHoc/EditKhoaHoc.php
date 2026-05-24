<?php
session_start();
include(__DIR__ . '/../../../config.php');

// 1. KIỂM TRA QUYỀN HẠN
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'teacher')) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION['user_role'] == 'admins') {
    die("Bạn không có quyền thực hiện hành động này!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// 2. LẤY DỮ LIỆU KHÓA HỌC
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

// Nếu không tìm thấy khóa học hoặc Teacher cố sửa bài của người khác
if (!$course) {
    die("Khóa học không tồn tại!");
}
if ($user_role == 'teacher' && $course['teacher_id'] != $user_id) {
    die("Bạn không có quyền chỉnh sửa khóa học này!");
}

// 3. LẤY DANH SÁCH DANH MỤC (Cho dropdown)
$categories = $conn->query("SELECT * FROM categories");

// 4. LẤY DANH SÁCH GIẢNG VIÊN (Chỉ Admin mới cần chọn GV, Teacher thì cố định chính họ)
$teachers = null;
if ($user_role == 'admins' || $user_role == 'admin') {
    // Lấy list user có role là teacher hoặc admin
    $teachers = $conn->query("SELECT id, fullname FROM users WHERE role IN ('teacher', 'admin', 'admins')");
}
?>

<?php 
include ROOT_PATH . "/admin/header.php"; 
include ROOT_PATH . "/admin/navbar.php"; 
?>

<div class="container-fluid page-body-wrapper">
    <?php include ROOT_PATH . "/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            
            <div class="page-header">
                <h3 class="page-title"> Chỉnh sửa Khóa học </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="ListKhoaHoc.php">Danh sách</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sửa khóa học #<?= $id ?></li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Thông tin khóa học</h4>
                            <p class="card-description"> Vui lòng điền đầy đủ thông tin bên dưới </p>
                            
                            <form class="forms-sample" action="UpdateKhoaHoc.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?= $course['id'] ?>">

                                <div class="form-group">
                                    <label for="title">Tên Khóa Học <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?= htmlspecialchars($course['title']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="category_id">Danh mục</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        <?php while($cat = $categories->fetch_assoc()): ?>
                                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $course['category_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <?php if($teachers): ?>
                                <div class="form-group">
                                    <label for="teacher_id">Giảng viên phụ trách</label>
                                    <select class="form-select" id="teacher_id" name="teacher_id">
                                        <?php while($t = $teachers->fetch_assoc()): ?>
                                            <option value="<?= $t['id'] ?>" <?= $t['id'] == $course['teacher_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($t['fullname']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <?php else: ?>
                                    <input type="hidden" name="teacher_id" value="<?= $user_id ?>">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="price">Học phí (VNĐ)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="price" name="price" 
                                               value="<?= intval($course['price']) ?>" min="0">
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Ảnh đại diện (Thumbnail)</label>
                                    <input type="file" name="thumbnail" class="file-upload-default" id="uploadFile" style="display:none">
                                    
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled placeholder="Chọn ảnh mới nếu muốn thay đổi">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-gradient-primary" type="button" onclick="document.getElementById('uploadFile').click()">Tải ảnh lên</button>
                                        </span>
                                    </div>

                                    <div class="mt-3">
                                        <label>Ảnh hiện tại:</label><br>
                                        <?php if(!empty($course['thumbnail'])): ?>
                                            <img src="<?= BASE_PATH ?>/<?= $course['thumbnail'] ?>" alt="old_thumb" 
                                                 style="width: 150px; height: 100px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Chưa có ảnh</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Mô tả chi tiết</label>
                                    <textarea class="form-control" id="description" name="description" rows="6" 
                                              placeholder="Nhập nội dung giới thiệu khóa học..."><?= htmlspecialchars($course['description']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="status">Trạng thái</label>
                                    <select class="form-select" name="status">
                                        <option value="draft" <?= $course['status']=='draft'?'selected':'' ?>>Bản nháp (Draft)</option>
                                        <option value="published" <?= $course['status']=='published'?'selected':'' ?>>Công khai (Published)</option>
                                        <option value="archived" <?= $course['status']=='archived'?'selected':'' ?>>Lưu trữ (Archived)</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-gradient-primary me-2">Lưu thay đổi</button>
                                <a href="ListKhoaHoc.php" class="btn btn-light">Hủy bỏ</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>

<script>
    document.getElementById('uploadFile').onchange = function () {
        document.querySelector('.file-upload-info').value = this.files[0].name;
    };
</script>
