<?php
session_start();
include(__DIR__ . '/../../../config.php');
if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$error = '';

$sql_lessons = "SELECT l.id, l.title, c.title as course_name 
                FROM lessons l 
                JOIN courses c ON l.course_id = c.id";

if ($role == 'teacher') {
    $sql_lessons .= " WHERE c.teacher_id = $user_id";
}
$sql_lessons .= " ORDER BY c.id DESC, l.id ASC";
$lessons = $conn->query($sql_lessons);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lesson_id = intval($_POST['lesson_id']);
    $file_name = trim($_POST['file_name']);
    if (empty($lesson_id) || empty($file_name) || empty($_FILES['attachment']['name'])) {
        $error = "Vui lÃ²ng nháº­p tÃªn tÃ i liá»u, chá»n bÃ i giáº£ng vÃ  file ÄÃ­nh kÃ¨m!";
    } else {
        if ($_FILES['attachment']['error'] == 0) {
            $target_dir = "../../../uploads/materials/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $new_filename = time() . '_' . uniqid() . '.' . $ext;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
                $db_path = "uploads/materials/" . $new_filename;
                $stmt = $conn->prepare("INSERT INTO lesson_materials (lesson_id, file_path, file_name) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $lesson_id, $db_path, $file_name);

                if ($stmt->execute()) {
                    $_SESSION['status_message'] = "Upload tÃ i liá»u thÃ nh cÃ´ng!";
                    header("Location: ListTaiLieu.php");
                    exit;
                } else {
                    $error = "Lá»i Database: " . $conn->error;
                }
            } else {
                $error = "KhÃ´ng thá» upload file. Kiá»m tra quyá»n ghi thÆ° má»¥c!";
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
                <h3 class="page-title"> Upload TÃ i liá»u </h3>
            </div>
            
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">ThÃ´ng tin tÃ i liá»u</h4>
                            
                            <?php if($error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <form class="forms-sample" method="POST" enctype="multipart/form-data">
                                
                                <div class="form-group">
                                    <label>Thuá»c BÃ i giáº£ng <span class="text-danger">*</span></label>
                                    <select class="form-select" name="lesson_id" required>
                                        <option value="">-- Chá»n bÃ i giáº£ng --</option>
                                        <?php 
                                        $current_course = "";
                                        while($l = $lessons->fetch_assoc()): 
                                            if ($current_course != $l['course_name']) {
                                                if ($current_course != "") echo "</optgroup>";
                                                echo "<optgroup label='" . htmlspecialchars($l['course_name']) . "'>";
                                                $current_course = $l['course_name'];
                                            }
                                        ?>
                                            <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['title']) ?></option>
                                        <?php endwhile; echo "</optgroup>"; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>TÃªn hiá»n thá» (Display Name) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="file_name" placeholder="VD: Slide thuyáº¿t trÃ¬nh chÆ°Æ¡ng 1" required>
                                </div>

                                <div class="form-group">
                                    <label>Chá»n File (PDF, Docx, Zip...) <span class="text-danger">*</span></label>
                                    <input type="file" name="attachment" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-gradient-primary me-2">LÆ°u láº¡i</button>
                                <a href="ListTaiLieu.php" class="btn btn-light">Há»§y</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
    </div>
</div>
