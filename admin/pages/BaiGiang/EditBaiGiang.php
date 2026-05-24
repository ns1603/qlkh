<?php
session_start();
include(__DIR__ . '/../../../config.php');

// 1. CHECK QUYỀN
if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION['user_role'] == 'admins') {
    die("Bạn không có quyền thực hiện hành động này!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$error = '';

// =================================================================================
// 2. LẤY THÔNG TIN BÀI GIẢNG (QUAN TRỌNG: PHẢI ĐẶT TRƯỚC PHẦN XỬ LÝ POST)
// =================================================================================
$sql = "SELECT lessons.*, courses.teacher_id 
        FROM lessons 
        JOIN courses ON lessons.course_id = courses.id 
        WHERE lessons.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$lesson = $stmt->get_result()->fetch_assoc();

// Nếu không tìm thấy bài giảng -> Dừng luôn
if (!$lesson) {
    die("Bài giảng không tồn tại!");
}

// Check quyền sở hữu (nếu là Teacher)
if ($role == 'teacher' && $lesson['teacher_id'] != $user_id) {
    die("Bạn không có quyền sửa bài giảng này!");
}

// Lấy danh sách khóa học để hiển thị select box
$sql_courses = "SELECT id, title FROM courses";
if ($role == 'teacher') $sql_courses .= " WHERE teacher_id = $user_id";
$courses = $conn->query($sql_courses);

// Xác định loại video hiện tại để hiển thị ra form (Youtube hay File)
$current_video_url = $lesson['video_url'];
$is_file_video = (strpos($current_video_url, 'uploads/videos/') !== false);
$video_type_default = $is_file_video ? 'file' : 'youtube';


// =================================================================================
// 3. XỬ LÝ FORM CẬP NHẬT (KHI BẤM NÚT LƯU)
// =================================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $course_id = intval($_POST['course_id']);
    $video_type = $_POST['video_type']; // youtube hoặc file
    
    // --- A. XỬ LÝ VIDEO ---
    $new_video_url = $lesson['video_url']; // Mặc định giữ nguyên link cũ (Lấy từ biến $lesson đã query ở trên)

    if ($video_type == 'youtube') {
        $new_video_url = trim($_POST['youtube_url']);
    } 
    elseif ($video_type == 'file') {
        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0) {
            $allowed = ['mp4', 'webm', 'ogg'];
            $filename = $_FILES['video_file']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $target_dir = "../../../uploads/videos/";
                if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                
                $new_name = time() . '_' . rand(1000,9999) . '.' . $ext;
                
                if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target_dir . $new_name)) {
                    // Xóa file video cũ nếu có
                    if ($is_file_video && file_exists("../../../" . $lesson['video_url'])) {
                        unlink("../../../" . $lesson['video_url']);
                    }
                    $new_video_url = 'uploads/videos/' . $new_name;
                }
            } else {
                $error = "Định dạng video không hợp lệ.";
            }
        }
    }

    // --- B. XỬ LÝ AUDIO (Sửa lỗi dòng 65 tại đây) ---
    $audio_path = $lesson['audio_url']; // Lấy từ biến $lesson đã query ở trên
    
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] == 0) {
        $allowed_audio = ['mp3', 'wav', 'ogg', 'm4a'];
        $ext_audio = strtolower(pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext_audio, $allowed_audio)) {
            $dir_audio = "../../../uploads/audio/";
            if (!file_exists($dir_audio)) mkdir($dir_audio, 0777, true);
            
            $name_audio = 'audio_' . time() . '_' . rand(1000,9999) . '.' . $ext_audio;
            
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $dir_audio . $name_audio)) {
                // Xóa audio cũ nếu có
                if (!empty($lesson['audio_url']) && file_exists("../../../" . $lesson['audio_url'])) {
                    unlink("../../../" . $lesson['audio_url']);
                }
                $audio_path = "uploads/audio/" . $name_audio;
            }
        } else {
            $error = "File âm thanh không đúng định dạng.";
        }
    }

    // --- C. XỬ LÝ TÀI LIỆU ---
    $attachment_path = $lesson['attachment'];
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $target_dir_mat = "../../../uploads/materials/";
        if (!file_exists($target_dir_mat)) mkdir($target_dir_mat, 0777, true);
        
        $file_ext = pathinfo($_FILES["attachment"]["name"], PATHINFO_EXTENSION);
        $new_name_mat = uniqid() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_dir_mat . $new_name_mat)) {
            if (!empty($lesson['attachment']) && file_exists("../../../" . $lesson['attachment'])) {
                unlink("../../../" . $lesson['attachment']);
            }
            $attachment_path = "uploads/materials/" . $new_name_mat;
        }
    }

    // --- D. UPDATE DATABASE ---
    if (empty($error)) {
        // Cập nhật đủ các trường: video, audio, text (content), attachment
        $sql_update = "UPDATE lessons SET course_id=?, title=?, video_url=?, audio_url=?, content=?, attachment=? WHERE id=?";
        $stmt_up = $conn->prepare($sql_update);
        $stmt_up->bind_param("isssssi", $course_id, $title, $new_video_url, $audio_path, $content, $attachment_path, $id);

        if ($stmt_up->execute()) {
            $_SESSION['status_message'] = "Cập nhật bài giảng thành công!";
            header("Location: ListBaiGiang.php");
            exit;
        } else {
            $error = "Lỗi Database: " . $conn->error;
        }
    }
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
                <h3 class="page-title"> Sửa Bài giảng </h3>
            </div>
            
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

                            <form class="forms-sample" method="POST" enctype="multipart/form-data">
                                
                                <div class="form-group">
                                    <label>Khóa học</label>
                                    <select class="form-select" name="course_id">
                                        <?php while($c = $courses->fetch_assoc()): ?>
                                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $lesson['course_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($c['title']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tiêu đề</label>
                                    <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($lesson['title']) ?>" required>
                                </div>

                                <div class="form-group border p-3 rounded bg-light">
                                    <label class="font-weight-bold">Video bài giảng</label>
                                    
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="video_type" value="youtube" <?= ($video_type_default == 'youtube') ? 'checked' : '' ?> onchange="toggleVideoInput()">
                                            Link YouTube
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="video_type" value="file" <?= ($video_type_default == 'file') ? 'checked' : '' ?> onchange="toggleVideoInput()">
                                            Upload File Video (MP4)
                                        </label>
                                    </div>

                                    <div class="mt-3" id="input_youtube" style="<?= ($video_type_default == 'youtube') ? 'display:block' : 'display:none' ?>">
                                        <label>Link YouTube:</label>
                                        <input type="url" class="form-control" name="youtube_url" 
                                               value="<?= (!$is_file_video) ? htmlspecialchars($lesson['video_url']) : '' ?>"
                                               placeholder="https://www.youtube.com/watch?v=...">
                                    </div>

                                    <div class="mt-3" id="input_file" style="<?= ($video_type_default == 'file') ? 'display:block' : 'display:none' ?>">
                                        <?php if ($is_file_video): ?>
                                            <div class="alert alert-info py-2">
                                                <i class="mdi mdi-check-circle"></i> Đang dùng video: 
                                                <a href="<?= BASE_PATH ?>/<?= $lesson['video_url'] ?>" target="_blank">Xem video hiện tại</a>
                                            </div>
                                        <?php endif; ?>
                                        <label>Chọn video mới để thay thế:</label>
                                        <input type="file" class="form-control" name="video_file" accept="video/mp4,video/*">
                                    </div>
                                </div>

                                <div class="form-group border p-3 bg-light mt-3">
                                    <label class="font-weight-bold">Audio / Podcast</label>
                                    <?php if(!empty($lesson['audio_url'])): ?>
                                        <div class="mb-2">
                                            <audio controls>
                                                <source src="<?= BASE_PATH ?>/<?= $lesson['audio_url'] ?>">
                                            </audio>
                                            <div class="small text-muted">File hiện tại. Upload mới sẽ thay thế.</div>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="audio_file" accept=".mp3,.wav,.ogg,.m4a">
                                </div>

                                <div class="form-group mt-3">
                                    <label>File đính kèm</label>
                                    <input type="file" name="attachment" class="form-control mb-2">
                                    <?php if(!empty($lesson['attachment'])): ?>
                                        <small class="text-muted">Hiện tại: <a href="<?= BASE_PATH ?>/<?= $lesson['attachment'] ?>" target="_blank">Xem file cũ</a></small>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Nội dung văn bản (Text)</label>
                                    <textarea class="form-control" name="content" rows="10"><?= htmlspecialchars($lesson['content']) ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-gradient-warning me-2">Cập nhật</button>
                                <a href="ListBaiGiang.php" class="btn btn-light">Hủy</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    // Bật CKEditor cho textarea
    CKEDITOR.replace('content');

    function toggleVideoInput() {
        var videoType = document.querySelector('input[name="video_type"]:checked').value;
        var youtubeInput = document.getElementById('input_youtube');
        var fileInput = document.getElementById('input_file');

        if (videoType === 'youtube') {
            youtubeInput.style.display = 'block';
            fileInput.style.display = 'none';
        } else {
            youtubeInput.style.display = 'none';
            fileInput.style.display = 'block';
        }
    }
</script>
