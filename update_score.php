<?php
session_start();
header('Content-Type: application/json');

// Gelen veriyi logla
error_log("Gelen POST: " . print_r($_POST, true));

// Action kontrolü
if (!isset($_POST['action']) || !in_array($_POST['action'], [
    'increment_score', 'increment_correct', 'increment_incorrect', 
    'decrement_lives', 'reset', 'next_sentence'
])) {
    error_log("Hata: action eksik veya yanlış. Gelen action: " . ($_POST['action'] ?? 'yok'));
    echo json_encode(['error' => 'Geçersiz istek: action eksik veya yanlış.']);
    exit;
}

// Oturum değişkenlerini başlat
if (!isset($_SESSION['score'])) $_SESSION['score'] = 0;
if (!isset($_SESSION['lives'])) $_SESSION['lives'] = 3;
if (!isset($_SESSION['correct_count'])) $_SESSION['correct_count'] = 0;
if (!isset($_SESSION['incorrect_count'])) $_SESSION['incorrect_count'] = 0;
if (!isset($_SESSION['sentence_index'])) $_SESSION['sentence_index'] = 0;

// Action’a göre işlem yap
switch ($_POST['action']) {
    case 'increment_score':
        $_SESSION['score']++;
        break;
    case 'increment_correct':
        $_SESSION['correct_count']++;
        break;
    case 'increment_incorrect':
        $_SESSION['incorrect_count']++;
        break;
    case 'decrement_lives':
        $_SESSION['lives'] = max(0, $_SESSION['lives'] - 1);
        break;
    case 'reset':
        $_SESSION['score'] = 0;
        $_SESSION['lives'] = 3;
        $_SESSION['correct_count'] = 0;
        $_SESSION['incorrect_count'] = 0;
        $_SESSION['sentence_index'] = 0;
        break;
    case 'next_sentence':
        $_SESSION['sentence_index']++;
        break;
}

echo json_encode([
    'score' => $_SESSION['score'],
    'lives' => $_SESSION['lives'],
    'correct_count' => $_SESSION['correct_count'],
    'incorrect_count' => $_SESSION['incorrect_count'],
    'sentence_index' => $_SESSION['sentence_index']
]);
?>