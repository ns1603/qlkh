<?php
session_start();
include(__DIR__ . '/../../../config.php');
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    die("Truy cáº­p bá» tá»« chá»i!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$my_id = $_SESSION['user_id'];

if ($id == $my_id) {
    $_SESSION['status_message'] = "Báº¡n khÃ´ng thá» tá»± xÃ³a tÃ i khoáº£n cá»§a chÃ­nh mÃ¬nh!";
    header("Location: ListGiaoVien.php");
    exit;
}

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'student'");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['status_message'] = "ÄÃ£ xÃ³a giÃ¡o viÃªn thÃ nh cÃ´ng!";
    } else {
        $_SESSION['status_message'] = "Lá»i: KhÃ´ng thá» xÃ³a (CÃ³ thá» giÃ¡o viÃªn nÃ y Äang phá»¥ trÃ¡ch khÃ³a há»c).";
    }
}

header("Location: ListGiaoVien.php");
exit;
?>
