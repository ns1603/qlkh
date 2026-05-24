<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$course_id = intval($_GET['course_id']);

$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    die("Khóa học không tồn tại");
}

$transfer_code = "KHOAHOC" . $course_id;
$bank_account  = "123456789";
?>

<?php include 'header.php'; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600&display=swap" rel="stylesheet">

<style>
* { box-sizing: border-box; }
body { font-family: 'Be Vietnam Pro', sans-serif; background: #f4f5f7; }

.checkout-wrapper { max-width: 900px; margin: 0 auto; padding: 2rem 1rem; }

/* Step bar */
.step-bar { display: flex; gap: 0; margin-bottom: 1.75rem; }
.step-item { flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; }
.step-item:not(:last-child)::after {
    content: ''; position: absolute; top: 14px; left: 50%;
    width: 100%; height: 1px; background: #dee2e6; z-index: 0;
}
.step-item.active:not(:last-child)::after { background: #185FA5; }
.step-dot {
    width: 28px; height: 28px; border-radius: 50%; z-index: 1;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 500; background: #f4f5f7;
    border: 1px solid #dee2e6; color: #6c757d;
}
.step-item.done .step-dot  { background: #3B6D11; color: #fff; border-color: #3B6D11; }
.step-item.active .step-dot { background: #185FA5; color: #fff; border-color: #185FA5; }
.step-label { font-size: 11px; color: #6c757d; margin-top: 5px; }
.step-item.active .step-label { color: #185FA5; font-weight: 500; }

/* Grid */
.checkout-grid { display: grid; grid-template-columns: 1fr 360px; gap: 1.5rem; }
@media (max-width: 680px) { .checkout-grid { grid-template-columns: 1fr; } }

/* Cards */
.ck-card {
    background: #fff; border-radius: 12px;
    border: 1px solid #e9ecef; padding: 1.5rem; margin-bottom: 1rem;
}
.ck-section-label {
    font-size: 11px; font-weight: 600; color: #6c757d;
    text-transform: uppercase; letter-spacing: .06em; margin-bottom: 1rem;
}

/* Course badge */
.course-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: #EAF3DE; color: #3B6D11;
    font-size: 12px; font-weight: 500; padding: 4px 10px;
    border-radius: 6px; margin-bottom: .75rem;
}
.course-title { font-size: 17px; font-weight: 600; margin-bottom: .5rem; line-height: 1.4; }
.course-meta { display: flex; gap: 16px; font-size: 13px; color: #6c757d; margin-bottom: 1rem; flex-wrap: wrap; }

/* Bank card */
.bank-card {
    background: linear-gradient(135deg, #185FA5, #0c3d75);
    border-radius: 12px; padding: 1.25rem 1.5rem; color: #fff;
    position: relative; overflow: hidden; margin-bottom: 1rem;
}
.bank-card::before {
    content: ''; position: absolute; right: -20px; top: -20px;
    width: 90px; height: 90px; border-radius: 50%; background: rgba(255,255,255,0.07);
}
.bank-stk { font-size: 20px; font-weight: 600; letter-spacing: .08em; margin: 6px 0 10px; }
.bank-footer { display: flex; justify-content: space-between; align-items: center; font-size: 12px; opacity: .85; }
.copy-btn-white {
    background: rgba(255,255,255,0.15); border: none; color: #fff;
    font-size: 11px; padding: 4px 10px; border-radius: 5px; cursor: pointer;
    transition: background .15s;
}
.copy-btn-white:hover { background: rgba(255,255,255,0.3); }

/* Transfer info rows */
.transfer-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 9px 0; font-size: 14px;
    border-bottom: 1px solid #f1f3f5;
}
.transfer-row:last-child { border-bottom: none; }
.transfer-row .label { color: #6c757d; }
.transfer-row .value { font-weight: 500; display: flex; align-items: center; gap: 6px; }
.code-chip {
    background: #EEEDFE; color: #3C3489; font-size: 12px;
    padding: 3px 9px; border-radius: 5px; font-family: monospace; letter-spacing: .04em;
}
.btn-icon-copy {
    background: none; border: none; cursor: pointer; color: #6c757d;
    font-size: 16px; padding: 2px;
    transition: color .15s;
}
.btn-icon-copy:hover { color: #185FA5; }

/* Notice */
.ck-notice {
    display: flex; gap: 10px; align-items: flex-start;
    background: #e8f4fe; border-left: 3px solid #378ADD;
    border-radius: 0 8px 8px 0; padding: 12px;
    font-size: 13px; color: #185FA5; margin-top: 1rem;
}

/* Include list */
.include-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #495057; padding: 5px 0; }
.include-item i { color: #3B6D11; font-size: 15px; }

/* Summary */
.summary-sticky { position: sticky; top: 1rem; }
.price-row { display: flex; justify-content: space-between; font-size: 13px; color: #6c757d; padding: 6px 0; }
.price-row .del { text-decoration: line-through; }
.price-row .disc { color: #3B6D11; font-weight: 500; }
.total-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; }
.total-label { font-size: 15px; font-weight: 600; }
.total-price { font-size: 22px; font-weight: 700; color: #D85A30; }

/* Submit button */
.btn-submit {
    width: 100%; padding: 14px; border: none; border-radius: 8px;
    background: #185FA5; color: #fff; font-size: 15px; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    gap: 8px; margin-top: 1rem; transition: background .15s, transform .1s;
    font-family: inherit;
}
.btn-submit:hover { background: #0c3d75; }
.btn-submit:active { transform: scale(.98); }

.guarantee {
    display: flex; align-items: center; gap: 6px; justify-content: center;
    font-size: 12px; color: #6c757d; margin-top: 12px;
}
.guarantee i { color: #3B6D11; font-size: 15px; }
.secure-note { text-align: center; font-size: 12px; color: #adb5bd; margin-top: 1rem; }
</style>

<div class="checkout-wrapper">

    <!-- Breadcrumb -->
    <a href="course.php?id=<?= $course_id ?>" class="text-muted text-decoration-none" style="font-size:13px;">
        ← Quay lại khóa học
    </a>

    <!-- Step bar -->
    <div class="step-bar mt-3">
        <div class="step-item done">
            <div class="step-dot">✓</div>
            <span class="step-label">Giỏ hàng</span>
        </div>
        <div class="step-item active">
            <div class="step-dot">2</div>
            <span class="step-label">Thanh toán</span>
        </div>
        <div class="step-item">
            <div class="step-dot">3</div>
            <span class="step-label">Hoàn tất</span>
        </div>
    </div>

    <div class="checkout-grid">

        <!-- LEFT -->
        <div>

            <!-- Course info -->
            <div class="ck-card">
                <span class="course-badge">📚 Khóa học trực tuyến</span>
                <p class="course-title"><?= htmlspecialchars($course['title']) ?></p>
                <div class="course-meta">
                    <span>⏱ 42 giờ học</span>
                    <span>👥 1.280 học viên</span>
                    <span>⭐ 4.8 / 5</span>
                </div>
                <hr>
                <p class="ck-section-label">Bao gồm</p>
                <div class="include-item"><i class="bi bi-check-circle-fill"></i> Truy cập trọn đời</div>
                <div class="include-item"><i class="bi bi-check-circle-fill"></i> Video bài giảng HD</div>
                <div class="include-item"><i class="bi bi-check-circle-fill"></i> Tài liệu &amp; source code</div>
                <div class="include-item"><i class="bi bi-check-circle-fill"></i> Chứng chỉ hoàn thành</div>
            </div>

            <!-- Bank transfer -->
            <div class="ck-card">
                <p class="ck-section-label">Thông tin chuyển khoản</p>

                <div class="bank-card">
                    <p style="font-size:13px; opacity:.8;">🏦 MB Bank</p>
                    <p class="bank-stk"><?= $bank_account ?></p>
                    <div class="bank-footer">
                        <span>NGUYEN VAN A</span>
                        <button class="copy-btn-white"
                            onclick="navigator.clipboard.writeText('<?= $bank_account ?>').then(()=>{this.textContent='Đã sao chép ✓'; setTimeout(()=>this.textContent='Sao chép STK',1500)})">
                            Sao chép STK
                        </button>
                    </div>
                </div>

                <div class="transfer-row">
                    <span class="label">Ngân hàng</span>
                    <span class="value">MB Bank</span>
                </div>
                <div class="transfer-row">
                    <span class="label">Số tài khoản</span>
                    <span class="value"><?= $bank_account ?></span>
                </div>
                <div class="transfer-row">
                    <span class="label">Chủ tài khoản</span>
                    <span class="value">NGUYEN VAN A</span>
                </div>
                <div class="transfer-row">
                    <span class="label">Nội dung CK</span>
                    <span class="value">
                        <span class="code-chip"><?= $transfer_code ?></span>
                        <button class="btn-icon-copy"
                            onclick="navigator.clipboard.writeText('<?= $transfer_code ?>').then(()=>{this.innerHTML='✓'; setTimeout(()=>this.innerHTML='⧉',1200)})"
                            title="Sao chép">⧉</button>
                    </span>
                </div>

                <div class="ck-notice">
                    ℹ️
                    <span>Nhập <strong>đúng nội dung</strong> chuyển khoản để hệ thống tự động kích hoạt trong 5–10 phút.</span>
                </div>
            </div>

        </div>

        <!-- RIGHT: Summary -->
        <div class="summary-sticky">
            <div class="ck-card">
                <p style="font-size:15px; font-weight:600; margin-bottom:1rem;">Tóm tắt đơn hàng</p>

                <div class="price-row">
                    <span>Giá gốc</span>
                    <span class="del"><?= number_format($course['price'] * 1.2) ?> đ</span>
                </div>
                <div class="price-row">
                    <span>Giảm giá</span>
                    <span class="disc">- <?= number_format($course['price'] * 0.2) ?> đ</span>
                </div>

                <hr>

                <div class="total-row">
                    <span class="total-label">Tổng thanh toán</span>
                    <span class="total-price"><?= number_format($course['price']) ?> đ</span>
                </div>

                <form action="enroll_process.php" method="POST">
                    <input type="hidden" name="course_id" value="<?= $course_id ?>">
                    <button type="submit" class="btn-submit">
                        ✅ Tôi đã chuyển khoản
                    </button>
                </form>

                <p class="guarantee">🛡 Hoàn tiền 100% trong 7 ngày nếu không hài lòng</p>
            </div>

            <p class="secure-note">🔒 Thanh toán an toàn &amp; bảo mật</p>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>