<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Truy cáº­p bá» tá»« chá»i! Báº¡n khÃ´ng cÃ³ quyá»n thÃªm GiÃ¡o viÃªn.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($fullname) || empty($email) || empty($password)) {
        $error = "Vui lÃ²ng Äiá»n Äáº§y Äá»§ thÃ´ng tin!";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email nÃ y ÄÃ£ ÄÆ°á»£c sá»­ dá»¥ng bá»i ngÆ°á»i khÃ¡c!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'teacher'; 

            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $_SESSION['status_message'] = "Thêm GiÃ¡o viÃªn thÃ nh cÃ´ng!";
                header("Location: ListGiaoVien.php");
                exit;
            } else {
                $error = "Lá»i há» thá»ng: " . $conn->error;
            }
        }
    }
}
?>

<?php 
include ROOT_PATH . "/admin/header.php"; 
include ROOT_PATH . "/admin/navbar.php"; 
?>

<div class="container-fluid page-body-wrapper">
    <?php include ROOT_PATH . "/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Thêm GiÃ¡o viÃªn má»i </h3>
            </div>
            
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">ThÃ´ng tin GiÃ¡o viÃªn</h4>
                            <p class="card-description"> TÃ i khoáº£n nÃ y sáº½ cÃ³ quyá»n táº¡o khÃ³a há»c vÃ  quáº£n lÃ½ há»c viÃªn. </p>

                            <?php if($error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <form class="forms-sample" method="POST">
                                <div class="form-group">
                                    <label>Há» vÃ  TÃªn <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="fullname" placeholder="Nguyá»n VÄn A" required>
                                </div>
                                <div class="form-group">
                                    <label>Email ÄÄng nháº­p <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" placeholder="teacher@example.com" required>
                                </div>
                                <div class="form-group">
                                    <label>Máº­t kháº©u <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                
                                <button type="submit" class="btn btn-gradient-danger me-2">Táº¡o tÃ i khoáº£n</button>
                                <a href="ListGiaoVien.php" class="btn btn-light">Há»§y</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>
