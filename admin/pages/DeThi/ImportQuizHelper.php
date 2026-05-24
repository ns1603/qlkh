<?php
function import_quiz_from_csv(mysqli $conn, int $quiz_id, string $tmp_path): int {
    $imported = 0;
    if (!file_exists($tmp_path)) return 0;
    if (($handle = fopen($tmp_path, 'r')) === false) return 0;

    $stmt_question = $conn->prepare(
        "INSERT INTO questions (quiz_id, question_text, level) VALUES (?, ?, ?)"
    );
    $stmt_option = $conn->prepare(
        "INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)"
    );

    $is_first = true;

    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
        if ($is_first) { $is_first = false; continue; }
        if (count($row) < 7) continue;

        [$q_text,$a,$b,$c,$d,$correct,$level] = $row;

        $q_text = trim($q_text);
        if ($q_text === '') continue;

        $level = strtolower(trim($level));
        if (!in_array($level,['easy','medium','hard'])) {
            $level = 'medium';
        }

        $options = [
            ['text'=>trim($a),'letter'=>'A'],
            ['text'=>trim($b),'letter'=>'B'],
            ['text'=>trim($c),'letter'=>'C'],
            ['text'=>trim($d),'letter'=>'D'],
        ];

        foreach ($options as $o) {
            if ($o['text'] === '') continue 2;
        }

        $correct = strtoupper(trim($correct));
        if (!in_array($correct,['A','B','C','D'])) $correct = 'A';

        // Insert question
        $stmt_question->bind_param("iss",$quiz_id,$q_text,$level);
        if (!$stmt_question->execute()) continue;
        $question_id = $conn->insert_id;

        // ð² RANDOM ÄÃP ÃN
        shuffle($options);

        foreach ($options as $o) {
            $is_correct = ($o['letter'] === $correct) ? 1 : 0;
            $stmt_option->bind_param("isi",$question_id,$o['text'],$is_correct);
            $stmt_option->execute();
        }

        $imported++;
    }

    fclose($handle);
    return $imported;
}
