<?php
session_start();
include(__DIR__ . '/../../../config.php');
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'teacher')) {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
function create_slug($string) {
    if (!$string) return 'course-' . time();
    $search = array('à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ','è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ','ì','í','ị','ỉ','ĩ','ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ','ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ','ỳ','ý','ỵ','ỷ','ỹ','đ','À','Á','Ạ','Ả','Ã','Â','Ầ','Ấ','Ậ','Ẩ','Ẫ','Ă','Ằ','Ắ','Ặ','Ẳ','Ẵ','È','É','Ẹ','Ẻ','Ẽ','Ê','Ề','Ế','Ệ','Ể','Ễ','Ì','Í','Ị','Ỉ','Ĩ','Ò','Ó','Ọ','Ỏ','Õ','Ô','Ồ','Ố','Ộ','Ổ','Ỗ','Ơ','Ờ','Ớ','Ợ','Ở','Ỡ','Ù','Ú','Ụ','Ủ','Ũ','Ư','Ừ','Ứ','Ự','Ử','Ữ','Ỳ','Ý','Ỵ','Ỷ','Ỹ','Đ');
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
            die("Bạn không có quyền sửa khóa học này!");
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
                $old_file_absolute = $_SERVER['DOCUMENT_ROOT'] . '/Learning/' . $oldData['thumbnail'];
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
        $_SESSION['status_message'] = "Cập nhật khóa học thành công!";
        header("Location: ListKhoaHoc.php");
        exit;
    } else {
        echo "Lỗi Database: " . $stmt->error;
    }
}
?>