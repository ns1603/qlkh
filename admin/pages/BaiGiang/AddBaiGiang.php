<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

if ($_SESSION['user_role'] == 'admins') {
    die("Bạn không có quyền thực hiện hành động này!");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$error = '';

// Lấy danh sách khóa học
$sql_courses = "SELECT id, title FROM courses";
if ($role == 'teacher') { $sql_courses .= " WHERE teacher_id = $user_id"; }
$courses = $conn->query($sql_courses);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = intval($_POST['course_id']);
    
    // Check quyền sở hữu khóa học (nếu là Teacher)
    if ($role == 'teacher') {
        $check = $conn->query("SELECT id FROM courses WHERE id = $course_id AND teacher_id = $user_id");
        if ($check->num_rows === 0) {
            die("Bạn không có quyền đăng bài vào khóa học này!");
        }
    }

    $title = trim($_POST['title']);
    $content = trim($_POST['content']); // Đây chính là phần TEXT
    $video_type = $_POST['video_type'];
    $video_url = '';
    $audio_url = ''; // Biến lưu đường dẫn Audio

    if (empty($title) || empty($course_id)) {
        $error = "Vui lòng nhập tên bài giảng!";
    } else {
        // 1. XỬ LÝ VIDEO (Giữ nguyên logic cũ)
        if ($video_type == 'youtube') {
            $video_url = trim($_POST['youtube_url']);
        } elseif ($video_type == 'file') {
            if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0) {
                $ext = strtolower(pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['mp4', 'webm', 'ogg'])) {
                    $target_dir = "../../../uploads/videos/";
                    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                    $new_name = time() . '_' . rand(1000,9999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target_dir . $new_name)) {
                        $video_url = 'uploads/videos/' . $new_name;
                    }
                }
            }
        }

        // 2. XỬ LÝ AUDIO (Mới thêm)
        if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] == 0) {
            $allowed_audio = ['mp3', 'wav', 'ogg', 'm4a'];
            $ext_audio = strtolower(pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION));
            
            if (in_array($ext_audio, $allowed_audio)) {
                $dir_audio = "../../../uploads/audio/";
                if (!file_exists($dir_audio)) mkdir($dir_audio, 0777, true);
                
                $name_audio = 'audio_' . time() . '_' . rand(1000,9999) . '.' . $ext_audio;
                if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $dir_audio . $name_audio)) {
                    $audio_url = 'uploads/audio/' . $name_audio;
                }
            } else {
                $error = "File âm thanh không hợp lệ (Chỉ hỗ trợ MP3, WAV, OGG)";
            }
        }

        // 3. XỬ LÝ TÀI LIỆU (Giữ nguyên)
        $attachment = '';
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
            $target_dir = "../../../uploads/materials/";
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
            $new_filename = uniqid() . '.' . pathinfo($_FILES["attachment"]["name"], PATHINFO_EXTENSION);
            if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_dir . $new_filename)) {
                $attachment = "uploads/materials/" . $new_filename;
            }
        }

        // INSERT DB
        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO lessons (course_id, title, video_url, audio_url, content, attachment) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $course_id, $title, $video_url, $audio_url, $content, $attachment);

            if ($stmt->execute()) {
                $_SESSION['status_message'] = "Thêm bài giảng thành công!";
                header("Location: ListBaiGiang.php");
                exit;
            } else {
                $error = "Lỗi Database: " . $conn->error;
            }
        }
    }
}
?>

<?php include ROOT_PATH . "/admin/header.php"; include ROOT_PATH . "/admin/navbar.php"; ?>
<div class="container-fluid page-body-wrapper">
    <?php include ROOT_PATH . "/admin/sidebar.php"; ?>
    <div class="main-panel"><div class="content-wrapper"><div class="row"><div class="col-12 grid-margin stretch-card"><div class="card"><div class="card-body">
        <h4 class="card-title">Thêm Bài Giảng</h4>
        <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Khóa học</label>
                <select class="form-select" name="course_id" required>
                    <?php while($c = $courses->fetch_assoc()): ?><option value="<?= $c['id'] ?>"><?= $c['title'] ?></option><?php endwhile; ?>
                </select>
            </div>
            <div class="form-group"><label>Tên bài</label><input type="text" class="form-control" name="title" required></div>

            <div class="form-group border p-3 bg-light">
                <label class="fw-bold">1. Video bài giảng</label>
                <div class="form-check"><label><input type="radio" name="video_type" value="youtube" checked onchange="toggleVideo()"> Link YouTube</label></div>
                <div class="form-check"><label><input type="radio" name="video_type" value="file" onchange="toggleVideo()"> Upload File Video</label></div>
                <div id="box_youtube"><input type="url" class="form-control" name="youtube_url" placeholder="Link Youtube..."></div>
                <div id="box_file" style="display:none"><input type="file" class="form-control" name="video_file"></div>
            </div>

            <div class="form-group border p-3 bg-light mt-3">
                <label class="fw-bold">2. Audio / Podcast (Tùy chọn)</label>
                <input type="file" class="form-control" name="audio_file" accept=".mp3,.wav,.ogg,.m4a">
                <small class="text-muted">Dùng cho bài nghe, podcast (MP3, WAV).</small>
            </div>

            <div class="form-group mt-3"><label>Tài liệu đính kèm</label><input type="file" name="attachment" class="form-control"></div>
            
            <div class="form-group">
                <label class="fw-bold">3. Nội dung văn bản (Text)</label>
                <textarea class="form-control" name="content" rows="10" placeholder="Nhập nội dung bài đọc, ghi chú..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary me-2">Lưu bài giảng</button>
        </form>
    </div></div></div></div></div>
    <?php include ROOT_PATH . "/admin/footer.php"; ?></div>
</div>

<script>
function toggleVideo() {
    var type = document.querySelector('input[name="video_type"]:checked').value;
    document.getElementById('box_youtube').style.display = (type === 'youtube') ? 'block' : 'none';
    document.getElementById('box_file').style.display = (type === 'file') ? 'block' : 'none';
}
</script>
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

<script>
    // Tìm thẻ textarea có name="content" và biến nó thành bộ soạn thảo xịn
    CKEDITOR.replace('content', {
        height: 300, // Chiều cao khung soạn thảo
        removePlugins: 'resize', // Tắt chức năng kéo giãn
        // Cấu hình thêm nếu muốn (ví dụ cho phép chèn ảnh từ URL)
    });
</script>
