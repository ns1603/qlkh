<?php
session_start();
include 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, fullname, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['fullname']  = $user['fullname'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['email']     = $email;

        if (in_array($user['role'], ['admin', 'admins', 'teacher'])) {
            header("Location: admin/index.php");
        } else {
            header("Location: home.php");
        }
        exit;
    } else {
        $error = "Email hoặc mật khẩu không chính xác!";
    }
}
?>

<?php include 'header.php'; ?>

<link rel="stylesheet" href="css/auth.css">

<section class="auth-bg">
  <div class="auth-wrap">

    <!-- Panel trái -->
    <div class="auth-side">
      <div>
        <div class="side-logo">
          <div class="side-logo-icon">VL</div>
          <span class="side-logo-text">V-<span>Learning</span></span>
        </div>
        <h3 style="margin-top: 1.25rem;">Chào mừng trở lại!</h3>
        <p class="sub-desc">Đăng nhập để tiếp tục hành trình học tập của bạn.</p>
      </div>
      <div class="perk-list">
        <div class="perk-item"><div class="perk-dot">✓</div><span>Tiếp tục bài học còn dang dở</span></div>
        <div class="perk-item"><div class="perk-dot">✓</div><span>Xem thành tích và chứng chỉ của bạn</span></div>
        <div class="perk-item"><div class="perk-dot">✓</div><span>Nhận thông báo khóa học mới nhất</span></div>
      </div>
    </div>

    <!-- Form -->
    <div class="auth-form">
      <h2>Đăng nhập</h2>
      <p class="form-sub">Nhập thông tin tài khoản của bạn</p>

      <?php if ($error): ?>
        <div class="vl-alert danger">
          <i class="mdi mdi-alert-circle"></i>
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST">
        <div class="vl-fg">
          <label for="email">Email đăng nhập</label>
          <div class="vl-input-wrap">
            <i class="mdi mdi-email-outline vl-icon"></i>
            <input type="email" id="email" name="email"
                   placeholder="vidu@gmail.com" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
        </div>

        <div class="vl-fg">
          <label for="password">
            Mật khẩu
            <a href="forgot_password.php" class="forgot">Quên mật khẩu?</a>
          </label>
          <div class="vl-input-wrap">
            <i class="mdi mdi-lock-outline vl-icon"></i>
            <input type="password" id="password" name="password"
                   placeholder="••••••••" required>
          </div>
        </div>

        <div class="remember-row">
          <input type="checkbox" id="remember" name="remember">
          <label for="remember">Ghi nhớ đăng nhập</label>
        </div>

        <button type="submit" class="btn-vl-submit">
          <i class="mdi mdi-login"></i>
          Đăng nhập
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
        Chưa có tài khoản? <a href="Register.php">Đăng ký miễn phí</a>
      </p>
    </div>

  </div>
</section>

<?php include 'footer.php'; ?>