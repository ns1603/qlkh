<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Truy cập bị từ chối!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT o.*, u.fullname, c.title as course_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        JOIN courses c ON o.course_id = c.id 
        WHERE o.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) die("Đơn hàng không tồn tại!");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_status = $_POST['status'];
    $transaction_code = trim($_POST['transaction_code']);
    $update = $conn->prepare("UPDATE orders SET status = ?, transaction_code = ? WHERE id = ?");
    $update->bind_param("ssi", $new_status, $transaction_code, $id);
    
    if ($update->execute()) {
        
        if ($new_status == 'completed') {
            $u_id = $order['user_id'];
            $c_id = $order['course_id'];

            $check_enroll = $conn->query("SELECT id FROM enrollments WHERE user_id = $u_id AND course_id = $c_id");
            
            if ($check_enroll->num_rows == 0) {
                $conn->query("INSERT INTO enrollments (user_id, course_id) VALUES ($u_id, $c_id)");
            }
        }

        $_SESSION['status_message'] = "Đã cập nhật đơn hàng thành công!";
        header("Location: ListDonHang.php");
        exit;
    } else {
        $error = "Lỗi Database: " . $conn->error;
    }
}
?>

<?php 
include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/header.php"; 
include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/navbar.php"; 
?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Xử lý Đơn hàng #<?= $id ?> </h3>
            </div>
            
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Thông tin chi tiết</h4>
                            
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item"><strong>Người mua:</strong> <?= htmlspecialchars($order['fullname']) ?></li>
                                <li class="list-group-item"><strong>Khóa học:</strong> <?= htmlspecialchars($order['course_name']) ?></li>
                                <li class="list-group-item"><strong>Số tiền:</strong> <span class="text-success fw-bold"><?= number_format($order['total_amount']) ?> đ</span></li>
                                <li class="list-group-item"><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></li>
                            </ul>

                            <form class="forms-sample" method="POST">
                                <div class="form-group">
                                    <label>Mã giao dịch ngân hàng (Nếu có)</label>
                                    <input type="text" class="form-control" name="transaction_code" 
                                           value="<?= htmlspecialchars($order['transaction_code'] ?? '') ?>" 
                                           placeholder="VD: FT23456789">
                                </div>

                                <div class="form-group">
                                    <label>Trạng thái đơn hàng</label>
                                    <select class="form-select" name="status">
                                        <option value="pending" <?= $order['status']=='pending'?'selected':'' ?>>⏳ Chờ thanh toán (Pending)</option>
                                        <option value="completed" <?= $order['status']=='completed'?'selected':'' ?>>✅ Đã thanh toán (Completed)</option>
                                        <option value="cancelled" <?= $order['status']=='cancelled'?'selected':'' ?>>❌ Đã hủy (Cancelled)</option>
                                    </select>
                                    <small class="text-muted mt-2 d-block">
                                        * Lưu ý: Khi chọn <strong>"Đã thanh toán"</strong>, học viên sẽ tự động được vào học.
                                    </small>
                                </div>

                                <button type="submit" class="btn btn-gradient-primary me-2">Cập nhật</button>
                                <a href="ListDonHang.php" class="btn btn-light">Quay lại</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/footer.php"; ?>
    </div>
</div>