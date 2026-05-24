<?php
if (!defined('BASE_PATH')) {
    include_once(dirname(__DIR__) . '/config.php');
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
  <!-- CSS -->
  <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/admin/assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/admin/assets/css/style.css">
  <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/admin/assets/vendors/css/vendor.bundle.base.css">
  <script src="<?php echo BASE_PATH; ?>/admin/assets/js/misc.js"></script>
  <link rel="shortcut icon" href="<?php echo BASE_PATH; ?>/admin/assets/images/favicon.ico" />
</head>

<body>
  <div class="container-scroller">
