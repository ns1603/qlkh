<?php
session_start();
include(__DIR__ . '/../../../config.php');

// 1. KIá»M TRA QUYá»N Háº N
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'teacher')) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// 2. Láº¤Y Dá»® LIá»U KHÃA Há»C
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

// Náº¿u khÃ´ng tÃ¬m tháº¥y khÃ³a há»c hoáº·c Teacher cá» sá»­a bÃ i cá»§a ngÆ°á»i khÃ¡c
if (!$course) {
    die("KhÃ³a há»c khÃ´ng tá»n táº¡i!");
}
if ($user_role == 'teacher' && $course['teacher_id'] != $user_id) {
    die("Báº¡n khÃ´ng cÃ³ quyá»n chá»nh sá»­a khÃ³a há»c nÃ y!");
}

// 3. Láº¤Y DANH SÃCH DANH Má»¤C (Cho dropdown)
$categories = $conn->query("SELECT * FROM categories");

// 4. Láº¤Y DANH SÃCH GIáº¢NG VIÃN (Chá» Admin má»i cáº§n chá»n GV, Teacher thÃ¬ cá» Äá»nh chÃ­nh há»)
$teachers = null;
if ($user_role == 'admins' || $user_role == 'admin') {
    // Láº¥y list user cÃ³ role lÃ  teacher hoáº·c admin
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
                <h3 class="page-title"> Chá»nh sá»­a KhÃ³a há»c </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="ListKhoaHoc.php">Danh sÃ¡ch</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sá»­a khÃ³a há»c #<?= $id ?></li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">ThÃ´ng tin khÃ³a há»c</h4>
                            <p class="card-description"> Vui lÃ²ng Äiá»n Äáº§y Äá»§ thÃ´ng tin bÃªn dÆ°á»i </p>
                            
                            <form class="forms-sample" action="UpdateKhoaHoc.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?= $course['id'] ?>">

                                <div class="form-group">
                                    <label for="title">TÃªn KhÃ³a Há»c <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?= htmlspecialchars($course['title']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="category_id">Danh má»¥c</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">-- Chá»n danh má»¥c --</option>
                                        <?php while($cat = $categories->fetch_assoc()): ?>
                                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $course['category_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <?php if($teachers): ?>
                                <div class="form-group">
                                    <label for="teacher_id">Giáº£ng viÃªn phá»¥ trÃ¡ch</label>
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
                                    <label for="price">Há»c phÃ­ (VNÄ)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="price" name="price" 
                                               value="<?= intval($course['price']) ?>" min="0">
                                        <span class="input-group-text">VNÄ</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>áº¢nh Äáº¡i diá»n (Thumbnail)</label>
                                    <input type="file" name="thumbnail" class="file-upload-default" id="uploadFile" style="display:none">
                                    
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled placeholder="Chá»n áº£nh má»i náº¿u muá»n thay Äá»i">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-gradient-primary" type="button" onclick="document.getElementById('uploadFile').click()">Táº£i áº£nh lÃªn</button>
                                        </span>
                                    </div>

                                    <div class="mt-3">
                                        <label>áº¢nh hiá»n táº¡i:</label><br>
                                        <?php if(!empty($course['thumbnail'])): ?>
                                            <img src="<?= BASE_PATH ?>/<?= $course['thumbnail'] ?>" alt="old_thumb" 
                                                 style="width: 150px; height: 100px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">
                                        <?php else: ?>
                                            <span class="badge badge-secondary">ChÆ°a cÃ³ áº£nh</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">MÃ´ táº£ chi tiáº¿t</label>
                                    <textarea class="form-control" id="description" name="description" rows="6" 
                                              placeholder="Nháº­p ná»i dung giá»i thiá»u khÃ³a há»c..."><?= htmlspecialchars($course['description']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="status">Tráº¡ng thÃ¡i</label>
                                    <select class="form-select" name="status">
                                        <option value="draft" <?= $course['status']=='draft'?'selected':'' ?>>Báº£n nhÃ¡p (Draft)</option>
                                        <option value="published" <?= $course['status']=='published'?'selected':'' ?>>CÃ´ng khai (Published)</option>
                                        <option value="archived" <?= $course['status']=='archived'?'selected':'' ?>>LÆ°u trá»¯ (Archived)</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-gradient-primary me-2">LÆ°u thay Äá»i</button>
                                <a href="ListKhoaHoc.php" class="btn btn-light">Há»§y bá»</a>
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
