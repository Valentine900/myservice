document.addEventListener("DOMContentLoaded", function() {
    const audioPlayers = document.querySelectorAll(".audio-player");

    audioPlayers.forEach(player => {
        const audio = player.querySelector("audio");
        const playBtn = player.querySelector(".play-btn");
        const progressBar = player.querySelector(".progress-bar");
        const progress = player.querySelector(".progress");
        const currentTimeLabel = player.querySelector(".current-time");
        const durationLabel = player.querySelector(".duration");

        // Обновляем длительность песни
        audio.addEventListener("loadedmetadata", function() {
            durationLabel.textContent = formatTime(audio.duration);
        });

        // Обновляем текущее время и прогресс
        audio.addEventListener("timeupdate", function() {
            currentTimeLabel.textContent = formatTime(audio.currentTime);
            const percent = (audio.currentTime / audio.duration) * 100;
            progress.style.width = percent + "%";
        });

        // Обработка нажатия на кнопку "Play"
        playBtn.addEventListener("click", function() {
            // Остановить все другие аудиоплееры
            audioPlayers.forEach(otherPlayer => {
                const otherAudio = otherPlayer.querySelector("audio");
                const otherPlayBtn = otherPlayer.querySelector(".play-btn");
                if (otherAudio !== audio && !otherAudio.paused) {
                    otherAudio.pause();
                    otherAudio.currentTime = 0; // Сбросить время
                    otherPlayBtn.textContent = "Play"; // Обновить текст кнопки
                }
            });

            if (audio.paused) {
                audio.play();
                playBtn.textContent = "Pause";
            } else {
                audio.pause();
                playBtn.textContent = "Play";
            }
        });

        // Форматирование времени
        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
        }

        // Обработка клика по прогресс-бару
        progressBar.addEventListener("click", function(e) {
            const rect = progressBar.getBoundingClientRect();
            const offsetX = e.clientX - rect.left;
            const totalWidth = rect.width;
            const percent = offsetX / totalWidth;
            audio.currentTime = percent * audio.duration;
        });
    });
});