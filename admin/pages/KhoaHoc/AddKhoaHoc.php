<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'teacher')) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION['user_role'] == 'admins') {
    die("Bạn không có quyền thực hiện hành động này!");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$error = '';
$categories = $conn->query("SELECT * FROM categories");

$teachers = null;
if ($role == 'admins' || $role == 'admin') {
    $teachers = $conn->query("SELECT id, fullname FROM users WHERE role IN ('teacher', 'admin', 'admins')");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $status = $_POST['status'];

    $teacher_id = ($role == 'teacher') ? $user_id : intval($_POST['teacher_id']);
    $cat_raw = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $category_id = ($cat_raw > 0) ? $cat_raw : NULL;

    if (empty($title)) {
        $error = "Vui lòng nhập tên khóa học!";
    } else {
        $slug = 'course-' . time() . '-' . rand(100, 999);
        $thumbnail_path = "";
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
            $target_dir = "../../../uploads/courses/";
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
            
            $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
            $new_name = $slug . '.' . $ext;
            
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_dir . $new_name)) {
                $thumbnail_path = "uploads/courses/" . $new_name;
            }
        }
        $sql = "INSERT INTO courses (title, slug, description, price, status, teacher_id, category_id, thumbnail) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssdsiis", $title, $slug, $description, $price, $status, $teacher_id, $category_id, $thumbnail_path);

            if ($stmt->execute()) {
                $_SESSION['status_message'] = "Thêm khóa học thành công!";
                echo "<script>window.location.href='ListKhoaHoc.php';</script>";
                exit;
            } else {
                $error = "Lỗi thực thi SQL: " . $stmt->error;
            }
        } else {
            $error = "Lỗi chuẩn bị SQL: " . $conn->error;
        }
    }
}
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/header.php"; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Thêm Khóa học mới </h3>
            </div>
            
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            
                            <?php if($error): ?>
                                <div class="alert alert-danger" role="alert">
                                    <strong>Có lỗi xảy ra:</strong> <?= $error ?>
                                </div>
                            <?php endif; ?>

                            <form class="forms-sample" method="POST" enctype="multipart/form-data">
                                
                                <div class="form-group">
                                    <label>Tên Khóa Học <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" required placeholder="VD: Lập trình PHP căn bản">
                                </div>

                                <div class="form-group">
                                    <label>Danh mục</label>
                                    <select class="form-select" name="category_id">
                                        <option value="0">-- Không chọn --</option>
                                        <?php if($categories): ?>
                                            <?php while($cat = $categories->fetch_assoc()): ?>
                                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <?php if ($teachers): ?>
                                <div class="form-group">
                                    <label>Giảng viên phụ trách</label>
                                    <select class="form-select" name="teacher_id">
                                        <?php while($t = $teachers->fetch_assoc()): ?>
                                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['fullname']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <label>Học phí (VNĐ)</label>
                                    <input type="number" class="form-control" name="price" value="0" min="0">
                                </div>

                                <div class="form-group">
                                    <label>Ảnh đại diện (Thumbnail)</label>
                                    <input type="file" class="form-control" name="thumbnail">
                                </div>

                                <div class="form-group">
                                    <label>Mô tả chi tiết</label>
                                    <textarea class="form-control" name="description" rows="5"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select class="form-select" name="status">
                                        <option value="draft">Bản nháp</option>
                                        <option value="published">Công khai</option>
                                        <option value="archived">Lưu trữ</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-gradient-primary me-2">Lưu Khóa Học</button>
                                <a href="ListKhoaHoc.php" class="btn btn-light">Hủy</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
    </div>
</div>
