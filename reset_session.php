<?php
session_start();
header('Content-Type: application/json');
try {
    $_SESSION['score'] = 0;
    $_SESSION['lives'] = 3;
    $_SESSION['sentence_index'] = 0;
    $_SESSION['correct_count'] = 0;
    $_SESSION['incorrect_count'] = 0;
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    error_log('reset_session.php hatası: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
}
?>