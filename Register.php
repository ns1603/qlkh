<?php
session_start();
include 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();

        if ($stmt_check->get_result()->num_rows > 0) {
            $error = "Email này đã được sử dụng!";
        } else {
            $role        = 'student';
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $email, $hashed_pass, $role);

            if ($stmt->execute()) {
                $success = "Đăng ký thành công! Đang chuyển hướng...";
                echo "<script>setTimeout(()=>{ window.location.href='Login.php'; }, 2000);</script>";
            } else {
                $error = "Lỗi hệ thống: " . $conn->error;
            }
        }
    }
}
?>

<?php include 'header.php'; ?>

<style>
    /* ── Auth layout ── */
    .auth-bg {
        background: #eef2f7;
        min-height: calc(100vh - 64px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1rem;
    }
    .auth-wrap {
        display: grid;
        grid-template-columns: 1fr 1fr;
        max-width: 860px;
        width: 100%;
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    }
    @media (max-width: 640px) {
        .auth-wrap { grid-template-columns: 1fr; }
        .auth-side  { display: none; }
    }

    /* ── Left side panel ── */
    .auth-side {
        background: #0f1f3d;
        padding: 2.5rem 2rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 1.5rem;
    }
    .side-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: .5rem;
    }
    .side-logo-icon {
        width: 40px; height: 40px;
        background: #2563eb;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 15px; color: #fff;
    }
    .side-logo-text {
        font-size: 20px; font-weight: 700; color: #fff;
    }
    .side-logo-text span { color: #60a5fa; }
    .auth-side h3 {
        font-size: 21px; font-weight: 700; color: #fff; line-height: 1.35;
    }
    .auth-side .sub-desc {
        font-size: 13px; color: rgba(255,255,255,0.6); line-height: 1.7; margin-top: 6px;
    }
    .perk-list { display: flex; flex-direction: column; gap: 12px; }
    .perk-item {
        display: flex; align-items: flex-start; gap: 10px;
        font-size: 13px; color: rgba(255,255,255,0.8); line-height: 1.5;
    }
    .perk-dot {
        width: 20px; height: 20px; border-radius: 50%;
        background: rgba(96,165,250,0.2);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; margin-top: 1px;
        font-size: 11px; color: #60a5fa; font-weight: 700;
    }

    /* ── Form panel ── */
    .auth-form {
        padding: 2.25rem 2rem;
    }
    .auth-form h2 {
        font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 4px;
    }
    .auth-form .form-sub {
        font-size: 13px; color: #6b7280; margin-bottom: 1.5rem;
    }

    /* Alert */
    .vl-alert {
        display: flex; align-items: center; gap: 9px;
        padding: 10px 14px; border-radius: 8px;
        font-size: 13px; margin-bottom: 1rem;
        font-weight: 500;
    }
    .vl-alert.danger {
        background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;
    }
    .vl-alert.success {
        background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534;
    }

    /* Form fields */
    .vl-fg { margin-bottom: 14px; }
    .vl-fg label {
        display: block; font-size: 12px; font-weight: 600;
        color: #374151; margin-bottom: 5px; letter-spacing: .02em;
    }
    .vl-input-wrap { position: relative; }
    .vl-input-wrap .vl-icon {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%);
        font-size: 17px; color: #9ca3af; pointer-events: none;
    }
    .vl-input-wrap input {
        width: 100%; height: 46px;
        padding: 0 14px 0 40px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px; color: #111827;
        background: #f9fafb;
        transition: all .15s; outline: none;
        font-family: inherit;
    }
    .vl-input-wrap input:focus {
        background: #fff;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
    }

    /* Password row */
    .form-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    /* Strength bar */
    .pw-strength { height: 3px; background: #e5e7eb; border-radius: 2px; margin-top: 5px; overflow: hidden; }
    .pw-strength-fill { height: 100%; width: 0; border-radius: 2px; transition: width .3s, background .3s; }

    /* Submit */
    .btn-vl-submit {
        width: 100%; height: 48px;
        background: #2563eb; color: #fff;
        border: none; border-radius: 8px;
        font-size: 14px; font-weight: 600;
        cursor: pointer; margin-top: 6px;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: all .15s; font-family: inherit;
    }
    .btn-vl-submit:hover { background: #1d4ed8; transform: translateY(-1px); }
    .btn-vl-submit:active { transform: scale(.98); }

    /* Divider */
    .or-divider {
        display: flex; align-items: center; gap: 12px;
        margin: 14px 0; font-size: 12px; color: #9ca3af;
    }
    .or-divider::before, .or-divider::after {
        content: ''; flex: 1; height: 1px; background: #e5e7eb;
    }

    /* Google btn */
    .btn-google {
        width: 100%; height: 44px;
        border: 1px solid #e5e7eb; border-radius: 8px;
        background: #fff; color: #374151;
        font-size: 13px; font-weight: 500;
        cursor: pointer; display: flex;
        align-items: center; justify-content: center; gap: 9px;
        transition: background .15s; font-family: inherit;
    }
    .btn-google:hover { background: #f9fafb; }

    /* Footer link */
    .auth-switch {
        text-align: center; font-size: 13px;
        color: #6b7280; margin-top: 16px;
    }
    .auth-switch a {
        color: #2563eb; font-weight: 600; text-decoration: none;
    }
    .auth-switch a:hover { text-decoration: underline; }
</style>

<section class="auth-bg">
  <div class="auth-wrap">

    <!-- Left panel -->
    <div class="auth-side">
      <div>
        <div class="side-logo">
          <div class="side-logo-icon">VL</div>
          <span class="side-logo-text">V-<span>Learning</span></span>
        </div>
        <h3>Bắt đầu hành trình học tập của bạn</h3>
        <p class="sub-desc">Tham gia cùng hơn 50.000 học viên đang học mỗi ngày.</p>
      </div>
      <div class="perk-list">
        <div class="perk-item"><div class="perk-dot">✓</div><span>Truy cập 500+ khóa học chất lượng cao</span></div>
        <div class="perk-item"><div class="perk-dot">✓</div><span>Học theo tốc độ của bản thân, mọi lúc mọi nơi</span></div>
        <div class="perk-item"><div class="perk-dot">✓</div><span>Nhận chứng chỉ sau khi hoàn thành</span></div>
        <div class="perk-item"><div class="perk-dot">✓</div><span>Hoàn toàn miễn phí để tạo tài khoản</span></div>
      </div>
    </div>

    <!-- Form panel -->
    <div class="auth-form">
      <h2>Tạo tài khoản</h2>
      <p class="form-sub">Điền thông tin bên dưới để bắt đầu</p>

      <?php if ($error): ?>
        <div class="vl-alert danger">
          <i class="mdi mdi-alert-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="vl-alert success">
          <i class="mdi mdi-check-circle"></i> <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <form method="POST">
        <div class="vl-fg">
          <label for="fullname">Họ và tên</label>
          <div class="vl-input-wrap">
            <i class="mdi mdi-account-outline vl-icon"></i>
            <input type="text" id="fullname" name="fullname"
                   placeholder="Nguyễn Văn A" required
                   value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>">
          </div>
        </div>

        <div class="vl-fg">
          <label for="email">Email đăng nhập</label>
          <div class="vl-input-wrap">
            <i class="mdi mdi-email-outline vl-icon"></i>
            <input type="email" id="email" name="email"
                   placeholder="email@example.com" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
        </div>

        <div class="form-2col">
          <div class="vl-fg">
            <label for="password">Mật khẩu</label>
            <div class="vl-input-wrap">
              <i class="mdi mdi-lock-outline vl-icon"></i>
              <input type="password" id="password" name="password"
                     placeholder="Tối thiểu 6 ký tự" required
                     oninput="checkStrength(this.value)">
            </div>
            <div class="pw-strength">
              <div class="pw-strength-fill" id="pw-fill"></div>
            </div>
          </div>
          <div class="vl-fg">
            <label for="confirm_password">Xác nhận mật khẩu</label>
            <div class="vl-input-wrap">
              <i class="mdi mdi-lock-check-outline vl-icon"></i>
              <input type="password" id="confirm_password" name="confirm_password"
                     placeholder="Nhập lại mật khẩu" required>
            </div>
          </div>
        </div>

        <button type="submit" class="btn-vl-submit">
          <i class="mdi mdi-account-plus-outline"></i>
          Đăng ký miễn phí
        </button>
      </form>

      <div class="or-divider">hoặc</div>

      <button class="btn-google" type="button">
        <svg width="16" height="16" viewBox="0 0 18 18">
          <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844a4.14 4.14 0 0 1-1.796 2.716v2.259h2.908C16.658 14.392 17.64 12.083 17.64 9.2z"/>
          <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/>
          <path fill="#FBBC05" d="M3.964 10.706A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.706V4.962H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.038l3.007-2.332z"/>
          <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.962L3.964 7.294C4.672 5.163 6.656 3.58 9 3.58z"/>
        </svg>
        Tiếp tục với Google
      </button>

      <p class="auth-switch">
        Đã có tài khoản? <a href="Login.php">Đăng nhập ngay</a>
      </p>
    </div>

  </div>
</section>

<script>
function checkStrength(v) {
    const fill = document.getElementById('pw-fill');
    let w = 0, c = '#ef4444';
    if (v.length >= 6) { w = 33; }
    if (v.length >= 8 && /[A-Z]/.test(v) && /[0-9]/.test(v)) { w = 66; c = '#f59e0b'; }
    if (v.length >= 10 && /[A-Z]/.test(v) && /[0-9]/.test(v) && /[^a-zA-Z0-9]/.test(v)) { w = 100; c = '#22c55e'; }
    fill.style.width = w + '%';
    fill.style.background = c;
}
</script>

<?php include 'footer.php'; ?>