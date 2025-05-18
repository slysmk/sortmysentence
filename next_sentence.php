<?php
session_start();
header('Content-Type: application/json');
try {
    $_SESSION['sentence_index'] = ($_SESSION['sentence_index'] ?? 0) + 1;
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    error_log('next_sentence.php hatası: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
}
?>