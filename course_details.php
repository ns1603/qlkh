<?php
session_start();
include 'config.php';

$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

/* =========================
   COURSE
========================= */
$sql = "
SELECT 
    c.*, 
    u.fullname AS teacher_name,
    u.avatar AS teacher_avatar,
    cat.name AS cat_name
FROM courses c
LEFT JOIN users u ON c.teacher_id = u.id
LEFT JOIN categories cat ON c.category_id = cat.id
WHERE c.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();

$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    header("Location: courses_list.php");
    exit;
}

/* =========================
   ENROLL CHECK
========================= */
$is_enrolled = false;

if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    $check = $conn->prepare("
        SELECT id 
        FROM enrollments
        WHERE user_id = ?
        AND course_id = ?
    ");

    $check->bind_param("ii", $user_id, $course_id);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        $is_enrolled = true;
    }
}

/* =========================
   LESSONS
========================= */
$sql_lessons = "
SELECT *
FROM lessons
WHERE course_id = ?
ORDER BY id ASC
";

$stmt_l = $conn->prepare($sql_lessons);
$stmt_l->bind_param("i", $course_id);
$stmt_l->execute();

$lessons = $stmt_l->get_result();
$lesson_count = $lessons->num_rows;

/* =========================
   REVIEWS
========================= */
$sql_reviews = "
SELECT 
    r.*,
    u.fullname,
    u.avatar
FROM ratings r
JOIN users u ON r.user_id = u.id
WHERE r.course_id = ?
ORDER BY r.created_at DESC
";

$stmt_r = $conn->prepare($sql_reviews);
$stmt_r->bind_param("i", $course_id);
$stmt_r->execute();

$reviews = $stmt_r->get_result();

/* =========================
   AVG RATING
========================= */
$sql_avg = "
SELECT 
    AVG(rating) as avg_rating,
    COUNT(*) as total
FROM ratings
WHERE course_id = ?
";

$stmt_avg = $conn->prepare($sql_avg);
$stmt_avg->bind_param("i", $course_id);
$stmt_avg->execute();

$rating_data = $stmt_avg->get_result()->fetch_assoc();

$avg_rating = round($rating_data['avg_rating'] ?? 0, 1);
$total_reviews = $rating_data['total'] ?? 0;
?>

<?php include 'header.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>

:root{
    --blue:#2563eb;
    --blue2:#1d4ed8;
    --dark:#0f172a;
    --gray:#64748b;
    --light:#f8fafc;
    --border:#e2e8f0;
    --green:#059669;
    --red:#dc2626;
    --yellow:#fbbf24;
}

*{
    box-sizing:border-box;
}

body{
    font-family:'Plus Jakarta Sans',sans-serif;
    background:#f1f5f9;
    color:var(--dark);
}

/* HERO */

