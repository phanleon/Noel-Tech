// js/music-player.js
document.addEventListener('DOMContentLoaded', () => {
    const music = document.getElementById('background-music');
    const musicButton = document.getElementById('music-control-button');
    const musicIcon = musicButton.querySelector('i');
    let isPlaying = false;

    // Cố gắng tự động phát nhạc khi người dùng tương tác lần đầu
    // Trình duyệt hiện đại chặn tự động phát âm thanh cho đến khi người dùng nhấp chuột
    function playMusicOnFirstInteraction() {
        if (!isPlaying) {
            music.play().then(() => {
                isPlaying = true;
                musicIcon.classList.remove('fa-volume-mute');
                musicIcon.classList.add('fa-volume-up');
            }).catch(error => {
                // Tự động phát bị chặn, không sao cả, người dùng có thể bật thủ công
                console.log("Autoplay was prevented. User must interact to play music.");
            });
        }
        // Gỡ bỏ sự kiện này sau lần tương tác đầu tiên
        document.body.removeEventListener('click', playMusicOnFirstInteraction);
    }

    document.body.addEventListener('click', playMusicOnFirstInteraction);


    // Xử lý khi nhấp vào nút điều khiển
    musicButton.addEventListener('click', () => {
        if (isPlaying) {
            music.pause();
            isPlaying = false;
            musicIcon.classList.remove('fa-volume-up');
            musicIcon.classList.add('fa-volume-mute');
        } else {
            music.play();
            isPlaying = true;
            musicIcon.classList.remove('fa-volume-mute');
            musicIcon.classList.add('fa-volume-up');
        }
    });
});