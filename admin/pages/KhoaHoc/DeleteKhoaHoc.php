<?php
session_start();
include(__DIR__ . '/../../../config.php');

// 1. KIá»M TRA ÄÄNG NHáº¬P
if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Ãp kiá»u sá» nguyÃªn Äá» báº£o máº­t

if ($id > 0) {
    // 2. Láº¤Y THÃNG TIN KHÃA Há»C TRÆ¯á»C (Äá» kiá»m tra quyá»n vÃ  láº¥y tÃªn áº£nh thumbnail)
    $stmt = $conn->prepare("SELECT id, teacher_id, thumbnail FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();

    if ($course) {
        // 3. PHÃN QUYá»N: Teacher chá» ÄÆ°á»£c xÃ³a bÃ i cá»§a mÃ¬nh
        if ($role == 'teacher' && $course['teacher_id'] != $user_id) {
            $_SESSION['status_message'] = "Cáº¢NH BÃO: Báº¡n khÃ´ng cÃ³ quyá»n xÃ³a khÃ³a há»c cá»§a ngÆ°á»i khÃ¡c!";
            header("Location: ListKhoaHoc.php");
            exit;
        }

        // 4. XÃA áº¢NH THUMBNAIL TRÃN SERVER (Dá»n rÃ¡c)
        if (!empty($course['thumbnail'])) {
            // ÄÆ°á»ng dáº«n thá»±c táº¿ file áº£nh trÃªn á» cá»©ng
            // LÆ°u Ã½: Cáº§n chá»nh ÄÆ°á»ng dáº«n nÃ y khá»p vá»i cáº¥u trÃºc thÆ° má»¥c cá»§a báº¡n
            // Giáº£ sá»­ config.php náº±m á» root/admin/pages/KhoaHoc/../../../ -> root
            $file_path = ROOT_PATH . '/' . $course['thumbnail'];
            
            if (file_exists($file_path)) {
                unlink($file_path); // HÃ m xÃ³a file
            }
        }

        // 5. XÃA Dá»® LIá»U TRONG DATABASE
        // LÆ°u Ã½: Náº¿u báº¡n chÆ°a cÃ i Äáº·t 'ON DELETE CASCADE' trong MySQL cho cÃ¡c báº£ng con (lessons, enrollments...),
        // báº¡n cáº§n xÃ³a dá»¯ liá»u á» báº£ng con trÆ°á»c hoáº·c dÃ¹ng lá»nh nÃ y sáº½ bá» lá»i khÃ³a ngoáº¡i.
        // Giáº£ sá»­ báº¡n ÄÃ£ thiáº¿t láº­p Database chuáº©n, ta xÃ³a course:
        
        $delStmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $delStmt->bind_param("i", $id);

        if ($delStmt->execute()) {
            $_SESSION['status_message'] = "ÄÃ£ xÃ³a khÃ³a há»c thÃ nh cÃ´ng!";
        } else {
            $_SESSION['status_message'] = "Lá»i Database: KhÃ´ng thá» xÃ³a (CÃ³ thá» do rÃ ng buá»c dá»¯ liá»u há»c viÃªn/bÃ i giáº£ng).";
        }
    } else {
        $_SESSION['status_message'] = "KhÃ³a há»c khÃ´ng tá»n táº¡i!";
    }
} else {
    $_SESSION['status_message'] = "ID khÃ´ng há»£p lá»!";
}

// 6. QUAY Vá» TRANG DANH SÃCH
header("Location: ListKhoaHoc.php");
exit;
?>
