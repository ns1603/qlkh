<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] == 'student') {
    die("Bạn không có quyền này!");
}

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=DanhSachDiemThi_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "\xEF\xBB\xBF"; 

// 1. Dòng tiêu đề cột
echo "STT\tTên Học Viên\tEmail\tTên Bài Thi\tKhóa Học\tĐiểm Số\tNgày Nộp\n";

$sql = "SELECT r.*, u.fullname, u.email, q.title as exam_name, c.title as course_name
        FROM exam_results r
        JOIN users u ON r.user_id = u.id
        JOIN quizzes q ON r.quiz_id = q.id
        JOIN courses c ON q.course_id = c.id";

// Nếu là Teacher thì chỉ xuất điểm của khóa mình dạy
if ($_SESSION['user_role'] == 'teacher') {
    $teacher_id = $_SESSION['user_id'];
    $sql .= " WHERE c.teacher_id = $teacher_id";
}

$sql .= " ORDER BY r.created_at DESC";
$result = $conn->query($sql);

$i = 1;
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Xuất từng dòng dữ liệu, ngăn cách bằng tab (\t)
        echo $i . "\t"
           . $row['fullname'] . "\t"
           . $row['email'] . "\t"
           . $row['exam_name'] . "\t"
           . $row['course_name'] . "\t"
           . $row['score'] . "\t"
           . $row['created_at'] . "\n";
        $i++;
    }
}
exit;
?>