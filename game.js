// game.js
// sortmysentence.com için oyun mantığı ve oturum yönetimi

// Kelimeleri karıştıran yardımcı fonksiyon
function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[i], array[j]];
    }
    return array;
}

// AJAX isteği ile oturum verilerini güncelleme
function updateGameState(url, params) {
    console.log('updateGameState çağrıldı, url:', url, 'params:', params);
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(params).toString()
    })
    .then(response => {
        console.log('Sunucu yanıtı, durum:', response.status);
        if (!response.ok) {
            throw new Error('Sunucu hatası: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Sunucu verisi:', data);
        if (data.error || data.status === 'error') {
            console.error('Hata:', data.error || data.message);
            document.getElementById('feedback').textContent = 'Hata: ' + (data.error || data.message);
        } else {
            // DOM'u güncelle (update_score.php'den gelen veriler için)
            if (data.score !== undefined) {
                const scoreElement = document.getElementById('score');
                const livesElement = document.getElementById('lives');
                const correctCountElement = document.getElementById('correct-count');
                const incorrectCountElement = document.getElementById('incorrect-count');
                
                if (scoreElement) scoreElement.textContent = 'Score: ' + data.score;
                if (livesElement) livesElement.textContent = 'Lives: ' + data.lives;
                if (correctCountElement) correctCountElement.textContent = 'Correct: ' + data.correct_count;
                if (incorrectCountElement) incorrectCountElement.textContent = 'Incorrect: ' + data.incorrect_count;
                
                // Oyun durumunu kontrol et
                if (data.lives <= 0) {
                    document.getElementById('feedback').textContent = 'Game Over! Please reset to continue.';
                    disableGameControls();
                }
            } else {
                document.getElementById('feedback').textContent = 'İşlem başarılı.';
            }
        }
    })
    .catch(error => {
        console.error('İstek hatası:', error);
        document.getElementById('feedback').textContent = 'Bir hata oluştu, lütfen tekrar deneyin.';
    });
}

// Oyun kontrollerini devre dışı bırak
function disableGameControls() {
    document.querySelectorAll('.word').forEach(word => word.onclick = null);
    document.querySelector('.next-button').disabled = true;
}

// Cümle sıralama doğruluğunu kontrol et
function checkSentenceOrder() {
    console.log('checkSentenceOrder çağrıldı');
    const arrangedWords = Array.from(document.querySelectorAll('#arrange-area .word')).map(el => el.textContent.trim());
    const correctWords = originalSentence.split(' ');
    const isCorrect = arrangedWords.join(' ') === correctWords.join(' ');

    console.log('Kullanıcı sıralaması:', arrangedWords, 'Doğru mu?', isCorrect);
    return isCorrect;
}

// Kelimeleri yükle ve oyunu başlat
function loadWords() {
    console.log('loadWords çağrıldı, cümle:', originalSentence);
    const wordList = document.getElementById('word-list');
    const arrangeArea = document.getElementById('arrange-area');
    wordList.innerHTML = '';
    arrangeArea.innerHTML = '';

    // Kelimeleri karıştır
    const words = shuffleArray(originalSentence.split(' '));
    
    // Kelimeleri #word-list'e ekle
    words.forEach(word => {
        const wordElement = document.createElement('div');
        wordElement.className = 'word';
        wordElement.textContent = word;
        wordElement.onclick = () => moveWord(wordElement, arrangeArea, wordList);
        wordList.appendChild(wordElement);
    });
}

// Kelimeyi taşıma (word-list ↔ arrange-area)
function moveWord(wordElement, toArea, fromArea) {
    console.log('moveWord çağrıldı, kelime:', wordElement.textContent);
    fromArea.removeChild(wordElement);
    wordElement.onclick = () => moveWord(wordElement, fromArea, toArea);
    toArea.appendChild(wordElement);

    // Sıralama kontrolü
    const isCorrect = checkSentenceOrder();
    if (toArea.id === 'arrange-area' && toArea.childElementCount === originalSentence.split(' ').length) {
        if (isCorrect) {
            document.getElementById('feedback').textContent = 'Correct! Moving to next sentence...';
            updateGameState('/update_correct_count.php', { action: 'increment_correct' });
            updateGameState('/update_score.php', { action: 'increment_score' });
            setTimeout(nextSentence, 1000);
        } else {
            document.getElementById('feedback').textContent = 'Incorrect! Try again or reset.';
            updateGameState('/update_score.php', { action: 'increment_incorrect' });
            updateGameState('/update_score.php', { action: 'decrement_lives' });
        }
    }
}

// Oyunu sıfırla
function resetGame() {
    console.log('resetGame çağrıldı');
    updateGameState('/update_score.php', { action: 'reset' });
    document.getElementById('feedback').textContent = 'Game reset!';
    loadWords();
    document.querySelector('.next-button').disabled = false;
}

// Bir sonraki cümleye geç
function nextSentence() {
    console.log('nextSentence çağrıldı');
    updateGameState('/next_sentence.php', {});
    loadWords();
    document.getElementById('feedback').textContent = '';
}

// Oyun mantığını başlat
function initializeGame() {
    console.log('Oyun başlatılıyor, totalSentences:', totalSentences, 'currentIndex:', currentIndex);
    loadWords();

    // Can sıfırlanırsa oyun biter
    if (document.getElementById('lives').textContent === 'Lives: 0') {
        disableGameControls();
    }
}

// Sayfa yüklendiğinde oyunu başlat
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOMContentLoaded, oyun başlatılıyor');
    initializeGame();
});