<?php
session_start();
include 'config.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xử lý đăng ký khóa học</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<?php

// =========================
// KIỂM TRA ĐĂNG NHẬP
// =========================
if (!isset($_SESSION['user_id'])) {
?>

<script>
Swal.fire({
    icon: 'warning',
    title: 'Chưa đăng nhập!',
    text: 'Vui lòng đăng nhập để đăng ký khóa học.'
}).then(() => {
    window.location.href = 'login.php';
});
</script>

<?php
exit;
}

// =========================
// KIỂM TRA POST
// =========================
if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    echo "<script>window.location.href='courses_list.php';</script>";
    exit;
}

// =========================
// NHẬN DỮ LIỆU
// =========================
$user_id = $_SESSION['user_id'];
$course_id = intval($_POST['course_id']);

// =========================
// KIỂM TRA USER TỒN TẠI
// =========================
$check_user = $conn->prepare("SELECT id FROM users WHERE id = ?");
$check_user->bind_param("i", $user_id);
$check_user->execute();

if ($check_user->get_result()->num_rows == 0) {

    session_destroy();
?>

<script>
Swal.fire({
    icon: 'error',
    title: 'Tài khoản không hợp lệ!',
    text: 'Vui lòng đăng nhập lại.'
}).then(() => {
    window.location.href = 'login.php';
});
</script>

<?php
exit;
}

// =========================
// LẤY KHÓA HỌC
// =========================
$stmt = $conn->prepare("SELECT price,title FROM courses WHERE id=?");
$stmt->bind_param("i", $course_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
?>

<script>
Swal.fire({
    icon: 'error',
    title: 'Khóa học không tồn tại!'
}).then(() => {
    history.back();
});
</script>

<?php
exit;
}

$course = $result->fetch_assoc();
$price = $course['price'];

// =========================
// KIỂM TRA ĐÃ MUA
// =========================
$check_enroll = $conn->prepare("
SELECT id FROM enrollments 
WHERE user_id=? AND course_id=?
");

$check_enroll->bind_param("ii", $user_id, $course_id);
$check_enroll->execute();

if ($check_enroll->get_result()->num_rows > 0) {
?>

<script>
Swal.fire({
    icon: 'info',
    title: 'Bạn đã mua khóa học này!'
}).then(() => {
    window.location.href = 'course_details.php?id=<?php echo $course_id; ?>';
});
</script>

<?php
exit;
}

// =========================
// TẠO ĐƠN HÀNG
// =========================
$stmt_order = $conn->prepare("
INSERT INTO orders(user_id,course_id,total_amount,status)
VALUES(?,?,?,'completed')
");

$stmt_order->bind_param("iid", $user_id, $course_id, $price);

if ($stmt_order->execute()) {

    // =========================
    // CẤP QUYỀN HỌC
    // =========================
    $stmt_enroll = $conn->prepare("
    INSERT INTO enrollments(user_id,course_id)
    VALUES(?,?)
    ");

    $stmt_enroll->bind_param("ii", $user_id, $course_id);

    if ($stmt_enroll->execute()) {
?>

<script>
Swal.fire({
    icon: 'success',
    title: 'Mua khóa học thành công!',
    text: 'Bạn có thể vào học ngay.',
    confirmButtonText: 'Vào học',
    confirmButtonColor: '#3085d6'
}).then(() => {
    window.location.href = 'course_details.php?id=<?php echo $course_id; ?>';
});
</script>

<?php
    } else {
?>

<script>
Swal.fire({
    icon: 'error',
    title: 'Lỗi cấp quyền học!'
});
</script>

<?php
    }

} else {
?>

<script>
Swal.fire({
    icon: 'error',
    title: 'Lỗi tạo đơn hàng!'
});
</script>

<?php
}
?>

</body>
</html>