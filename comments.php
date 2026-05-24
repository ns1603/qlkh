<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    $c_lesson_id = intval($_POST['lesson_id']);
    $c_user_id = $_SESSION['user_id'];
    $c_content = trim($_POST['comment_text']);

    if (!empty($c_content)) {
        $stmt = $conn->prepare("INSERT INTO comments (user_id, lesson_id, comment_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $c_user_id, $c_lesson_id, $c_content);
        
        if ($stmt->execute()) {
            echo "<script>window.location.href = 'learning.php?id=$c_lesson_id';</script>";
            exit;
        }
    }
}

$sql_cmt = "SELECT c.*, u.fullname, u.avatar 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.lesson_id = ? 
            ORDER BY c.created_at DESC";

$stmt_cmt = $conn->prepare($sql_cmt);
$stmt_cmt->bind_param("i", $lesson_id);
$stmt_cmt->execute();
$comments = $stmt_cmt->get_result();
?>

<div class="comments-section mt-5">
    <h4 class="mb-4"><i class="mdi mdi-comment-multiple-outline"></i> Thảo luận & Hỏi đáp</h4>

    <div class="comment-form mb-5">
        <form method="POST">
            <input type="hidden" name="lesson_id" value="<?= $lesson_id ?>">
            <div class="form-group">
                <textarea name="comment_text" class="form-control" rows="3" placeholder="Bạn có thắc mắc gì về bài học này? Hãy đặt câu hỏi..." required></textarea>
            </div>
            <div class="text-right mt-2">
                <button type="submit" name="submit_comment" 
                        style="border: 2px solid #007bff;
                            border-radius: 6px;
                            padding: 8px 20px;
                            background-color: transparent;
                            color: #007bff;
                            font-weight: 600;
                            cursor: pointer;
                            display: inline-flex;
                            align-items: center;       
                            gap: 8px;
                            transition: all 0.3s ease;">
                    Gửi bình luận <i class="mdi mdi-send"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="comment-list">
        <?php if ($comments->num_rows > 0): ?>
            <?php while ($cmt = $comments->fetch_assoc()): 
                $avatar = !empty($cmt['avatar']) ? $cmt['avatar'] : 'images/default-avatar.png';
            ?>
                <div class="media mb-4 p-3 bg-white rounded shadow-sm border">
                    <img src="<?= $avatar ?>" class="mr-3 rounded-circle" alt="..." style="width: 50px; height: 50px; object-fit: cover;">
                    <div class="media-body">
                        <h6 class="mt-0 font-weight-bold text-primary">
                            <?= htmlspecialchars($cmt['fullname']) ?>
                            <small class="text-muted ml-2" style="font-size: 0.8rem; font-weight: normal;">
                                <?= date('d/m/Y H:i', strtotime($cmt['created_at'])) ?>
                            </small>
                        </h6>
                        <p class="mb-0 text-dark">
                            <?= nl2br(htmlspecialchars($cmt['comment_text'])) ?>
                        </p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted text-center">Chưa có bình luận nào. Hãy là người đầu tiên đặt câu hỏi!</p>
        <?php endif; ?>
    </div>
</div>