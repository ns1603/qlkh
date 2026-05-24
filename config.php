<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'learning_app';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// Determine ROOT_PATH and BASE_PATH dynamically
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', str_replace('\\', '/', __DIR__));
}

if (!defined('BASE_PATH')) {
    $project_path = '';
    if (isset($_SERVER['DOCUMENT_ROOT'])) {
        $doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        $root_dir = ROOT_PATH;
        $doc_root = rtrim($doc_root, '/');
        if (strpos($root_dir, $doc_root) === 0) {
            $project_path = substr($root_dir, strlen($doc_root));
        }
    }
    $project_path = rtrim(str_replace('\\', '/', $project_path), '/');
    define('BASE_PATH', $project_path);
}

