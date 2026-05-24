<?php
session_start();
include(__DIR__ . '/../../../config.php');
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'teacher')) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
function create_slug($string) {
    if (!$string) return 'course-' . time();
    $search = array('Ã ','Ã¡','áº¡','áº£','Ã£','Ã¢','áº§','áº¥','áº­','áº©','áº«','Ä','áº±','áº¯','áº·','áº³','áºµ','Ã¨','Ã©','áº¹','áº»','áº½','Ãª','á»','áº¿','á»','á»','á»','Ã¬','Ã­','á»','á»','Ä©','Ã²','Ã³','á»','á»','Ãµ','Ã´','á»','á»','á»','á»','á»','Æ¡','á»','á»','á»£','á»','á»¡','Ã¹','Ãº','á»¥','á»§','Å©','Æ°','á»«','á»©','á»±','á»­','á»¯','á»³','Ã½','á»µ','á»·','á»¹','Ä','Ã','Ã','áº ','áº¢','Ã','Ã','áº¦','áº¤','áº¬','áº¨','áºª','Ä','áº°','áº®','áº¶','áº²','áº´','Ã','Ã','áº¸','áºº','áº¼','Ã','á»','áº¾','á»','á»','á»','Ã','Ã','á»','á»','Ä¨','Ã','Ã','á»','á»','Ã','Ã','á»','á»','á»','á»','á»','Æ ','á»','á»','á»¢','á»','á» ','Ã','Ã','á»¤','á»¦','Å¨','Æ¯','á»ª','á»¨','á»°','á»¬','á»®','á»²','Ã','á»´','á»¶','á»¸','Ä');
    $replace = array('a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','e','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y','d','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','e','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y','d');
    
    $string = str_replace($search, $replace, $string);
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\- ]/', '', $string); 
    $string = str_replace(' ', '-', $string);
    $string = preg_replace('/-+/', '-', $string); 
    return trim($string, '-');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $status = $_POST['status'];
    
    $cat_raw = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $category_id = ($cat_raw > 0) ? $cat_raw : NULL;
    if ($role == 'admins' || $role == 'admin') {
        $teacher_id = intval($_POST['teacher_id']);
    } else {
        $checkOwner = $conn->query("SELECT teacher_id FROM courses WHERE id = $id")->fetch_assoc();
        if ($checkOwner['teacher_id'] != $user_id) {
            die("Báº¡n khÃ´ng cÃ³ quyá»n sá»­a khÃ³a há»c nÃ y!");
        }
        $teacher_id = $user_id;
    }

    $oldData = $conn->query("SELECT thumbnail FROM courses WHERE id = $id")->fetch_assoc();
    $thumbnail_path = $oldData['thumbnail'];
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $target_dir = "../../../uploads/courses/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        $slug = create_slug($title);
        if (empty($slug)) $slug = 'course-' . time();
        $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $new_name = $slug . '-' . time() . '.' . $ext;
        
        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_dir . $new_name)) {
            $thumbnail_path = "uploads/courses/" . $new_name;
            if (!empty($oldData['thumbnail'])) {
                $old_file_absolute = ROOT_PATH . '/' . $oldData['thumbnail'];
                if (file_exists($old_file_absolute)) {
                    unlink($old_file_absolute);
                }
            }
        }
    }
    $new_slug = create_slug($title);
    if (empty($new_slug)) $new_slug = 'course-' . time() . '-' . rand(100,999);
    $checkSlug = $conn->query("SELECT id FROM courses WHERE slug = '$new_slug' AND id != $id");
    if ($checkSlug->num_rows > 0) {
        $new_slug .= '-' . time();
    }

    $sql = "UPDATE courses SET 
            title = ?, 
            slug = ?, 
            description = ?, 
            price = ?, 
            status = ?, 
            teacher_id = ?, 
            category_id = ?, 
            thumbnail = ? 
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdsiisi", $title, $new_slug, $description, $price, $status, $teacher_id, $category_id, $thumbnail_path, $id);

    if ($stmt->execute()) {
        $_SESSION['status_message'] = "Cáº­p nháº­t khÃ³a há»c thÃ nh cÃ´ng!";
        header("Location: ListKhoaHoc.php");
        exit;
    } else {
        echo "Lá»i Database: " . $stmt->error;
    }
}
?>
