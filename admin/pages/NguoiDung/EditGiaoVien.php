<?php
session_start();
include(__DIR__ . '/../../../config.php');

// 1. CHá» SUPER ADMIN Má»I ÄÆ¯á»¢C VÃO TRANG NÃY
// VÃ¬ trang nÃ y cho phÃ©p Äá»i quyá»n háº¡n, nÃªn pháº£i báº£o máº­t ká»¹
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    die("Truy cáº­p bá» tá»« chá»i! Chá» Super Admin má»i cÃ³ quyá»n sá»­a Äá»i cáº¥p báº­c thÃ nh viÃªn.");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';

// 2. Láº¤Y THÃNG TIN USER
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("NgÆ°á»i dÃ¹ng khÃ´ng tá»n táº¡i!");
}

// 3. Xá»¬ LÃ FORM Cáº¬P NHáº¬T
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $role_update = $_POST['role']; // Láº¥y quyá»n má»i tá»« form
    
    // Validate cÆ¡ báº£n
    if (empty($fullname) || empty($email)) {
        $message = "<div class='alert alert-danger'>Vui lÃ²ng Äiá»n Äáº§y Äá»§ tÃªn vÃ  email!</div>";
    } else {
        // Cáº¬P NHáº¬T DATABASE (Bao gá»m cáº£ cá»t ROLE)
        $sql = "UPDATE users SET fullname = ?, email = ?, role = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql);
        $stmt_update->bind_param("sssi", $fullname, $email, $role_update, $id);
        
        if ($stmt_update->execute()) {
            $_SESSION['status_message'] = "Cáº­p nháº­t thÃ nh cÃ´ng! {$user['fullname']} giá» lÃ : $role_update";
            
            // Äiá»u hÆ°á»ng vá» ÄÃºng danh sÃ¡ch dá»±a trÃªn role má»i
            if ($role_update == 'student') header("Location: ListHocVien.php");
            else header("Location: ListGiaoVien.php"); // Teacher hoáº·c Admin thÃ¬ vá» ÄÃ¢y
            exit;
        } else {
            $message = "<div class='alert alert-danger'>Lá»i: " . $conn->error . "</div>";
        }
    }
}
?>

<?php include ROOT_PATH . "/admin/header.php"; ?>
<?php include ROOT_PATH . "/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
    <?php include ROOT_PATH . "/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Chá»nh sá»­a thÃ´ng tin & Cáº¥p quyá»n </h3>
            </div>
            
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Há» sÆ¡: <?= htmlspecialchars($user['fullname']) ?></h4>
                            <?= $message ?>

                            <form class="forms-sample" method="POST">
                                <div class="form-group">
                                    <label>Há» vÃ  TÃªn</label>
                                    <input type="text" class="form-control" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Email (TÃ i khoáº£n ÄÄng nháº­p)</label>
                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>

                                <div class="form-group bg-light p-3 border rounded">
                                    <label class="font-weight-bold text-primary">Cáº¥p báº­c / Vai trÃ² (Role)</label>
                                    
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="role" value="student" <?= ($user['role'] == 'student') ? 'checked' : '' ?>>
                                            Há»c viÃªn (Student)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="role" value="teacher" <?= ($user['role'] == 'teacher') ? 'checked' : '' ?>>
                                            GiÃ¡o viÃªn (Teacher)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="role" value="admins" <?= ($user['role'] == 'admins') ? 'checked' : '' ?>>
                                            Quáº£n trá» viÃªn (Admin)
                                        </label>
                                    </div>
                                <button type="submit" class="btn btn-gradient-primary me-2">LÆ°u thay Äá»i</button>
                                <a href="ListHocVien.php" class="btn btn-light">Há»§y</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>
