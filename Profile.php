<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];
    $avatar_path = ""; 
    $stmt = $conn->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $old_data = $stmt->get_result()->fetch_assoc();
    $avatar_db = $old_data['avatar'];

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $target_dir = "uploads/avatars/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $new_filename = "user_" . $user_id . "_" . time() . "." . $ext;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_dir . $new_filename)) {
            $avatar_db = $target_dir . $new_filename; // Cập nhật đường dẫn mới
        }
    }

    if (!empty($new_password)) {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET fullname = ?, email = ?, password = ?, avatar = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $fullname, $email, $hash, $avatar_db, $user_id);
    } else {
        $sql = "UPDATE users SET fullname = ?, email = ?, avatar = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $fullname, $email, $avatar_db, $user_id);
    }

    if ($stmt->execute()) {
        $message = "Cập nhật hồ sơ thành công!";
        $_SESSION['fullname'] = $fullname;
    } else {
        $error = "Lỗi: " . $conn->error;
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$sql_courses = "SELECT c.id, c.title, c.thumbnail, c.slug, e.enrolled_at, e.id as enrollment_id
                FROM enrollments e
                JOIN courses c ON e.course_id = c.id
                WHERE e.user_id = ?
                ORDER BY e.enrolled_at DESC";
$stmt_courses = $conn->prepare($sql_courses);
$stmt_courses->bind_param("i", $user_id);
$stmt_courses->execute();
$my_courses = $stmt_courses->get_result();
?>

<?php include 'header.php'; ?>

<style>
    /* Breadcrumbs Styling */
    .breadcrumbs-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 100px 0;
        width: 100%;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .breadcrumbs-custom::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .breadcrumbs-custom-title {
        font-size: 3rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 15px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
    }
    
    .breadcrumbs-custom-title::before {
        content: '👤';
        font-size: 3.5rem;
    }

    .breadcrumbs-custom-path {
        list-style: none;
        padding: 0;
        margin-top: 25px;
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }

    .breadcrumbs-custom-path li {
        color: #fff;
        font-size: 0.95rem;
    }

    .breadcrumbs-custom-path li a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        padding: 5px 10px;
        border-radius: 4px;
    }
    
    .breadcrumbs-custom-path li a:hover {
        color: #fff;
        background: rgba(255,255,255,0.2);
    }

    .breadcrumbs-custom-path li.active {
        color: #fff;
        font-weight: 600;
    }
    
    .breadcrumbs-custom-path li:not(:last-child)::after {
        content: '/';
        margin-left: 15px;
        color: rgba(255,255,255,0.5);
    }

    /* Profile Section */
    .profile-section { 
        padding: 80px 0; 
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 600px;
    }
    
    /* Profile Card */
    .profile-card { 
        background: #fff; 
        padding: 40px; 
        border-radius: 20px; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.05);
        position: sticky;
        top: 20px;
        transition: all 0.3s ease;
    }
    
    .profile-card:hover {
        box-shadow: 0 15px 50px rgba(102, 126, 234, 0.15);
    }
    
    .profile-avatar-wrapper { 
        text-align: center; 
        margin-bottom: 25px;
        position: relative;
        display: inline-block;
        width: 100%;
    }
    
    .profile-avatar { 
        width: 150px; 
        height: 150px; 
        border-radius: 50%; 
        object-fit: cover; 
        border: 5px solid #fff;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
        display: block;
        margin: 0 auto;
    }
    
    .profile-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    }
    
    .profile-name { 
        font-weight: 700; 
        text-align: center; 
        margin-bottom: 8px; 
        color: #2c3e50;
        font-size: 1.5rem;
    }
    
    .profile-role { 
        text-align: center; 
        color: #667eea;
        font-size: 0.9rem; 
        margin-bottom: 30px; 
        text-transform: uppercase; 
        letter-spacing: 2px;
        font-weight: 600;
        padding: 6px 15px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-radius: 20px;
        display: inline-block;
    }

    /* Form Styling */
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-group label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        display: block;
        font-size: 0.9rem;
    }
    
    .form-control {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 18px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .form-control-sm {
        padding: 10px 15px;
        font-size: 0.9rem;
    }
    
    hr {
        border: none;
        border-top: 2px solid #f0f0f0;
        margin: 30px 0;
    }

    /* Button Styling */
    .button-primary-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: #fff;
        padding: 14px 30px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 6px 25px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        width: 100%;
        text-align: center;
    }
    
    .button-primary-gradient::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .button-primary-gradient:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .button-primary-gradient:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 35px rgba(102, 126, 234, 0.4);
        color: #fff;
    }

    /* Alert Styling */
    .alert {
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 20px;
        border: none;
        font-weight: 500;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
    }
    
    .alert-info {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        color: #0c5460;
        box-shadow: 0 4px 15px rgba(23, 162, 184, 0.2);
    }

    /* Course Section */
    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 30px;
        padding-left: 20px;
        border-left: 5px solid #667eea;
        position: relative;
    }
    
    .section-title::before {
        content: '📚';
        margin-right: 10px;
    }

    /* Course Item */
    .course-item { 
        background: #fff; 
        border-radius: 16px; 
        overflow: hidden; 
        display: flex; 
        margin-bottom: 25px; 
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
    }
    
    .course-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }
    
    .course-item:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.15);
        border-color: rgba(102, 126, 234, 0.2);
    }
    
    .course-item:hover::before {
        opacity: 1;
    }
    
    .course-thumb { 
        width: 250px; 
        height: 180px; 
        object-fit: cover; 
        flex-shrink: 0;
        transition: transform 0.4s ease;
    }
    
    .course-item:hover .course-thumb {
        transform: scale(1.1);
    }
    
    .course-info { 
        padding: 25px; 
        flex-grow: 1; 
        display: flex; 
        flex-direction: column; 
        justify-content: space-between;
        position: relative;
        z-index: 1;
    }
    
    .course-title { 
        font-size: 1.3rem; 
        font-weight: 700; 
        margin-bottom: 12px; 
        color: #2c3e50; 
        text-decoration: none;
        transition: color 0.3s ease;
        line-height: 1.4;
    }
    
    .course-title:hover {
        color: #667eea;
    }
    
    .course-meta { 
        font-size: 0.9rem; 
        color: #6c757d; 
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .course-meta i {
        color: #667eea;
        font-size: 1.1rem;
    }
    
    .btn-action { 
        font-size: 0.9rem; 
        padding: 10px 20px; 
        border-radius: 25px; 
        margin-right: 10px;
        margin-bottom: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
    }
    
    .button-sm {
        padding: 8px 18px;
        font-size: 0.85rem;
    }
    
    .button-default-outline {
        background: transparent;
        border: 2px solid #667eea;
        color: #667eea;
    }
    
    .button-default-outline:hover {
        background: #667eea;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    /* Grid System */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    
    .row > [class*="col-"] {
        padding-right: 15px;
        padding-left: 15px;
        position: relative;
        width: 100%;
    }
    
    @media (min-width: 992px) {
        .row > .col-lg-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
        
        .row > .col-lg-8 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .breadcrumbs-custom-title {
            font-size: 2rem;
        }
        
        .breadcrumbs-custom-title::before {
            font-size: 2.5rem;
        }
        
        .profile-section {
            padding: 40px 0;
        }
        
        .profile-card {
            padding: 25px;
            position: static;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
        }
        
        .course-item { 
            flex-direction: column; 
        }
        
        .course-thumb { 
            width: 100%; 
            height: 200px; 
        }
        
        .section-title {
            font-size: 1.5rem;
        }
    }
</style>

<div class="breadcrumbs-custom">
    <div class="container">
        <h1 class="breadcrumbs-custom-title">Hồ sơ cá nhân</h1>
        <ul class="breadcrumbs-custom-path">
            <li><a href="home.php">Trang chủ</a></li>
            <li class="active">Hồ sơ</li>
        </ul>
    </div>
</div>

<section class="profile-section">
    <div class="container">
        <div class="row row-40">
            
            <div class="col-lg-4">
                <div class="profile-card">
                    <div class="profile-avatar-wrapper">
                        <?php 
                            $avatar_url = !empty($user['avatar']) ? $user['avatar'] : 'images/default-avatar.png'; 
                        ?>
                        <img src="<?= $avatar_url ?>" alt="Avatar" class="profile-avatar">
                    </div>
                    <h4 class="profile-name"><?= htmlspecialchars($user['fullname']) ?></h4>
                    <p class="profile-role"><?= htmlspecialchars($user['role']) ?></p>

                    <?php if($message): ?>
                        <div class="alert alert-success text-center"><?= $message ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group mb-3">
                            <label class="small fw-bold">Họ và tên</label>
                            <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small fw-bold">Đổi Avatar</label>
                            <input type="file" name="avatar" class="form-control form-control-sm">
                        </div>
                        <hr>
                        <div class="form-group mb-3">
                            <label class="small fw-bold text-muted">Đổi mật khẩu (Bỏ trống nếu không đổi)</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Mật khẩu mới...">
                        </div>
                        <button type="submit" class="button button-block button-primary-gradient">Lưu thay đổi</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <h3 class="section-title wow fadeInLeft">Khóa học của tôi</h3>
                
                <?php if ($my_courses->num_rows > 0): ?>
                    <?php while($course = $my_courses->fetch_assoc()): 
                        $thumb = !empty($course['thumbnail']) ? $course['thumbnail'] : 'images/default.jpg';
                    ?>
                        <div class="course-item wow fadeInUp">
                            <a href="course_details.php?id=<?= $course['id'] ?>">
                                <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($course['title']) ?>" class="course-thumb">
                            </a>
                            
                            <div class="course-info">
                                <div>
                                    <a href="course_details.php?id=<?= $course['id'] ?>" class="course-title">
                                        <?= htmlspecialchars($course['title']) ?>
                                    </a>
                                    <p class="course-meta">
                                        <i class="mdi mdi-calendar"></i> 
                                        <span>Đăng ký ngày: <?= date('d/m/Y', strtotime($course['enrolled_at'])) ?></span>
                                    </p>
                                </div>
                                
                                <div>
                                    <a href="course_details.php?id=<?= $course['id'] ?>" class="button button-sm button-primary-gradient btn-action">
                                        <i class="mdi mdi-play-circle-outline"></i> 
                                        <span>Vào học ngay</span>
                                    </a>

                                    <a href="student_contract.php?id=<?= $course['enrollment_id'] ?>&course_id=<?= $course['id'] ?>" target="_blank" class="button button-sm button-default-outline btn-action">
                                        <i class="mdi mdi-file-document"></i> 
                                        <span>Xem hợp đồng</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="mdi mdi-information" style="margin-right: 8px;"></i>
                        Bạn chưa đăng ký khóa học nào. 
                        <a href="courses_list.php" style="font-weight: 700; color: #667eea; text-decoration: none;">Khám phá ngay!</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>