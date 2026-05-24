<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit;
}

$enrollment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$sql = "SELECT c.title, c.price, c.thumbnail, u.fullname, u.email, e.enrolled_at, t.fullname as teacher_name
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        JOIN users u ON e.user_id = u.id
        JOIN users t ON c.teacher_id = t.id
        WHERE e.id = ? AND e.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $enrollment_id, $user_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Không tìm thấy thông tin đăng ký hoặc bạn không có quyền truy cập.");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đăng ký khóa học</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <style>
        body { background: #f4f7f6; padding: 50px 0; }
        .contract-box {
            background: #fff;
            padding: 40px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        .contract-header { border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .contract-title { text-transform: uppercase; color: #333; font-weight: bold; }
        .contract-meta { color: #666; font-style: italic; }
        .stamp {
            border: 3px solid #00b894;
            color: #00b894;
            display: inline-block;
            padding: 10px 20px;
            text-transform: uppercase;
            font-weight: bold;
            transform: rotate(-5deg);
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="contract-box">
        <div class="text-center contract-header">
            <h3>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</h3>
            <p>Độc lập - Tự do - Hạnh phúc</p>
            <hr width="50%">
            <h2 class="contract-title mt-4">GIẤY XÁC NHẬN ĐĂNG KÝ KHÓA HỌC</h2>
            <p class="contract-meta">Mã số: EL-<?= time() ?>-<?= $enrollment_id ?></p>
        </div>

        <div class="row">
            <div class="col-md-12">
                <p>Hôm nay, ngày <strong><?= date('d') ?></strong> tháng <strong><?= date('m') ?></strong> năm <strong><?= date('Y') ?></strong>.</p>
                <p>Hệ thống đào tạo trực tuyến <strong>Learning System</strong> xác nhận:</p>
                
                <h5 class="mt-4 text-primary">1. THÔNG TIN HỌC VIÊN</h5>
                <ul class="list-unstyled" style="padding-left: 20px;">
                    <li><strong>Họ và tên:</strong> <?= htmlspecialchars($data['fullname']) ?></li>
                    <li><strong>Email:</strong> <?= htmlspecialchars($data['email']) ?></li>
                </ul>

                <h5 class="mt-4 text-primary">2. THÔNG TIN KHÓA HỌC</h5>
                <ul class="list-unstyled" style="padding-left: 20px;">
                    <li><strong>Tên khóa học:</strong> <?= htmlspecialchars($data['title']) ?></li>
                    <li><strong>Giảng viên phụ trách:</strong> <?= htmlspecialchars($data['teacher_name']) ?></li>
                    <li><strong>Học phí:</strong> <?= number_format($data['price']) ?> VNĐ</li>
                    <li><strong>Ngày đăng ký:</strong> <?= date('d/m/Y H:i', strtotime($data['enrolled_at'])) ?></li>
                </ul>

                <h5 class="mt-4 text-primary">3. QUYỀN LỢI</h5>
                <p>Học viên được quyền truy cập vào toàn bộ bài giảng, tài liệu và hệ thống bài kiểm tra của khóa học này. Chứng chỉ sẽ được cấp sau khi hoàn thành 100% nội dung.</p>
                
                <div class="text-center mt-5">
                    <div class="stamp">ĐÃ THANH TOÁN & KÍCH HOẠT</div>
                    <br><br>
                    <button onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="mdi mdi-printer"></i> In xác nhận
                    </button>
                    <a href="course_details.php?id=<?= $_GET['course_id'] ?? 0 ?>" class="btn btn-primary">Vào học ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>