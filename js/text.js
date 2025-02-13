document.getElementById('toggleLyrics').addEventListener('click', function() {
    var lyrics = document.getElementById('lyrics');
    
    if (lyrics.classList.contains('hidden')) {
        lyrics.classList.remove('hidden');
        lyrics.classList.add('show');
        setTimeout(function() {
            lyrics.classList.add('visible');
        }, 200); // Небольшая задержка для запуска перехода
    } else {
        lyrics.classList.remove('visible');
        setTimeout(function() {
            lyrics.classList.add('hidden');
            lyrics.classList.remove('show');
        }, 800); // Задержка, чтобы дождаться окончания анимации
    }
});