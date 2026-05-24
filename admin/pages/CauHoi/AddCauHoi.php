<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

if ($_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
}

$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
if ($quiz_id == 0) die("Thiáº¿u ID Äá» thi!");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text = trim($_POST['question_text']);
    $options = $_POST['options'];
    $correct_index = intval($_POST['correct_option']);

    if (empty($question_text)) {
        $error = "Vui lÃ²ng nháº­p ná»i dung cÃ¢u há»i!";
    } else {
        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
        $stmt->bind_param("is", $quiz_id, $question_text);
        
        if ($stmt->execute()) {
            $question_id = $conn->insert_id;

            $stmt_opt = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
            
            foreach ($options as $index => $opt_text) {
                $is_correct = ($index == $correct_index) ? 1 : 0;
                $stmt_opt->bind_param("isi", $question_id, $opt_text, $is_correct);
                $stmt_opt->execute();
            }

            $_SESSION['status_message'] = "Thêm cÃ¢u há»i thÃ nh cÃ´ng!";
            header("Location: ListCauHoi.php?quiz_id=$quiz_id");
            exit;
        } else {
            $error = "Lá»i Database: " . $conn->error;
        }
    }
}
?>

<?php 
include ROOT_PATH . "/admin/header.php"; 
include ROOT_PATH . "/admin/navbar.php"; 
?>

<div class="container-fluid page-body-wrapper">
    <?php include ROOT_PATH . "/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Thêm CÃ¢u há»i </h3>
            </div>
            <div class="row">
                <div class="col-md-10 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <form class="forms-sample" method="POST">
                                
                                <div class="form-group">
                                    <label><strong>Ná»i dung cÃ¢u há»i:</strong></label>
                                    <textarea class="form-control" name="question_text" rows="3" required placeholder="Nháº­p cÃ¢u há»i..."></textarea>
                                </div>

                                <label><strong>CÃ¡c phÆ°Æ¡ng Ã¡n tráº£ lá»i:</strong> (Chá»n trÃ²n bÃªn cáº¡nh ÄÃ¡p Ã¡n ÄÃºng)</label>
                                
                                <?php $labels = ['A', 'B', 'C', 'D']; ?>
                                <?php for($i=0; $i<4; $i++): ?>
                                <div class="input-group mb-3">
                                    <div class="input-group-text">
                                        <input class="form-check-input mt-0" type="radio" name="correct_option" value="<?= $i ?>" <?= $i==0?'checked':'' ?>>
                                        &nbsp; <?= $labels[$i] ?>
                                    </div>
                                    <input type="text" class="form-control" name="options[]" required placeholder="Nháº­p ÄÃ¡p Ã¡n <?= $labels[$i] ?>">
                                </div>
                                <?php endfor; ?>

                                <button type="submit" class="btn btn-gradient-primary me-2">LÆ°u CÃ¢u Há»i</button>
                                <a href="ListCauHoi.php?quiz_id=<?= $quiz_id ?>" class="btn btn-light">Há»§y</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>
