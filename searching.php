<?php
/**
 * learning.php  – trang xem video bài học cho học viên
 * Đặt tại root project (cùng cấp course_detail.php)
 */
session_start();
include 'config.php';

/* ── Auth: phải đăng nhập ── */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
$user_id = $_SESSION['user_id'];

/* ── Lấy course_id & lesson_id ── */
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$lesson_id = isset($_GET['id'])        ? intval($_GET['id'])        : 0;

/* ── Kiểm tra enroll ── */
$enroll_check = $conn->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
$enroll_check->bind_param("ii", $user_id, $course_id);
$enroll_check->execute();
$enrolled = $enroll_check->get_result()->num_rows > 0;

/* ── Lấy thông tin khóa học ── */
$course = $conn->query("SELECT c.*, u.fullname AS teacher_name 
    FROM courses c LEFT JOIN users u ON c.teacher_id = u.id 
    WHERE c.id = $course_id")->fetch_assoc();

if (!$course) { header("Location: courses_list.php"); exit; }

/* ── Tất cả bài học trong khóa ── */
$all_lessons_res = $conn->query(
    "SELECT * FROM lessons WHERE course_id = $course_id ORDER BY sort_order ASC, id ASC"
);
$all_lessons = [];
while ($r = $all_lessons_res->fetch_assoc()) $all_lessons[] = $r;

if (empty($all_lessons)) { header("Location: course_detail.php?id=$course_id"); exit; }

/* ── Xác định bài học hiện tại ── */
$current = null;
if ($lesson_id) {
    foreach ($all_lessons as $ls) {
        if ($ls['id'] === $lesson_id) { $current = $ls; break; }
    }
}
if (!$current) $current = $all_lessons[0];
$lesson_id = $current['id'];

/* ── Kiểm tra quyền xem bài này ── */
$can_watch = $enrolled || !empty($current['free_preview']);
if (!$can_watch) {
    header("Location: course_detail.php?id=$course_id");
    exit;
}

/* ── Đánh dấu đã xem (progress) ── */
if ($enrolled) {
    $chk = $conn->prepare("SELECT id FROM lesson_progress WHERE user_id=? AND lesson_id=?");
    $chk->bind_param("ii", $user_id, $lesson_id);
    $chk->execute();
    if ($chk->get_result()->num_rows === 0) {
        $ins = $conn->prepare("INSERT IGNORE INTO lesson_progress (user_id, lesson_id, completed_at) VALUES (?,?,NOW())");
        $ins->bind_param("ii", $user_id, $lesson_id);
        $ins->execute();
    }
    /* tỉ lệ hoàn thành */
    $done_res = $conn->query("SELECT COUNT(*) AS cnt FROM lesson_progress WHERE user_id=$user_id AND lesson_id IN (SELECT id FROM lessons WHERE course_id=$course_id)");
    $done_count = $done_res->fetch_assoc()['cnt'];
    $progress_pct = count($all_lessons) > 0 ? round($done_count / count($all_lessons) * 100) : 0;
} else {
    $progress_pct = 0;
}

/* ── Bài trước / sau ── */
$prev_lesson = null; $next_lesson = null;
foreach ($all_lessons as $idx => $ls) {
    if ($ls['id'] == $lesson_id) {
        if ($idx > 0) $prev_lesson = $all_lessons[$idx - 1];
        if ($idx < count($all_lessons) - 1) $next_lesson = $all_lessons[$idx + 1];
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($current['title']) ?> – <?= htmlspecialchars($course['title']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
    --navy:#0f1f3d;--navy2:#1a2f55;
    --blue:#2563eb;--blue2:#1d4ed8;--blue-light:#eff6ff;
    --green:#059669;--green-light:#ecfdf5;
    --red:#dc2626;
    --gray0:#f8fafc;--gray1:#f1f5f9;--gray2:#e2e8f0;--gray3:#cbd5e1;
    --gray5:#64748b;--gray7:#334155;--gray9:#0f172a;
    --sidebar-w:320px;
    --header-h:60px;
    --font:'Plus Jakarta Sans',system-ui,sans-serif;
}
html,body{height:100%;overflow:hidden}
body{font-family:var(--font);color:var(--gray9);font-size:15px;background:#0a0f1a;display:flex;flex-direction:column}

/* ── HEADER ── */
.lrn-header{height:var(--header-h);background:var(--navy);display:flex;align-items:center;padding:0 20px;gap:16px;flex-shrink:0;border-bottom:1px solid rgba(255,255,255,.08);z-index:10}
.lrn-header .logo{font-size:15px;font-weight:800;color:#fff;text-decoration:none;display:flex;align-items:center;gap:8px}
.lrn-header .logo span{color:#60a5fa}
.lrn-course-title{flex:1;font-size:13px;font-weight:600;color:rgba(255,255,255,.7);overflow:hidden;white-space:nowrap;text-overflow:ellipsis}
.progress-pill{display:flex;align-items:center;gap:8px;font-size:12px;color:rgba(255,255,255,.6);flex-shrink:0}
.progress-ring{width:32px;height:32px}
.lrn-header a.back{color:#93c5fd;font-size:12px;text-decoration:none;display:flex;align-items:center;gap:4px;white-space:nowrap}
.lrn-header a.back:hover{color:#fff}

/* ── LAYOUT ── */
.lrn-body{flex:1;display:flex;overflow:hidden}

/* ── VIDEO AREA ── */
.lrn-main{flex:1;display:flex;flex-direction:column;overflow-y:auto;background:#0a0f1a}
.video-wrap{background:#000;width:100%;position:relative}
.video-wrap video{width:100%;display:block;max-height:calc(100vh - var(--header-h) - 200px)}

.lrn-content{background:#fff;padding:28px 32px;flex:1}
.lesson-headline{font-size:20px;font-weight:800;color:var(--gray9);margin-bottom:8px}
.lesson-meta-row{display:flex;align-items:center;gap:16px;font-size:13px;color:var(--gray5);margin-bottom:20px}
.lesson-meta-row .tag{background:var(--blue-light);color:var(--blue);font-size:11px;font-weight:600;padding:3px 10px;border-radius:50px}
.lesson-desc{color:var(--gray5);line-height:1.8;font-size:14.5px}
.lesson-nav{display:flex;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--gray2)}
.nav-btn{display:flex;align-items:center;gap:8px;padding:11px 18px;border-radius:10px;border:1px solid var(--gray2);background:#fff;font-size:14px;font-weight:600;color:var(--gray7);cursor:pointer;font-family:var(--font);text-decoration:none;transition:.15s}
.nav-btn:hover{border-color:var(--blue);color:var(--blue)}
.nav-btn.next{background:var(--blue);color:#fff;border-color:var(--blue);margin-left:auto}
.nav-btn.next:hover{background:var(--blue2)}
.nav-btn:disabled,.nav-btn.disabled{opacity:.4;pointer-events:none}

/* ── SIDEBAR ── */
.lrn-sidebar{width:var(--sidebar-w);background:var(--navy);border-left:1px solid rgba(255,255,255,.07);overflow-y:auto;flex-shrink:0;display:flex;flex-direction:column}
@media(max-width:900px){.lrn-sidebar{display:none}}

.sidebar-head{padding:16px 18px;border-bottom:1px solid rgba(255,255,255,.08);flex-shrink:0}
.sidebar-head h3{font-size:13px;font-weight:700;color:#fff;margin-bottom:10px}
.prog-bar-bg{height:5px;background:rgba(255,255,255,.12);border-radius:3px;overflow:hidden;margin-bottom:6px}
.prog-bar-fill{height:100%;background:linear-gradient(90deg,#00b894,#00cec9);border-radius:3px;transition:width .6s}
.prog-label{font-size:11px;color:rgba(255,255,255,.5)}

.sidebar-list{padding:10px 0;flex:1}
.sl-item{display:flex;align-items:center;gap:10px;padding:11px 18px;cursor:pointer;transition:.15s;text-decoration:none;border-left:3px solid transparent}
.sl-item:hover{background:rgba(255,255,255,.05)}
.sl-item.active{background:rgba(37,99,235,.2);border-left-color:var(--blue)}
.sl-num{width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;background:rgba(255,255,255,.1);color:rgba(255,255,255,.6);flex-shrink:0}
.sl-item.active .sl-num{background:var(--blue);color:#fff}
.sl-item.done .sl-num{background:var(--green);color:#fff}
.sl-info{flex:1;min-width:0}
.sl-title{font-size:13px;font-weight:600;color:rgba(255,255,255,.8);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sl-item.active .sl-title{color:#fff}
.sl-sub{font-size:11px;color:rgba(255,255,255,.35);margin-top:2px}
.sl-lock{font-size:14px;color:rgba(255,255,255,.2)}

/* ── NO VIDEO ── */
.no-video-screen{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:320px;background:#0f172a;color:rgba(255,255,255,.4);gap:14px}
.no-video-screen i{font-size:56px;opacity:.3}
.no-video-screen p{font-size:14px;text-align:center}

/* ── MOBILE LESSON TOGGLE ── */
.mob-lesson-btn{display:none;position:fixed;bottom:20px;right:20px;z-index:50;width:48px;height:48px;border-radius:50%;background:var(--blue);color:#fff;border:none;font-size:22px;cursor:pointer;box-shadow:0 4px 20px rgba(37,99,235,.4)}
@media(max-width:900px){.mob-lesson-btn{display:flex;align-items:center;justify-content:center}}
</style>
</head>
<body>

<!-- HEADER -->
<header class="lrn-header">
    <a href="course_detail.php?id=<?= $course_id ?>" class="back">
        <i class="mdi mdi-arrow-left"></i> Thoát
    </a>
    <div class="lrn-course-title"><?= htmlspecialchars($course['title']) ?></div>

    <?php if ($enrolled): ?>
    <div class="progress-pill">
        <svg class="progress-ring" viewBox="0 0 32 32">
            <circle cx="16" cy="16" r="12" fill="none" stroke="rgba(255,255,255,.15)" stroke-width="3"/>
            <circle cx="16" cy="16" r="12" fill="none" stroke="#00b894" stroke-width="3"
                stroke-dasharray="<?= round(75.4 * $progress_pct / 100) ?> 75.4"
                stroke-linecap="round"
                transform="rotate(-90 16 16)"/>
            <text x="16" y="20" text-anchor="middle" fill="#fff" font-size="8" font-weight="700" font-family="sans-serif"><?= $progress_pct ?>%</text>
        </svg>
        <span><?= $done_count ?>/<?= count($all_lessons) ?> bài</span>
    </div>
    <?php endif; ?>
</header>

<!-- BODY -->
<div class="lrn-body">

    <!-- MAIN -->
    <main class="lrn-main">
        <!-- VIDEO PLAYER -->
        <div class="video-wrap">
            <?php if (!empty($current['video_url'])): ?>
                <video id="mainVideo" controls autoplay preload="metadata"
                    onended="onVideoEnd()"
                    style="width:100%;display:block;max-height:calc(100vh - <?= isset($next_lesson) ? '60px' : '60px' ?> - 120px)">
                    <source src="<?= htmlspecialchars($current['video_url']) ?>" type="video/mp4">
                    <source src="<?= htmlspecialchars($current['video_url']) ?>" type="video/webm">
                    Trình duyệt không hỗ trợ HTML5 video.
                </video>
            <?php else: ?>
                <div class="no-video-screen">
                    <i class="mdi mdi-video-off-outline"></i>
                    <p>Bài học này chưa có video.<br>Vui lòng quay lại sau.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- INFO -->
        <div class="lrn-content">
            <h1 class="lesson-headline"><?= htmlspecialchars($current['title']) ?></h1>
            <div class="lesson-meta-row">
                <span><i class="mdi mdi-book-outline"></i> <?= htmlspecialchars($course['title']) ?></span>
                <span><i class="mdi mdi-account-outline"></i> <?= htmlspecialchars($course['teacher_name'] ?? 'Admin') ?></span>
                <?php if (!empty($current['free_preview'])): ?>
                    <span class="tag">Xem thử miễn phí</span>
                <?php endif; ?>
            </div>

            <?php if (!empty($current['description'])): ?>
                <div class="lesson-desc"><?= nl2br(htmlspecialchars($current['description'])) ?></div>
            <?php endif; ?>

            <!-- NAV -->
            <div class="lesson-nav">
                <?php if ($prev_lesson): ?>
                    <a href="learning.php?course_id=<?= $course_id ?>&id=<?= $prev_lesson['id'] ?>" class="nav-btn">
                        <i class="mdi mdi-chevron-left"></i> Bài trước
                    </a>
                <?php endif; ?>

                <?php if ($next_lesson): ?>
                    <?php $can_next = $enrolled || !empty($next_lesson['free_preview']); ?>
                    <a href="<?= $can_next ? 'learning.php?course_id='.$course_id.'&id='.$next_lesson['id'] : '#' ?>"
                        class="nav-btn next <?= !$can_next ? 'disabled' : '' ?>">
                        Bài tiếp <i class="mdi mdi-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <a href="course_detail.php?id=<?= $course_id ?>" class="nav-btn next">
                        <i class="mdi mdi-check-circle-outline"></i> Hoàn thành khóa học
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- SIDEBAR: danh sách bài -->
    <aside class="lrn-sidebar">
        <div class="sidebar-head">
            <h3>Nội dung khóa học</h3>
            <?php if ($enrolled): ?>
            <div class="prog-bar-bg">
                <div class="prog-bar-fill" style="width:<?= $progress_pct ?>%"></div>
            </div>
            <div class="prog-label"><?= $progress_pct ?>% hoàn thành · <?= $done_count ?>/<?= count($all_lessons) ?> bài</div>
            <?php endif; ?>
        </div>

        <div class="sidebar-list">
            <?php
            /* lấy danh sách bài đã xem */
            $done_ids = [];
            if ($enrolled) {
                $dr = $conn->query("SELECT lesson_id FROM lesson_progress WHERE user_id=$user_id AND lesson_id IN (SELECT id FROM lessons WHERE course_id=$course_id)");
                while ($d = $dr->fetch_assoc()) $done_ids[] = $d['lesson_id'];
            }
            foreach ($all_lessons as $idx => $ls):
                $is_active = ($ls['id'] == $lesson_id);
                $is_done   = in_array($ls['id'], $done_ids);
                $can_view  = $enrolled || !empty($ls['free_preview']);
                $cls       = $is_active ? 'active' : ($is_done ? 'done' : '');
            ?>
            <?php if ($can_view): ?>
                <a href="learning.php?course_id=<?= $course_id ?>&id=<?= $ls['id'] ?>"
                    class="sl-item <?= $cls ?>">
                    <div class="sl-num">
                        <?php if ($is_done && !$is_active): ?>
                            <i class="mdi mdi-check" style="font-size:12px"></i>
                        <?php else: ?>
                            <?= $idx + 1 ?>
                        <?php endif; ?>
                    </div>
                    <div class="sl-info">
                        <div class="sl-title"><?= htmlspecialchars($ls['title']) ?></div>
                        <?php if (!empty($ls['free_preview']) && !$enrolled): ?>
                            <div class="sl-sub">Xem thử miễn phí</div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($ls['video_url'])): ?>
                        <i class="mdi mdi-play-circle-outline" style="color:rgba(255,255,255,.3);font-size:16px"></i>
                    <?php endif; ?>
                </a>
            <?php else: ?>
                <div class="sl-item" style="opacity:.5;cursor:default">
                    <div class="sl-num"><?= $idx + 1 ?></div>
                    <div class="sl-info"><div class="sl-title"><?= htmlspecialchars($ls['title']) ?></div></div>
                    <i class="mdi mdi-lock-outline sl-lock"></i>
                </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </aside>

</div>

<script>
/* Tự động chuyển bài tiếp theo khi video kết thúc */
function onVideoEnd() {
    <?php if ($next_lesson && ($enrolled || !empty($next_lesson['free_preview']))): ?>
        setTimeout(() => {
            window.location.href = 'learning.php?course_id=<?= $course_id ?>&id=<?= $next_lesson['id'] ?>';
        }, 1500);
    <?php endif; ?>
}

/* Lưu vị trí xem (localStorage) */
const vid = document.getElementById('mainVideo');
const storageKey = 'progress_lesson_<?= $lesson_id ?>';
if (vid) {
    const saved = localStorage.getItem(storageKey);
    if (saved && parseFloat(saved) > 5) vid.currentTime = parseFloat(saved);
    vid.addEventListener('timeupdate', () => {
        if (vid.currentTime > 3) localStorage.setItem(storageKey, vid.currentTime);
    });
}
</script>
</body>
</html>
