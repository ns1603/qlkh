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

if ($id > 0) {
    $conn->query("UPDATE courses SET category_id = NULL WHERE category_id = $id");

    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['status_message'] = "ÄÃ£ xÃ³a danh má»¥c thÃ nh cÃ´ng!";
    } else {
        $_SESSION['status_message'] = "Lá»i khi xÃ³a: " . $conn->error;
    }
} else {
    $_SESSION['status_message'] = "ID danh má»¥c khÃ´ng há»£p lá»!";
}

header("Location: ListDanhMuc.php");
exit;
?>
