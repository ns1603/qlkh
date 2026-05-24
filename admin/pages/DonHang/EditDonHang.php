<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Truy cáº­p bá» tá»« chá»i!");
}

if ($_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
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

if (!$order) die("ÄÆ¡n hÃ ng khÃ´ng tá»n táº¡i!");
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

        $_SESSION['status_message'] = "ÄÃ£ cáº­p nháº­t ÄÆ¡n hÃ ng thÃ nh cÃ´ng!";
        header("Location: ListDonHang.php");
        exit;
    } else {
        $error = "Lá»i Database: " . $conn->error;
    }
}
?>

<?php 
include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/header.php"; 
include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/navbar.php"; 
?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Xá»­ lÃ½ ÄÆ¡n hÃ ng #<?= $id ?> </h3>
            </div>
            
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">ThÃ´ng tin chi tiáº¿t</h4>
                            
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item"><strong>NgÆ°á»i mua:</strong> <?= htmlspecialchars($order['fullname']) ?></li>
                                <li class="list-group-item"><strong>KhÃ³a há»c:</strong> <?= htmlspecialchars($order['course_name']) ?></li>
                                <li class="list-group-item"><strong>Sá» tiá»n:</strong> <span class="text-success fw-bold"><?= number_format($order['total_amount']) ?> Ä</span></li>
                                <li class="list-group-item"><strong>NgÃ y Äáº·t:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></li>
                            </ul>

                            <form class="forms-sample" method="POST">
                                <div class="form-group">
                                    <label>MÃ£ giao dá»ch ngÃ¢n hÃ ng (Náº¿u cÃ³)</label>
                                    <input type="text" class="form-control" name="transaction_code" 
                                           value="<?= htmlspecialchars($order['transaction_code'] ?? '') ?>" 
                                           placeholder="VD: FT23456789">
                                </div>

                                <div class="form-group">
                                    <label>Tráº¡ng thÃ¡i ÄÆ¡n hÃ ng</label>
                                    <select class="form-select" name="status">
                                        <option value="pending" <?= $order['status']=='pending'?'selected':'' ?>>â³ Chá» thanh toÃ¡n (Pending)</option>
                                        <option value="completed" <?= $order['status']=='completed'?'selected':'' ?>>â ÄÃ£ thanh toÃ¡n (Completed)</option>
                                        <option value="cancelled" <?= $order['status']=='cancelled'?'selected':'' ?>>â ÄÃ£ há»§y (Cancelled)</option>
                                    </select>
                                    <small class="text-muted mt-2 d-block">
                                        * LÆ°u Ã½: Khi chá»n <strong>"ÄÃ£ thanh toÃ¡n"</strong>, há»c viÃªn sáº½ tá»± Äá»ng ÄÆ°á»£c vÃ o há»c.
                                    </small>
                                </div>

                                <button type="submit" class="btn btn-gradient-primary me-2">Cáº­p nháº­t</button>
                                <a href="ListDonHang.php" class="btn btn-light">Quay láº¡i</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
    </div>
</div>
