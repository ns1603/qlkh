<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit;
}

$enrollment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            e.enrolled_at,
            c.title as course_name, 
            c.price, 
            c.thumbnail,
            student.fullname as student_name, 
            student.email as student_email,
            teacher.fullname as teacher_name
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        JOIN users student ON e.user_id = student.id
        JOIN users teacher ON c.teacher_id = teacher.id
        WHERE e.id = ? AND e.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $enrollment_id, $user_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Lỗi: Không tìm thấy thông tin đăng ký hoặc bạn không có quyền xem tài liệu này.");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đăng ký - <?= htmlspecialchars($data['course_name']) ?></title>
    <link rel="stylesheet" href="css/bootstrap.css">
    
    <style>
        body {
            background-color: #e9ecef;
            padding: 40px 0;
            font-family: 'Times New Roman', Times, serif;
        }
        .contract-paper {
            background: #fff;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 50px 60px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            position: relative;
        }
        .contract-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .contract-header h3 {
            font-size: 16px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .contract-header h2 {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-top: 20px;
            text-transform: uppercase;
        }
        .contract-meta {
            text-align: right;
            font-style: italic;
            color: #666;
            margin-bottom: 30px;
        }
        .section-title {
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #007bff;
        }
        .info-row {
            margin-bottom: 10px;
            font-size: 15px;
            line-height: 1.6;
        }
        .info-label {
            font-weight: bold;
            width: 180px;
            display: inline-block;
        }
        .signature-box {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .stamp-box {
            border: 3px solid #dc3545;
            color: #dc3545;
            font-weight: bold;
            font-size: 18px;
            display: inline-block;
            padding: 10px 20px;
            transform: rotate(-15deg);
            opacity: 0.8;
            margin-top: 20px;
            font-family: sans-serif;
        }
        /* Ẩn nút in khi in ra giấy */
        @media print {
            body { background: #fff; padding: 0; }
            .contract-paper { box-shadow: none; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="text-center mb-4 no-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="mdi mdi-printer"></i> In xác nhận / Lưu PDF
            </button>
            <a href="profile.php" class="btn btn-secondary">Quay lại Hồ sơ</a>
        </div>

        <div class="contract-paper">
            
            <div class="contract-header">
                <h3>Cộng Hòa Xã Hội Chủ Nghĩa Việt Nam</h3>
                <p>Độc lập - Tự do - Hạnh phúc</p>
                <hr style="width: 40%; border-top: 2px solid #000;">
                <h2>GIẤY XÁC NHẬN ĐĂNG KÝ KHÓA HỌC</h2>
            </div>
            <div class="contract-meta">
                <p>Mã số giao dịch: #EL-<?= date('Ymd') ?>-<?= $enrollment_id ?></p>
                <p>Ngày lập: <?= date('d/m/Y') ?></p>
            </div>
            <div class="contract-content">
                <p>Hệ thống Đào tạo trực tuyến <strong>V-Learning</strong> xác nhận:</p>

                <div class="section-title">I. THÔNG TIN HỌC VIÊN</div>
                <div class="info-row">
                    <span class="info-label">Họ và tên:</span> <?= htmlspecialchars($data['student_name']) ?>
                </div>
                <div class="info-row">
                    <span class="info-label">Email đăng ký:</span> <?= htmlspecialchars($data['student_email']) ?>
                </div>
                <div class="info-row">
                    <span class="info-label">Ngày vào học:</span> <?= date('d/m/Y H:i', strtotime($data['enrolled_at'])) ?>
                </div>

                <div class="section-title">II. THÔNG TIN KHÓA HỌC</div>
                <div class="info-row">
                    <span class="info-label">Tên khóa học:</span> <strong><?= htmlspecialchars($data['course_name']) ?></strong>
                </div>
                <div class="info-row">
                    <span class="info-label">Giảng viên phụ trách:</span> <?= htmlspecialchars($data['teacher_name']) ?>
                </div>
                <div class="info-row">
                    <span class="info-label">Học phí:</span> <?= number_format($data['price']) ?> VNĐ
                </div>
                <div class="info-row">
                    <span class="info-label">Trạng thái:</span> Đã thanh toán
                </div>

                <div class="section-title">III. QUYỀN LỢI & ĐIỀU KHOẢN</div>
                <p>1. Học viên được cấp quyền truy cập đầy đủ vào các video bài giảng, tài liệu đính kèm và hệ thống bài kiểm tra của khóa học này trên website.</p>
                <p>2. Học viên cam kết không sao chép, chia sẻ tài khoản hoặc phát tán nội dung khóa học ra bên ngoài dưới mọi hình thức.</p>
                <p>3. Chứng chỉ hoàn thành sẽ được cấp tự động sau khi học viên hoàn tất 100% nội dung chương trình.</p>
            </div>
            <div class="signature-box">
                <div style="width: 40%">
                    <p><strong>HỌC VIÊN</strong></p>
                    <p><i>(Đã xác nhận điện tử)</i></p>
                    <br><br>
                    <strong><?= htmlspecialchars($data['student_name']) ?></strong>
                </div>
                
                <div style="width: 40%">
                    <p><strong>ĐẠI DIỆN HỆ THỐNG</strong></p>
                    <div class="stamp-box">ĐÃ KÍCH HOẠT</div>
                    <br><br>
                    <strong>Ban Quản Trị</strong>
                </div>
            </div>

            <div style="margin-top: 50px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; padding-top: 10px;">
                Đây là văn bản điện tử, có giá trị xác thực trên hệ thống Learning System.<br>
                Vui lòng liên hệ Admin nếu có sai sót thông tin.
            </div>

        </div>
    </div>

</body>
</html>