<?php
session_start();
include(__DIR__ . '/../config.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $login_input = trim($_POST['email']); 
    $password = $_POST['password'];
    $sqlAdmin = "SELECT id, username, email, password FROM admins WHERE username = ? OR email = ?";
    $stmtAdmin = $conn->prepare($sqlAdmin);
    $stmtAdmin->bind_param("ss", $login_input, $login_input);
    $stmtAdmin->execute();
    $resultAdmin = $stmtAdmin->get_result();

    if ($resultAdmin->num_rows === 1) {
        $admin = $resultAdmin->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_role'] = 'admins';
            header("Location: index.php");
            exit;
        }
    }

    $sqlUser = "SELECT id, fullname, email, password, role FROM users WHERE email = ?";
    $stmtUser = $conn->prepare($sqlUser);
    $stmtUser->bind_param("s", $login_input);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();

    if ($resultUser->num_rows === 1) {
        $user = $resultUser->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
        
            $role = strtolower(trim($user['role'])); 
            $_SESSION['user_role'] = $role;
            if ($role === 'admins' || $role === 'admin' || $role === 'teacher') {
                header("Location: index.php");
                exit;
            } 
            else {
                header("Location: ../home.php");
                exit;
            }
        }
    }

    $message = "Sai tài khoản hoặc mật khẩu.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .login-box {
            width: 350px; margin: 100px auto; padding: 20px; 
            background: white; border-radius: 8px; 
            box-shadow: 0 0 10px rgba(0,0,0,.2);
        }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; border: none; color: white; cursor: pointer; }
        button:hover { background: #0056b3; }
        .error { color: red; text-align: center; margin-bottom: 10px; }
        .nav-item { list-style: none; margin-top: 10px; text-align: center; }
        .nav-link { text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

<div class="login-box">
    <h2 style="text-align:center;">Đăng nhập Admin</h2>

    <?php if(!empty($message)) echo "<p class='error'>$message</p>"; ?>

    <form method="POST">
        <input type="text" name="email" placeholder="Email hoặc Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng nhập</button>
        
        <li class="nav-item">
            <a class="nav-link" href="Register.php"> Đăng kí tài khoản </a>
        </li>
    </form>
</div>

</body>
</html>