<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

if ($_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
if(!$user) die("KhÃ´ng tá»n táº¡i!");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET fullname=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $fullname, $email, $hash, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET fullname=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $fullname, $email, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['status_message'] = "Cáº­p nháº­t thÃ nh cÃ´ng!";
        header("Location: ListHocVien.php");
        exit;
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
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Sá»­a thÃ´ng tin Há»c viÃªn</h4>
                            <form class="forms-sample" method="POST">
                                <div class="form-group">
                                    <label>Há» vÃ  TÃªn</label>
                                    <input type="text" class="form-control" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Máº­t kháº©u má»i (Äá» trá»ng náº¿u khÃ´ng Äá»i)</label>
                                    <input type="password" class="form-control" name="password">
                                </div>
                                <button type="submit" class="btn btn-gradient-warning me-2">Cáº­p nháº­t</button>
                                <a href="ListHocVien.php" class="btn btn-light">Há»§y</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
    </div>
</div>
