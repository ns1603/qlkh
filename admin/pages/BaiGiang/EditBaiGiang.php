<?php
session_start();
include(__DIR__ . '/../../../config.php');

// 1. CHECK QUYá»N
if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$error = '';

// =================================================================================
// 2. Láº¤Y THÃNG TIN BÃI GIáº¢NG (QUAN TRá»NG: PHáº¢I Äáº¶T TRÆ¯á»C PHáº¦N Xá»¬ LÃ POST)
// =================================================================================
$sql = "SELECT lessons.*, courses.teacher_id 
        FROM lessons 
        JOIN courses ON lessons.course_id = courses.id 
        WHERE lessons.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$lesson = $stmt->get_result()->fetch_assoc();

// Náº¿u khÃ´ng tÃ¬m tháº¥y bÃ i giáº£ng -> Dá»«ng luÃ´n
if (!$lesson) {
    die("BÃ i giáº£ng khÃ´ng tá»n táº¡i!");
}

// Check quyá»n sá» há»¯u (náº¿u lÃ  Teacher)
if ($role == 'teacher' && $lesson['teacher_id'] != $user_id) {
    die("Báº¡n khÃ´ng cÃ³ quyá»n sá»­a bÃ i giáº£ng nÃ y!");
}

// Láº¥y danh sÃ¡ch khÃ³a há»c Äá» hiá»n thá» select box
$sql_courses = "SELECT id, title FROM courses";
if ($role == 'teacher') $sql_courses .= " WHERE teacher_id = $user_id";
$courses = $conn->query($sql_courses);

// XÃ¡c Äá»nh loáº¡i video hiá»n táº¡i Äá» hiá»n thá» ra form (Youtube hay File)
$current_video_url = $lesson['video_url'];
$is_file_video = (strpos($current_video_url, 'uploads/videos/') !== false);
$video_type_default = $is_file_video ? 'file' : 'youtube';


// =================================================================================
// 3. Xá»¬ LÃ FORM Cáº¬P NHáº¬T (KHI Báº¤M NÃT LÆ¯U)
// =================================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $course_id = intval($_POST['course_id']);
    $video_type = $_POST['video_type']; // youtube hoáº·c file
    
    // --- A. Xá»¬ LÃ VIDEO ---
    $new_video_url = $lesson['video_url']; // Máº·c Äá»nh giá»¯ nguyÃªn link cÅ© (Láº¥y tá»« biáº¿n $lesson ÄÃ£ query á» trÃªn)

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
                    // Xóa file video cÅ© náº¿u cÃ³
                    if ($is_file_video && file_exists("../../../" . $lesson['video_url'])) {
                        unlink("../../../" . $lesson['video_url']);
                    }
                    $new_video_url = 'uploads/videos/' . $new_name;
                }
            } else {
                $error = "Äá»nh dáº¡ng video khÃ´ng há»£p lá».";
            }
        }
    }

    // --- B. Xá»¬ LÃ AUDIO (Sá»­a lá»i dÃ²ng 65 táº¡i ÄÃ¢y) ---
    $audio_path = $lesson['audio_url']; // Láº¥y tá»« biáº¿n $lesson ÄÃ£ query á» trÃªn
    
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] == 0) {
        $allowed_audio = ['mp3', 'wav', 'ogg', 'm4a'];
        $ext_audio = strtolower(pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext_audio, $allowed_audio)) {
            $dir_audio = "../../../uploads/audio/";
            if (!file_exists($dir_audio)) mkdir($dir_audio, 0777, true);
            
            $name_audio = 'audio_' . time() . '_' . rand(1000,9999) . '.' . $ext_audio;
            
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $dir_audio . $name_audio)) {
                // Xóa audio cÅ© náº¿u cÃ³
                if (!empty($lesson['audio_url']) && file_exists("../../../" . $lesson['audio_url'])) {
                    unlink("../../../" . $lesson['audio_url']);
                }
                $audio_path = "uploads/audio/" . $name_audio;
            }
        } else {
            $error = "File Ã¢m thanh khÃ´ng ÄÃºng Äá»nh dáº¡ng.";
        }
    }

    // --- C. Xá»¬ LÃ TÃI LIá»U ---
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
        // Cáº­p nháº­t Äá»§ cÃ¡c trÆ°á»ng: video, audio, text (content), attachment
        $sql_update = "UPDATE lessons SET course_id=?, title=?, video_url=?, audio_url=?, content=?, attachment=? WHERE id=?";
        $stmt_up = $conn->prepare($sql_update);
        $stmt_up->bind_param("isssssi", $course_id, $title, $new_video_url, $audio_path, $content, $attachment_path, $id);

        if ($stmt_up->execute()) {
            $_SESSION['status_message'] = "Cáº­p nháº­t bÃ i giáº£ng thÃ nh cÃ´ng!";
            header("Location: ListBaiGiang.php");
            exit;
        } else {
            $error = "Lá»i Database: " . $conn->error;
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
                <h3 class="page-title"> Sá»­a BÃ i giáº£ng </h3>
            </div>
            
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

                            <form class="forms-sample" method="POST" enctype="multipart/form-data">
                                
                                <div class="form-group">
                                    <label>KhÃ³a há»c</label>
                                    <select class="form-select" name="course_id">
                                        <?php while($c = $courses->fetch_assoc()): ?>
                                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $lesson['course_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($c['title']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>TiÃªu Äá»</label>
                                    <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($lesson['title']) ?>" required>
                                </div>

                                <div class="form-group border p-3 rounded bg-light">
                                    <label class="font-weight-bold">Video bÃ i giáº£ng</label>
                                    
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
                                                <i class="mdi mdi-check-circle"></i> Äang dÃ¹ng video: 
                                                <a href="<?= BASE_PATH ?>/<?= $lesson['video_url'] ?>" target="_blank">Xem video hiá»n táº¡i</a>
                                            </div>
                                        <?php endif; ?>
                                        <label>Chá»n video má»i Äá» thay tháº¿:</label>
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
                                            <div class="small text-muted">File hiá»n táº¡i. Upload má»i sáº½ thay tháº¿.</div>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="audio_file" accept=".mp3,.wav,.ogg,.m4a">
                                </div>

                                <div class="form-group mt-3">
                                    <label>File ÄÃ­nh kÃ¨m</label>
                                    <input type="file" name="attachment" class="form-control mb-2">
                                    <?php if(!empty($lesson['attachment'])): ?>
                                        <small class="text-muted">Hiá»n táº¡i: <a href="<?= BASE_PATH ?>/<?= $lesson['attachment'] ?>" target="_blank">Xem file cÅ©</a></small>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Ná»i dung vÄn báº£n (Text)</label>
                                    <textarea class="form-control" name="content" rows="10"><?= htmlspecialchars($lesson['content']) ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-gradient-warning me-2">Cáº­p nháº­t</button>
                                <a href="ListBaiGiang.php" class="btn btn-light">Há»§y</a>
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
    // Báº­t CKEditor cho textarea
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