.cd-hero{
    background:linear-gradient(135deg,#0f172a,#1e40af);
    padding:45px 0;
    color:#fff;
}

.cd-container{
    max-width:1180px;
    margin:auto;
    padding:0 20px;
}

.cd-category{
    display:inline-block;
    background:rgba(255,255,255,.1);
    color:#bfdbfe;
    padding:6px 14px;
    border-radius:30px;
    font-size:12px;
    font-weight:700;
    margin-bottom:16px;
}

.cd-hero h1{
    font-size:36px;
    font-weight:800;
    margin-bottom:18px;
    line-height:1.4;
}

.cd-meta{
    display:flex;
    flex-wrap:wrap;
    gap:18px;
    color:rgba(255,255,255,.85);
    font-size:14px;
}

/* LAYOUT */

.cd-main{
    padding:30px 0 60px;
}

.cd-grid{
    display:grid;
    grid-template-columns:1fr 330px;
    gap:24px;
}

@media(max-width:900px){

    .cd-grid{
        grid-template-columns:1fr;
    }

    .cd-sidebar{
        position:static;
    }

    .cd-hero h1{
        font-size:28px;
    }
}

/* CARD */

.cd-card,
.cd-sidebar-card{
    background:#fff;
    border-radius:20px;
    border:1px solid var(--border);
    box-shadow:0 6px 18px rgba(15,23,42,.04);
}

.cd-card{
    padding:28px;
}

.cd-section{
    margin-bottom:36px;
}

.cd-section:last-child{
    margin-bottom:0;
}

.cd-title{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:22px;
    font-weight:800;
    margin-bottom:20px;
}

.cd-title i{
    color:var(--blue);
}

.cd-description{
    color:var(--gray);
    line-height:1.9;
    font-size:15px;
}

/* LESSON */

.cd-lesson{
    display:flex;
    gap:14px;
    padding:15px;
    border:1px solid var(--border);
    border-radius:14px;
    margin-bottom:12px;
    transition:.2s;
}

.cd-lesson:hover{
    border-color:#bfdbfe;
    background:#f8fbff;
}

.cd-lesson-number{
    width:36px;
    height:36px;
    border-radius:50%;
    background:#dbeafe;
    color:var(--blue);
    font-weight:700;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
}

.cd-lesson-title{
    font-size:15px;
    font-weight:700;
    margin-bottom:8px;
}

.cd-youtube{
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:#fee2e2;
    color:#dc2626;
    text-decoration:none;
    padding:8px 13px;
    border-radius:30px;
    font-size:13px;
    font-weight:700;
    transition:.2s;
}

.cd-youtube:hover{
    background:#dc2626;
    color:#fff;
}

/* REVIEW */

.cd-review-summary{
    background:#fffbeb;
    border:1px solid #fde68a;
    border-radius:18px;
    padding:22px;
    text-align:center;
    margin-bottom:22px;
}

.cd-review-score{
    font-size:54px;
    font-weight:800;
    color:#d97706;
}

.cd-stars{
    color:var(--yellow);
    font-size:18px;
    margin:8px 0;
}

.cd-review-item{
    display:flex;
    gap:14px;
    padding:18px 0;
    border-bottom:1px solid #eef2f7;
}

.cd-review-item:last-child{
    border-bottom:none;
}

.cd-review-avatar,
.cd-review-avatar-placeholder{
    width:48px;
    height:48px;
    border-radius:50%;
    flex-shrink:0;
}

.cd-review-avatar{
    object-fit:cover;
}

.cd-review-avatar-placeholder{
    background:linear-gradient(135deg,#2563eb,#7c3aed);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
}

.cd-review-name{
    font-weight:700;
    margin-bottom:4px;
}

.cd-review-text{
    color:var(--gray);
    line-height:1.7;
    font-size:14px;
}

/* FORM */

.cd-rating-form{
    margin-top:25px;
    background:#eff6ff;
    border:1px solid #bfdbfe;
    border-radius:18px;
    padding:22px;
}

.star-input-wrap{
    display:flex;
    flex-direction:row-reverse;
    justify-content:flex-end;
    gap:4px;
    margin-bottom:16px;
}

.star-input-wrap input{
    display:none;
}

.star-input-wrap label{
    font-size:32px;
    color:#cbd5e1;
    cursor:pointer;
}

.star-input-wrap input:checked ~ label,
.star-input-wrap label:hover,
.star-input-wrap label:hover ~ label{
    color:#fbbf24;
}

.cd-textarea{
    width:100%;
    min-height:110px;
    border:1px solid var(--border);
    border-radius:14px;
    padding:14px;
    resize:vertical;
    font-family:inherit;
    outline:none;
}

.cd-textarea:focus{
    border-color:#93c5fd;
}

.cd-submit{
    margin-top:14px;
    background:linear-gradient(135deg,#2563eb,#4f46e5);
    color:#fff;
    border:none;
    padding:13px 22px;
    border-radius:14px;
    font-weight:700;
    cursor:pointer;
    transition:.2s;
}

.cd-submit:hover{
    opacity:.92;
}

/* SIDEBAR */

.cd-sidebar{
    position:sticky;
    top:20px;
}

.cd-sidebar-card{
    overflow:hidden;
}

.cd-thumb{
    width:100%;
    height:210px;
    object-fit:cover;
}

.cd-sidebar-body{
    padding:24px;
}

.cd-price{
    text-align:center;
    margin-bottom:20px;
}

.cd-price-free,
.cd-price-paid{
    font-size:32px;
    font-weight:800;
}

.cd-price-free{
    color:var(--green);
}

.cd-price-paid{
    color:var(--red);
}

.cd-btn{
    width:100%;
    padding:14px;
    border:none;
    border-radius:14px;
    text-decoration:none;
    display:flex;
    justify-content:center;
    align-items:center;
    gap:8px;
    font-weight:700;
    transition:.2s;
}

.cd-btn.buy{
    background:linear-gradient(135deg,#2563eb,#4f46e5);
    color:#fff;
}

.cd-btn.learn{
    background:linear-gradient(135deg,#059669,#0891b2);
    color:#fff;
}

.cd-btn:hover{
    opacity:.92;
}

.cd-feature{
    padding:12px 0;
    border-bottom:1px dashed #e5e7eb;
    color:var(--gray);
    font-size:14px;
}

.cd-feature:last-child{
    border-bottom:none;
}

/* EMPTY */

.cd-empty{
    text-align:center;
    color:var(--gray);
    padding:30px 0;
}

.cd-empty i{
    font-size:40px;
    opacity:.3;
    display:block;
    margin-bottom:10px;
}

</style>

<!-- HERO -->
<section class="cd-hero">

    <div class="cd-container">

        <div class="cd-category">
            <?= htmlspecialchars($course['cat_name'] ?? 'Khóa học') ?>
        </div>

        <h1>
            <?= htmlspecialchars($course['title']) ?>
        </h1>

        <div class="cd-meta">

            <span>
                <i class="mdi mdi-account"></i>
                <?= htmlspecialchars($course['teacher_name'] ?? 'Admin') ?>
            </span>

            <span>
                <i class="mdi mdi-video-outline"></i>
                <?= $lesson_count ?> bài học
            </span>

            <span>
                <i class="mdi mdi-star"></i>
                <?= $avg_rating ?>/5
            </span>

            <span>
                <i class="mdi mdi-calendar"></i>
                <?= date('d/m/Y', strtotime($course['updated_at'])) ?>
            </span>

        </div>

    </div>

</section>

<!-- MAIN -->
<section class="cd-main">

    <div class="cd-container">

        <div class="cd-grid">

            <!-- LEFT -->
            <div>

                <div class="cd-card">

                    <!-- DESCRIPTION -->
                    <div class="cd-section">

                        <div class="cd-title">
                            <i class="mdi mdi-book-open-page-variant"></i>
                            Giới thiệu khóa học
                        </div>

                        <div class="cd-description">
                            <?= nl2br(htmlspecialchars($course['description'])) ?>
                        </div>

                    </div>

                    <!-- LESSONS -->
                    <div class="cd-section">

                        <div class="cd-title">
                            <i class="mdi mdi-play-circle"></i>
                            Nội dung khóa học
                        </div>

                        <?php if ($lesson_count > 0): ?>

                            <?php
                            $i = 1;
                            $lessons->data_seek(0);

                            while ($lesson = $lessons->fetch_assoc()):
                            ?>

                                <div class="cd-lesson">

                                    <div class="cd-lesson-number">
                                        <?= $i ?>
                                    </div>

                                    <div style="flex:1;">

                                        <div class="cd-lesson-title">
                                            Bài <?= $i++ ?>:
                                            <?= htmlspecialchars($lesson['title']) ?>
                                        </div>

                                        <?php
                                        $video_url = $lesson['video_url'] ?? '';
                                        ?>

                                        <?php if (!empty($video_url)): ?>

                                            <a
                                                href="<?= htmlspecialchars($video_url) ?>"
                                                target="_blank"
                                                class="cd-youtube"
                                            >
                                                <i class="mdi mdi-youtube"></i>
                                                Xem trên Youtube
                                            </a>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <div class="cd-empty">

                                <i class="mdi mdi-video-off-outline"></i>

                                Chưa có bài học nào.

                            </div>

                        <?php endif; ?>

                    </div>

                    <!-- REVIEWS -->
                    <div class="cd-section">

                        <div class="cd-title">
                            <i class="mdi mdi-star"></i>
                            Đánh giá học viên
                        </div>

                        <?php if ($total_reviews > 0): ?>

                            <div class="cd-review-summary">

                                <div class="cd-review-score">
                                    <?= $avg_rating ?>
                                </div>

                                <div class="cd-stars">

                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo ($i <= round($avg_rating)) ? '★' : '☆';
                                    }
                                    ?>

                                </div>

                                <div>
                                    <?= $total_reviews ?> đánh giá
                                </div>

                            </div>

                        <?php endif; ?>

                        <?php
                        $reviews->data_seek(0);
                        $has_reviews = false;

                        while ($rv = $reviews->fetch_assoc()):
                            $has_reviews = true;
                        ?>

                            <div class="cd-review-item">

                                <?php if (!empty($rv['avatar'])): ?>

                                    <img
                                        src="<?= htmlspecialchars($rv['avatar']) ?>"
                                        class="cd-review-avatar"
                                    >

                                <?php else: ?>

                                    <div class="cd-review-avatar-placeholder">
                                        <?= mb_strtoupper(mb_substr($rv['fullname'], 0, 1)) ?>
                                    </div>

                                <?php endif; ?>

                                <div style="flex:1;">

                                    <div class="cd-review-name">
                                        <?= htmlspecialchars($rv['fullname']) ?>
                                    </div>

                                    <div class="cd-stars" style="font-size:15px;margin:4px 0;">

                                        <?php
                                        for ($k = 1; $k <= 5; $k++) {
                                            echo ($k <= $rv['rating']) ? '★' : '☆';
                                        }
                                        ?>

                                    </div>

                                    <div class="cd-review-text">
                                        <?= htmlspecialchars($rv['review']) ?>
                                    </div>

                                </div>

                            </div>

                        <?php endwhile; ?>

                        <?php if (!$has_reviews): ?>

                            <div class="cd-empty">

                                <i class="mdi mdi-comment-outline"></i>

                                Chưa có đánh giá nào.

                            </div>

                        <?php endif; ?>

                        <!-- FORM -->
                        <?php if ($is_enrolled): ?>

                            <div class="cd-rating-form">

                                <h3 style="margin-bottom:16px;">
                                    Viết đánh giá
                                </h3>

                                <form action="submit_rating.php" method="POST">

                                    <input
                                        type="hidden"
                                        name="course_id"
                                        value="<?= $course_id ?>"
                                    >

                                    <div class="star-input-wrap">

                                        <input type="radio" name="rating" value="1" id="r1">
                                        <label for="r1">★</label>

                                        <input type="radio" name="rating" value="2" id="r2">
                                        <label for="r2">★</label>

                                        <input type="radio" name="rating" value="3" id="r3">
                                        <label for="r3">★</label>

                                        <input type="radio" name="rating" value="4" id="r4">
                                        <label for="r4">★</label>

                                        <input type="radio" name="rating" value="5" id="r5" checked>
                                        <label for="r5">★</label>

                                    </div>

                                    <textarea
                                        name="review"
                                        class="cd-textarea"
                                        placeholder="Nhập đánh giá..."
                                        required
                                    ></textarea>

                                    <button
                                        type="submit"
                                        class="cd-submit"
                                    >
                                        Gửi đánh giá
                                    </button>

                                </form>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

            <!-- SIDEBAR -->
            <div class="cd-sidebar">

                <div class="cd-sidebar-card">

                    <?php if (!empty($course['thumbnail'])): ?>

                        <img
                            src="<?= htmlspecialchars($course['thumbnail']) ?>"
                            class="cd-thumb"
                        >

                    <?php endif; ?>

                    <div class="cd-sidebar-body">

                        <div class="cd-price">

                            <?php if ($course['price'] == 0): ?>

                                <div class="cd-price-free">
                                    Miễn phí
                                </div>

                            <?php else: ?>

                                <div class="cd-price-paid">
                                    <?= number_format($course['price']) ?> đ
                                </div>

                            <?php endif; ?>

                        </div>

                        <?php if ($is_enrolled): ?>

                            <a
                                href="learning.php?course_id=<?= $course_id ?>"
                                class="cd-btn learn"
                            >
                                <i class="mdi mdi-play-circle"></i>
                                Vào học ngay
                            </a>

                        <?php else: ?>

                            <form action="checkout.php" method="GET">

                                <input
                                    type="hidden"
                                    name="course_id"
                                    value="<?= $course_id ?>"
                                >

                                <button
                                    type="submit"
                                    class="cd-btn buy"
                                >
                                    <i class="mdi mdi-cart"></i>
                                    Mua khóa học
                                </button>

                            </form>

                        <?php endif; ?>

                        <div style="margin-top:25px;">

                            <div class="cd-feature">
                                <i class="mdi mdi-infinity"></i>
                                Truy cập trọn đời
                            </div>

                            <div class="cd-feature">
                                <i class="mdi mdi-cellphone"></i>
                                Học trên mọi thiết bị
                            </div>

                            <div class="cd-feature">
                                <i class="mdi mdi-certificate"></i>
                                Có chứng chỉ
                            </div>

                            <div class="cd-feature">
                                <i class="mdi mdi-video-outline"></i>
                                <?= $lesson_count ?> bài học video
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<?php include 'footer.php'; ?>