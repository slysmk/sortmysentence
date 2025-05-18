<?php
session_start();

// SQLite veritabanına bağlan
try {
    $db = new PDO('sqlite:/usr/local/lsws/sortsentence/html/sentences.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tabloyu kontrol et ve yoksa oluştur
    $db->exec("CREATE TABLE IF NOT EXISTS sentences (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sentence TEXT NOT NULL
    )");

    // Toplam cümle sayısını al
    $totalSentences = $db->query("SELECT COUNT(*) FROM sentences")->fetchColumn();

    // Oturumda puan, can, doğru/yanlış sayaçlarını başlat
    if (!isset($_SESSION['score'])) {
        $_SESSION['score'] = 0;
    }
    if (!isset($_SESSION['lives'])) {
        $_SESSION['lives'] = 3;
    }
    if (!isset($_SESSION['sentence_index'])) {
        $_SESSION['sentence_index'] = 0;
    }
    if (!isset($_SESSION['correct_count'])) {
        $_SESSION['correct_count'] = 0;
    }
    if (!isset($_SESSION['incorrect_count'])) {
        $_SESSION['incorrect_count'] = 0;
    }

    if ($totalSentences == 0) {
        $sentence = "I love football"; // Varsayılan cümle
        $message = "Veritabanında cümle yok, varsayılan cümle kullanılıyor.";
    } else {
        // Bir sonraki cümle
        $_SESSION['sentence_index'] = $_SESSION['sentence_index'] % $totalSentences;
        $stmt = $db->query("SELECT sentence FROM sentences ORDER BY id LIMIT 1 OFFSET " . $_SESSION['sentence_index']);
        $sentence = $stmt->fetchColumn();

        if (!$sentence) {
            $sentence = "I love football"; // Hata durumunda varsayılan
            $message = "Cümle alınamadı, varsayılan cümle kullanılıyor.";
        }
    }
} catch (PDOException $e) {
    $sentence = "I love football"; // Hata durumunda varsayılan
    $message = "Veritabanı hatası: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Sort My Sentence</title>
    <meta name="description" content="Play Sort My Sentence, arrange words to form correct sentences in this fun and educational game!">
    <meta name="keywords" content="word sorting game, sentence game, puzzle game, educational game, Sort My Sentence">
    <meta name="author" content="Sort My Sentence">
    <link rel="canonical" href="https://sortmysentence.com/game.php">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="game-container">
        <h1>Sort My Sentence</h1>
        <p>Click the words to arrange them in the correct order above. Click arranged words to return them.</p>
        <?php if (isset($message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <div id="score">Score: <?php echo $_SESSION['score']; ?></div>
        <div id="lives">Lives: <?php echo $_SESSION['lives']; ?></div>
        <div id="correct-count">Correct: <?php echo $_SESSION['correct_count']; ?></div>
        <div id="incorrect-count">Incorrect: <?php echo $_SESSION['incorrect_count']; ?></div>
        <div id="arrange-area"></div>
        <div id="word-list"></div>
        <div id="feedback"></div>
        <div class="button-container">
            <button class="reset-button" onclick="resetGame()">Reset</button>
            <button class="next-button" onclick="nextSentence()">Next Sentence</button>
        </div>
        <div class="home-button-container">
            <a href="index.html"><button class="home-button">Back to Home</button></a>
        </div>
    </div>
    <script>
        // PHP’den gelen verileri JavaScript’e aktar
        const originalSentence = <?php echo json_encode($sentence); ?>;
        const totalSentences = <?php echo json_encode($totalSentences); ?>;
        const currentIndex = <?php echo json_encode($_SESSION['sentence_index']); ?>;
    </script>
    <script src="game.js"></script>
</body>
</html>