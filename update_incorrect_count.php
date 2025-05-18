<?php
session_start();
header('Content-Type: application/json');
try {
    if (isset($_POST['action']) && $_POST['action'] === 'increment_incorrect') {
        $_SESSION['incorrect_count'] = ($_SESSION['incorrect_count'] ?? 0) + 1;
        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception('Geçersiz istek: action eksik veya yanlış.');
    }
} catch (Exception $e) {
    error_log('update_incorrect_count.php hatası: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
}
?>