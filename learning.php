<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$lesson_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if ($lesson_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM lessons WHERE id = ?");
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $current_lesson = $stmt->get_result()->fetch_assoc();
    
    if ($current_lesson) {
        $course_id = $current_lesson['course_id'];
    } else {
        die("Bài học không tồn tại!");
    }
} 
elseif ($course_id > 0) {
    // Nếu chỉ truyền course_id, lấy bài đầu tiên
    $sql_first = "SELECT * FROM lessons WHERE course_id = $course_id ORDER BY id ASC LIMIT 1";
    $current_lesson = $conn->query($sql_first)->fetch_assoc();
    
    if ($current_lesson) {
        $lesson_id = $current_lesson['id'];
    } else {
        die("Khóa học này chưa có bài giảng nào! Vui lòng quay lại sau.");
    }
} else {
    header("Location: courses_list.php");
    exit;
}

$is_allowed = false;
$course_info = $conn->query("SELECT title, teacher_id FROM courses WHERE id = $course_id")->fetch_assoc();

if (isset($_SESSION['user_role']) && 
   ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'admins' || $_SESSION['user_id'] == $course_info['teacher_id'])) {
    $is_allowed = true;
} 
else {
    $check_enroll = $conn->query("SELECT id FROM enrollments WHERE user_id = $user_id AND course_id = $course_id");
    if ($check_enroll->num_rows > 0) $is_allowed = true;
}

if (!$is_allowed) {
    die("Bạn chưa mua khóa học này! <a href='course_details.php?id=$course_id'>Quay lại trang đăng ký</a>");
}

$all_lessons = $conn->query("SELECT id, title FROM lessons WHERE course_id = $course_id ORDER BY id ASC");

function getYoutubeId($url) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
    return isset($match[1]) ? $match[1] : null;
}
?>

<?php include 'header.php'; ?>

