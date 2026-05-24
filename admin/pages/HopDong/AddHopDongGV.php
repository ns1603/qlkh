<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Truy cáº­p bá» tá»« chá»i!");
}
$teachers = $conn->query("SELECT id, fullname, email FROM users WHERE role = 'teacher'");
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = intval($_POST['teacher_id']);
    $contract_code = trim($_POST['contract_code']);
    $revenue_share = intval($_POST['revenue_share']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $file_path = "";
    if (isset($_FILES['contract_file']) && $_FILES['contract_file']['error'] == 0) {
        $target_dir = "../../../uploads/contracts/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $ext = pathinfo($_FILES['contract_file']['name'], PATHINFO_EXTENSION);
        $new_name = $contract_code . '_' . time() . '.' . $ext;
        
        if (move_uploaded_file($_FILES['contract_file']['tmp_name'], $target_dir . $new_name)) {
            $file_path = "uploads/contracts/" . $new_name;
        }
    }

    if (empty($contract_code) || empty($start_date)) {
        $error = "Vui lÃ²ng nháº­p mÃ£ há»£p Äá»ng vÃ  ngÃ y báº¯t Äáº§u!";
    } else {
        $stmt = $conn->prepare("INSERT INTO teacher_contracts (teacher_id, contract_code, file_path, revenue_share, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississ", $teacher_id, $contract_code, $file_path, $revenue_share, $start_date, $end_date);
        
        if ($stmt->execute()) {
            $_SESSION['status_message'] = "Thêm há»£p Äá»ng thÃ nh cÃ´ng!";
            header("Location: ListHopDongGV.php");
            exit;
        } else {
            $error = "Lá»i: " . $conn->error;
        }
    }
}
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/header.php"; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-8 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">KÃ½ káº¿t Há»£p Äá»ng má»i</h4>
                            <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

                            <form class="forms-sample" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Chá»n GiÃ¡o viÃªn</label>
                                    <select class="form-select" name="teacher_id" required>
                                        <?php while($t = $teachers->fetch_assoc()): ?>
                                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['fullname']) ?> (<?= $t['email'] ?>)</option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>MÃ£ há»£p Äá»ng</label>
                                        <input type="text" class="form-control" name="contract_code" placeholder="VD: HD-2024-GV01" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Tá»· lá» chia sáº» doanh thu (%)</label>
                                        <input type="number" class="form-control" name="revenue_share" value="70" min="0" max="100">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>NgÃ y báº¯t Äáº§u</label>
                                        <input type="date" class="form-control" name="start_date" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>NgÃ y káº¿t thÃºc</label>
                                        <input type="date" class="form-control" name="end_date" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>File Há»£p Äá»ng (PDF/Word)</label>
                                    <input type="file" class="form-control" name="contract_file">
                                </div>

                                <button type="submit" class="btn btn-gradient-primary me-2">LÆ°u Há»£p Äá»ng</button>
                                <a href="ListHopDongGV.php" class="btn btn-light">Há»§y</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
    </div>
</div>
