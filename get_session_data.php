<?php
session_start();
header('Content-Type: application/json');
try {
    echo json_encode([
        'status' => 'success',
        'score' => $_SESSION['score'] ?? 0,
        'lives' => $_SESSION['lives'] ?? 3,
        'correct_count' => $_SESSION['correct_count'] ?? 0,
        'incorrect_count' => $_SESSION['incorrect_count'] ?? 0
    ]);
} catch (Exception $e) {
    error_log('get_session_data.php hatası: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
}
?>