<style>
    .learning-container { padding: 40px 0; background: #f4f6f8; min-height: 80vh; }
    
    .video-wrapper {
        position: relative;
        padding-bottom: 56.25%; /* Tỉ lệ 16:9 */
        height: 0;
        background: #000;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .video-wrapper iframe, .video-wrapper video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    /* STYLE CHO NỘI DUNG */
    .lesson-main-content { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; }
    .lesson-title { font-weight: 700; color: #333; margin-bottom: 10px; font-size: 1.8rem; }
    
    .attachment-box { 
        background: #e3f2fd; 
        padding: 15px; 
        border-radius: 6px; 
        border-left: 5px solid #2196f3; 
        margin-top: 20px; 
        display: flex; 
        align-items: center;
    }


    .audio-box {
        background: #fff3e0;
        border: 1px solid #ffe0b2;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .playlist-box { background: #fff; border-radius: 8px; border: 1px solid #ddd; overflow: hidden; position: sticky; top: 20px; }
    .playlist-header { background: #343a40; color: #fff; padding: 15px 20px; font-weight: bold; }
    .lesson-list { list-style: none; padding: 0; margin: 0; max-height: 500px; overflow-y: auto; }
    .lesson-list li a {
        display: block;
        padding: 12px 20px;
        border-bottom: 1px solid #eee;
        color: #555;
        text-decoration: none;
        transition: 0.2s;
        font-size: 0.95rem;
    }
    .lesson-list li a:hover { background: #f8f9fa; color: #007bff; }
    
    .lesson-list li a.active {
        background: #e8f5e9; 
        color: #28a745;       
        font-weight: bold;
        border-left: 4px solid #28a745;
    }
    .text-content { font-size: 1rem; line-height: 1.8; color: #333; text-align: justify; }
</style>

<div class="breadcrumbs-custom bg-image context-dark" style="background-color: #222; padding: 20px 0;">
    <div class="container">
        <ul class="breadcrumbs-custom-path text-left">
            <li><a href="profile.php">Khóa học của tôi</a></li>
            <li><a href="course_details.php?id=<?= $course_id ?>"><?= htmlspecialchars($course_info['title']) ?></a></li>
            <li class="active">Bài: <?= htmlspecialchars($current_lesson['title']) ?></li>
        </ul>
    </div>
</div>

<section class="learning-container">
    <div class="container-fluid px-md-5"> 
        <div class="row">
            
            <div class="col-lg-8">
                
                <?php if (!empty($current_lesson['video_url'])): ?>
                <div class="video-wrapper">
                    <?php 
                        $vid_url = $current_lesson['video_url'];
                        
                        if (strpos($vid_url, 'uploads/') !== false) {
                    ?>
                            <video controls controlsList="nodownload" oncontextmenu="return false;">
                                <source src="/Learning/<?= $vid_url ?>" type="video/mp4">
                                Trình duyệt của bạn không hỗ trợ thẻ video.
                            </video>

                    <?php 
                        } else { 
                            // Nếu là YouTube
                            $video_id = getYoutubeId($vid_url);
                            if ($video_id):
                    ?>
                            <iframe src="https://www.youtube.com/embed/<?= $video_id ?>?rel=0&modestbranding=1" 
                                    frameborder="0" allowfullscreen>
                            </iframe>
                    <?php 
                            else:
                                echo '<div class="d-flex justify-content-center align-items-center h-100 text-white">Video không khả dụng</div>';
                            endif;
                        } 
                    ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($current_lesson['audio_url'])): ?>
                    <div class="audio-box">
                        <h5 class="mb-3"><i class="mdi mdi-headphones text-warning"></i> Bài nghe / Podcast</h5>
                        <audio controls style="width: 100%;">
                            <source src="/Learning/<?= $current_lesson['audio_url'] ?>" type="audio/mpeg">
                            Trình duyệt của bạn không hỗ trợ phát âm thanh.
                        </audio>
                    </div>
                <?php endif; ?>


                <div class="lesson-main-content">
                    <h1 class="lesson-title"><?= htmlspecialchars($current_lesson['title']) ?></h1>
                    <hr>

                    <?php if (!empty($current_lesson['attachment'])): ?>
                        <div class="attachment-box">
                            <i class="mdi mdi-paperclip" style="font-size: 24px; margin-right: 15px;"></i>
                            <div>
                                <strong>Tài liệu đính kèm:</strong><br>
                                <a href="/Learning/<?= $current_lesson['attachment'] ?>" target="_blank" class="text-primary text-decoration-none">
                                    <i class="mdi mdi-download"></i> Tải xuống tài liệu
                                </a>
                            </div>
                        </div>
                        <div class="mb-4"></div>
                    <?php endif; ?>

                    <?php if (!empty($current_lesson['content'])): ?>
                        <div class="text-content">
                            <?= html_entity_decode($current_lesson['content']) ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center mt-3"><i>(Bài học này không có nội dung văn bản)</i></p>
                    <?php endif; ?>

                    <div class="mt-4 text-end">
                    <button onclick="alert('Đã lưu tiến độ học tập!')" 
                        style="border: 2px solid #4CAF50; 
                            border-radius: 5px; 
                            padding: 10px 20px;
                            background-color: transparent; 
                            color: #4CAF50; 
                            font-weight: bold; 
                            cursor: pointer; 
                            display: flex;
                            align-items: center; 
                            gap: 5px;">
                        Hoàn thành bài học <i class="mdi mdi-check-circle"></i>
                    </button>
                    </div>
                </div>

                <div class="mt-4">
                    <h4 class="mb-3">Thảo luận & Hỏi đáp</h4>
                    <?php include 'comments.php'; ?>
                </div>

            </div>
            <div class="col-lg-4 d-none d-lg-block">
                <div class="playlist-box">
                    <div class="playlist-header">
                        <i class="mdi mdi-format-list-bulleted"></i> Nội dung khóa học
                    </div>
                    <ul class="lesson-list">
                        <?php 
                        $i = 1;
                        if ($all_lessons->num_rows > 0):
                            while ($l = $all_lessons->fetch_assoc()): 
                                $isActive = ($l['id'] == $lesson_id) ? 'active' : '';
                        ?>
                            <li>
                                <a href="learning.php?id=<?= $l['id'] ?>" class="<?= $isActive ?>">
                                    <i class="<?= ($isActive) ? 'mdi mdi-play-circle' : 'mdi mdi-play-circle-outline' ?>"></i> 
                                    Bài <?= $i++ ?>: <?= htmlspecialchars($l['title']) ?>
                                </a>
                            </li>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                            <li class="p-3 text-muted">Chưa có bài học nào.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            </div>
    </div>
</section>

<?php include 'footer.php'; ?>