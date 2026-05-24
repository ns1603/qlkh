<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    $_SESSION['status_message'] = "Không tìm thấy danh mục!";
    header("Location: ListDanhMuc.php");
    exit;
}

function create_slug($string) {
    $search = array('à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ', 'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ', 'ì', 'í', 'ị', 'ỉ', 'ĩ', 'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ', 'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ', 'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ', 'đ', 'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ', 'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ', 'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ', 'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ', 'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ', 'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ', 'Đ', ' ');
    $replace = array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'y', 'y', 'y', 'y', 'y', 'd', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'Y', 'Y', 'Y', 'Y', 'Y', 'D', '-');
    $string = str_replace($search, $replace, $string);
    $string = strtolower($string);
    return preg_replace('/[^a-z0-9\-]/', '', $string);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $slug = create_slug($name);

    if (empty($name)) {
        $error = "Tên danh mục không được để trống!";
    } else {
        $updateStmt = $conn->prepare("UPDATE categories SET name=?, slug=?, description=? WHERE id=?");
        $updateStmt->bind_param("sssi", $name, $slug, $description, $id);

        if ($updateStmt->execute()) {
            $_SESSION['status_message'] = "Cập nhật danh mục thành công!";
            header("Location: ListDanhMuc.php");
            exit;
        } else {
            $error = "Lỗi cập nhật: " . $conn->error;
        }
    }
}
?>

<?php 
include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/header.php"; 
include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/navbar.php"; 
?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Chỉnh sửa Danh mục </h3>
            </div>
            <div class="row">
                <div class="col-md-8 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Sửa thông tin: <?= htmlspecialchars($category['name']) ?></h4>
                            
                            <?php if($error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <form class="forms-sample" method="POST">
                                <div class="form-group">
                                    <label for="name">Tên Danh mục</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= htmlspecialchars($category['name']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Mô tả</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($category['description']) ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-gradient-warning me-2">Cập nhật</button>
                                <a href="ListDanhMuc.php" class="btn btn-light">Hủy bỏ</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/footer.php"; ?>
    </div>
</div>