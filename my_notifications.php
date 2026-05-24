<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

/* ===== ĐÁNH DẤU ĐÃ ĐỌC ===== */
if (isset($_GET['read_id'])) {
    $n_id = (int)$_GET['read_id'];
    $stmt = $conn->prepare("
        UPDATE notifications 
        SET is_read = 1 
        WHERE id = ? AND receiver_id = ?
    ");
    $stmt->bind_param("ii", $n_id, $user_id);
    $stmt->execute();
    header("Location: my_notifications.php");
    exit;
}

/* ===== LẤY THÔNG BÁO (HƯỚNG 2) ===== */
$sql = "
SELECT 
    n.*,
    CASE 
        WHEN n.sender_type = 'admin' THEN a.username
        ELSE u.fullname
    END AS sender_name,
    CASE 
        WHEN n.sender_type = 'admin' THEN 'admins'
        ELSE u.role
    END AS sender_role
FROM notifications n
LEFT JOIN users u 
    ON n.sender_type = 'user' AND n.sender_id = u.id
LEFT JOIN admins a 
    ON n.sender_type = 'admin' AND n.sender_id = a.id
WHERE n.receiver_id = ?
ORDER BY 
    n.is_pinned DESC,
    n.priority DESC,
    n.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
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

    .breadcrumbs-custom h2 {
        font-size: 3rem;
        font-weight: 700;
        color: #fff;
        margin: 0;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
    }
    
    .breadcrumbs-custom h2::before {
        content: '🔔';
        font-size: 3.5rem;
        animation: bellRing 2s ease-in-out infinite;
    }
    
    @keyframes bellRing {
        0%, 100% { transform: rotate(0deg); }
        10%, 30% { transform: rotate(-10deg); }
        20%, 40% { transform: rotate(10deg); }
        50% { transform: rotate(0deg); }
    }

    /* Notification Item Styling */
    .notif-item { 
        background: #fff;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.05);
        border-left: 4px solid transparent;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }
    
    .notif-item::before {
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
    
    .notif-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);
        border-color: rgba(102, 126, 234, 0.2);
    }
    
    .notif-item:hover::before {
        opacity: 1;
    }
    
    .notif-unread { 
        border-left-color: #11998e;
        background: linear-gradient(135deg, rgba(17, 153, 142, 0.05) 0%, rgba(56, 239, 125, 0.05) 100%);
        font-weight: 600;
        position: relative;
    }
    
    .notif-unread::after {
        content: '';
        position: absolute;
        top: 20px;
        right: 20px;
        width: 12px;
        height: 12px;
        background: #11998e;
        border-radius: 50%;
        box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.2);
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.7;
        }
    }
    
    .notif-read { 
        border-left-color: #e5e7eb;
        color: #6c757d;
        opacity: 0.9;
    }
    
    .notif-read:hover {
        opacity: 1;
    }

    /* Badge Styling */
    .sender-badge-admin { 
        background: linear-gradient(135deg, #d63031 0%, #e74c3c 100%);
        color: #fff; 
        padding: 6px 12px; 
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(214, 48, 49, 0.3);
        display: inline-block;
        margin-right: 10px;
    }
    
    .sender-badge-teacher { 
        background: linear-gradient(135deg, #0984e3 0%, #00b894 100%);
        color: #fff; 
        padding: 6px 12px; 
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(9, 132, 227, 0.3);
        display: inline-block;
        margin-right: 10px;
    }
    
    .badge-priority {
        background: linear-gradient(135deg, #fdcb6e 0%, #f39c12 100%);
        color: #000;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 12px;
        margin-left: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 6px rgba(253, 203, 110, 0.4);
        display: inline-block;
    }
    
    .pin-icon {
        font-size: 1.2rem;
        margin-right: 8px;
        animation: pinPulse 2s ease-in-out infinite;
    }
    
    @keyframes pinPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    /* Notification Content */
    .notif-item h5 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 12px;
        line-height: 1.5;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .notif-item .text-muted {
        color: #6c757d !important;
        font-size: 0.9rem;
    }
    
    .notif-item p {
        color: #4a5568;
        line-height: 1.7;
        margin-bottom: 15px;
        white-space: pre-line;
        font-size: 0.95rem;
    }
    
    .notif-item small {
        color: #6c757d;
        font-size: 0.85rem;
    }
    
    .notif-item strong {
        color: #2c3e50;
        font-weight: 600;
    }

    /* Button Styling */
    .btn-outline-success {
        border: 2px solid #11998e;
        color: #11998e;
        background: transparent;
        padding: 8px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-outline-success:hover {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border-color: transparent;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
    }
    
    .btn-outline-success:active {
        transform: translateY(0);
    }

    /* Empty State */
    .empty-state {
    text-align: center;
    padding: 80px 20px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

    
    .empty-state img {
        width: 120px;
        height: 120px;
        opacity: 0.5;
        margin-bottom: 25px;
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    .empty-state h4 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #6c757d;
        font-size: 1rem;
    }

    /* List Group */
    .list-group {
        border: none;
        background: transparent;
    }
    
    .list-group-item {
        border: none;
        padding: 25px;
        position: relative;
        z-index: 1;
    }

    /* Section Styling */
    .section {
        padding: 60px 0;
    }
    
    .section-md {
        padding: 40px 0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .breadcrumbs-custom h2 {
            font-size: 2rem;
        }
        
        .breadcrumbs-custom h2::before {
            font-size: 2.5rem;
        }
        
        .notif-item {
            padding: 20px !important;
        }
        
        .notif-item h5 {
            font-size: 1rem;
        }
        
        .sender-badge-admin,
        .sender-badge-teacher {
            font-size: 0.65rem;
            padding: 4px 8px;
        }
        
        .badge-priority {
            font-size: 0.6rem;
            padding: 3px 8px;
        }
    }
</style>

<div class="breadcrumbs-custom">
    <div class="container">
        <h2>Hộp thư thông báo</h2>
    </div>
</div>

<section class="section section-md bg-default">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-10">

<?php if ($result->num_rows === 0): ?>
    <div class="empty-state">
        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076478.png" alt="No notifications">
        <h4>Bạn chưa có thông báo nào</h4>
        <p>Thông báo mới sẽ xuất hiện ở đây khi có cập nhật</p>
    </div>
<?php endif; ?>

<div class="list-group shadow-sm">

<?php while ($row = $result->fetch_assoc()): ?>

<?php
    $is_read = (int)$row['is_read'];
    $itemClass = $is_read ? 'notif-read' : 'notif-unread';

    if ($row['sender_role'] === 'admins') {
        $roleLabel  = 'HỆ THỐNG';
        $badgeClass = 'sender-badge-admin';
    } else {
        $roleLabel  = 'GIẢNG VIÊN';
        $badgeClass = 'sender-badge-teacher';
    }

    $priorityBadge = ($row['priority'] === 'important')
        ? '<span class="badge badge-priority">ƯU TIÊN</span>'
        : '';

    $pinIcon = $row['is_pinned'] ? '📌 ' : '';
?>

<div class="list-group-item notif-item <?= $itemClass ?>">

    <div class="d-flex justify-content-between align-items-start mb-3">
        <h5 class="mb-0 flex-grow-1">
            <?php if ($row['is_pinned']): ?>
                <span class="pin-icon">📌</span>
            <?php endif; ?>
            <span class="<?= $badgeClass ?>"><?= $roleLabel ?></span>
            <?= htmlspecialchars($row['title']) ?>
            <?= $priorityBadge ?>
        </h5>
        <small class="text-muted ms-3" style="white-space: nowrap;">
            <i class="mdi mdi-clock-outline"></i> <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
        </small>
    </div>

    <p class="mb-3">
        <?= htmlspecialchars($row['message']) ?>
    </p>

    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
        <small class="text-muted">
            <i class="mdi mdi-account-circle"></i> Gửi bởi: <strong><?= htmlspecialchars($row['sender_name']) ?></strong>
        </small>

        <?php if (!$is_read): ?>
            <a href="my_notifications.php?read_id=<?= $row['id'] ?>" 
               class="btn btn-outline-success">
                <i class="mdi mdi-check-circle"></i> Đánh dấu đã đọc
            </a>
        <?php else: ?>
            <span class="text-muted small">
                <i class="mdi mdi-check-circle" style="color: #11998e;"></i> Đã xem
            </span>
        <?php endif; ?>
    </div>

</div>

<?php endwhile; ?>

</div>
</div>
</div>
</div>
</section>

<?php include 'footer.php'; ?>
