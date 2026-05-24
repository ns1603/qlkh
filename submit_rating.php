<?php
session_start();
include 'config.php';

/* =========================
   CHECK LOGIN
========================= */
if (!isset($_SESSION['user_id'])) {

    $_SESSION['error'] = "Vui lòng đăng nhập!";
    header("Location: Login.php");
    exit;
}

/* =========================
   ONLY POST
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: courses_list.php");
    exit;
}

/* =========================
   GET DATA
========================= */
$user_id   = $_SESSION['user_id'];

$course_id = isset($_POST['course_id'])
    ? intval($_POST['course_id'])
    : 0;

$rating = isset($_POST['rating'])
    ? intval($_POST['rating'])
    : 0;

$review = trim($_POST['review'] ?? '');

/* =========================
   VALIDATE
========================= */
if ($course_id <= 0) {

    die("Course ID không hợp lệ!");
}

if ($rating < 1 || $rating > 5) {

    die("Số sao không hợp lệ!");
}

if (empty($review)) {

    die("Vui lòng nhập nội dung đánh giá!");
}

/* =========================
   CHECK COURSE EXIST
========================= */
$check_course = $conn->prepare("
    SELECT id
    FROM courses
    WHERE id = ?
");

$check_course->bind_param("i", $course_id);
$check_course->execute();

$result_course = $check_course->get_result();

if ($result_course->num_rows <= 0) {

    die("Khóa học không tồn tại!");
}

/* =========================
   CHECK ENROLLMENT
========================= */
$check_enroll = $conn->prepare("
    SELECT id
    FROM enrollments
    WHERE user_id = ?
    AND course_id = ?
");

$check_enroll->bind_param(
    "ii",
    $user_id,
    $course_id
);

$check_enroll->execute();

$result_enroll = $check_enroll->get_result();

if ($result_enroll->num_rows <= 0) {

    die("Bạn chưa mua khóa học này!");
}

/* =========================
   INSERT NEW COMMENT
========================= */
$stmt = $conn->prepare("
    INSERT INTO ratings (
        user_id,
        course_id,
        rating,
        review,
        created_at
    )
    VALUES (?, ?, ?, ?, NOW())
");

$stmt->bind_param(
    "iiis",
    $user_id,
    $course_id,
    $rating,
    $review
);

/* =========================
   EXECUTE
========================= */
if ($stmt->execute()) {

    $_SESSION['success'] = "Gửi đánh giá thành công!";

} else {

    $_SESSION['error'] = "Lỗi: " . $stmt->error;
}

/* =========================
   REDIRECT
========================= */
header("Location: course_details.php?id=" . $course_id);
exit;
?>