<?php
session_start();
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admins') {
    die('Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!');
}
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    $_SESSION['status_message'] = "KhÃ´ng tÃ¬m tháº¥y danh má»¥c!";
    header("Location: ListDanhMuc.php");
    exit;
}

function create_slug($string) {
    $search = array('Ã ', 'Ã¡', 'áº¡', 'áº£', 'Ã£', 'Ã¢', 'áº§', 'áº¥', 'áº­', 'áº©', 'áº«', 'Ä', 'áº±', 'áº¯', 'áº·', 'áº³', 'áºµ', 'Ã¨', 'Ã©', 'áº¹', 'áº»', 'áº½', 'Ãª', 'á»', 'áº¿', 'á»', 'á»', 'á»', 'Ã¬', 'Ã­', 'á»', 'á»', 'Ä©', 'Ã²', 'Ã³', 'á»', 'á»', 'Ãµ', 'Ã´', 'á»', 'á»', 'á»', 'á»', 'á»', 'Æ¡', 'á»', 'á»', 'á»£', 'á»', 'á»¡', 'Ã¹', 'Ãº', 'á»¥', 'á»§', 'Å©', 'Æ°', 'á»«', 'á»©', 'á»±', 'á»­', 'á»¯', 'á»³', 'Ã½', 'á»µ', 'á»·', 'á»¹', 'Ä', 'Ã', 'Ã', 'áº ', 'áº¢', 'Ã', 'Ã', 'áº¦', 'áº¤', 'áº¬', 'áº¨', 'áºª', 'Ä', 'áº°', 'áº®', 'áº¶', 'áº²', 'áº´', 'Ã', 'Ã', 'áº¸', 'áºº', 'áº¼', 'Ã', 'á»', 'áº¾', 'á»', 'á»', 'á»', 'Ã', 'Ã', 'á»', 'á»', 'Ä¨', 'Ã', 'Ã', 'á»', 'á»', 'Ã', 'Ã', 'á»', 'á»', 'á»', 'á»', 'á»', 'Æ ', 'á»', 'á»', 'á»¢', 'á»', 'á» ', 'Ã', 'Ã', 'á»¤', 'á»¦', 'Å¨', 'Æ¯', 'á»ª', 'á»¨', 'á»°', 'á»¬', 'á»®', 'á»²', 'Ã', 'á»´', 'á»¶', 'á»¸', 'Ä', ' ');
    $replace = array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'y', 'y', 'y', 'y', 'y', 'd', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'Y', 'Y', 'Y', 'Y', 'Y', 'D', '-');
    $string = str_replace($search, $replace, $string);
    $string = strtolower($string);
    return preg_replace('/[^a-z0-9\-]/', '', $string);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $slug = create_slug($name);

    if (empty($name)) {
        $error = "TÃªn danh má»¥c khÃ´ng ÄÆ°á»£c Äá» trá»ng!";
    } else {
        $updateStmt = $conn->prepare("UPDATE categories SET name=?, slug=?, description=? WHERE id=?");
        $updateStmt->bind_param("sssi", $name, $slug, $description, $id);

        if ($updateStmt->execute()) {
            $_SESSION['status_message'] = "Cáº­p nháº­t danh má»¥c thÃ nh cÃ´ng!";
            header("Location: ListDanhMuc.php");
            exit;
        } else {
            $error = "Lá»i cáº­p nháº­t: " . $conn->error;
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
                <h3 class="page-title"> Chá»nh sá»­a Danh má»¥c </h3>
            </div>
            <div class="row">
                <div class="col-md-8 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Sá»­a thÃ´ng tin: <?= htmlspecialchars($category['name']) ?></h4>
                            
                            <?php if($error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <form class="forms-sample" method="POST">
                                <div class="form-group">
                                    <label for="name">TÃªn Danh má»¥c</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= htmlspecialchars($category['name']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">MÃ´ táº£</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($category['description']) ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-gradient-warning me-2">Cáº­p nháº­t</button>
                                <a href="ListDanhMuc.php" class="btn btn-light">Há»§y bá»</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>
