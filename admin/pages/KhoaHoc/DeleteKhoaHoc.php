<?php
session_start();
include(__DIR__ . '/../../../config.php');

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Ép kiểu số nguyên để bảo mật

if ($id > 0) {
    // 2. LẤY THÔNG TIN KHÓA HỌC TRƯỚC (Để kiểm tra quyền và lấy tên ảnh thumbnail)
    $stmt = $conn->prepare("SELECT id, teacher_id, thumbnail FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();

    if ($course) {
        // 3. PHÂN QUYỀN: Teacher chỉ được xóa bài của mình
        if ($role == 'teacher' && $course['teacher_id'] != $user_id) {
            $_SESSION['status_message'] = "CẢNH BÁO: Bạn không có quyền xóa khóa học của người khác!";
            header("Location: ListKhoaHoc.php");
            exit;
        }

        // 4. XÓA ẢNH THUMBNAIL TRÊN SERVER (Dọn rác)
        if (!empty($course['thumbnail'])) {
            // Đường dẫn thực tế file ảnh trên ổ cứng
            // Lưu ý: Cần chỉnh đường dẫn này khớp với cấu trúc thư mục của bạn
            // Giả sử config.php nằm ở root/admin/pages/KhoaHoc/../../../ -> root
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/Learning/' . $course['thumbnail'];
            
            if (file_exists($file_path)) {
                unlink($file_path); // Hàm xóa file
            }
        }

        // 5. XÓA DỮ LIỆU TRONG DATABASE
        // Lưu ý: Nếu bạn chưa cài đặt 'ON DELETE CASCADE' trong MySQL cho các bảng con (lessons, enrollments...),
        // bạn cần xóa dữ liệu ở bảng con trước hoặc dùng lệnh này sẽ bị lỗi khóa ngoại.
        // Giả sử bạn đã thiết lập Database chuẩn, ta xóa course:
        
        $delStmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $delStmt->bind_param("i", $id);

        if ($delStmt->execute()) {
            $_SESSION['status_message'] = "Đã xóa khóa học thành công!";
        } else {
            $_SESSION['status_message'] = "Lỗi Database: Không thể xóa (Có thể do ràng buộc dữ liệu học viên/bài giảng).";
        }
    } else {
        $_SESSION['status_message'] = "Khóa học không tồn tại!";
    }
} else {
    $_SESSION['status_message'] = "ID không hợp lệ!";
}

// 6. QUAY VỀ TRANG DANH SÁCH
header("Location: ListKhoaHoc.php");
exit;
?>