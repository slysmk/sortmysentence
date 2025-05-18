<?php
session_start();
header('Content-Type: application/json');
try {
    if (isset($_POST['lives'])) {
        $_SESSION['lives'] = (int)$_POST['lives'];
        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception('Geçersiz istek: lives parametresi eksik.');
    }
} catch (Exception $e) {
    error_log('update_lives.php hatası: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
}
?>