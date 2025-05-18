<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $sentences = $_POST['sentences'] ?? '';

    // Parola doğrulama (kendi parolanızı buraya yazın)
    if ($password !== 'm1Ppfu@djppjhamg') {
        $message = "Hatalı parola!";
    } else {
        try {
            // SQLite veritabanına bağlanma
            $db = new PDO('sqlite:/usr/local/lsws/sortsentence/html/sentences.db');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Tabloyu kontrol et ve yoksa oluştur
            $db->exec("CREATE TABLE IF NOT EXISTS sentences (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                sentence TEXT NOT NULL
            )");

            // Cümleleri artı işaretine göre böl
            $sentenceArray = explode('+', trim($sentences));
            $stmt = $db->prepare("INSERT INTO sentences (sentence) VALUES (:sentence)");
            $successCount = 0;

            foreach ($sentenceArray as $sentence) {
                $sentence = trim($sentence); // Boşlukları temizle
                if (!empty($sentence)) { // Boş cümleleri atla
                    $stmt->execute([':sentence' => $sentence]);
                    $successCount++;
                }
            }

            $message = "$successCount cümle başarıyla eklendi!";
        } catch (PDOException $e) {
            $message = "Hata: " . $e->getMessage() . " | PDO Drivers: " . implode(", ", PDO::getAvailableDrivers());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli</title>
</head>
<body>
    <h2>Cümle Ekle</h2>
    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="password">Parola:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <label for="sentences">Cümleler (artı işaretiyle ayırarak girin, örn: i love football+we play tennis):</label><br>
        <textarea id="sentences" name="sentences" rows="5" cols="50" required></textarea><br><br>
        <input type="submit" value="Cümleleri Ekle">
    </form>
</body>
</html>