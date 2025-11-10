</main>

        <footer class="main-footer">
            <div class="container footer-links">
                <a href="#">About Us</a>
                <a href="#">Liên Hệ</a>
                <a href="#">FAQS</a>
                <a href="#">Privacy Policy</a>
            </div>
            <div class="copyright">
                <p>Merry Christmas </p>
                <p>&copy; NOEL TECH</p>
            </div>
        </footer>
    </div> <!-- .page-wrapper -->
<!-- ... footer của bạn ... -->
</footer>
</div> <!-- .page-wrapper -->

<!-- Swiper's JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Khởi tạo Swiper -->
<script>
  var swiper = new Swiper(".mySwiper", {
    loop: true, // Lặp lại vô hạn
    autoplay: {
      delay: 4000, // Tự động chuyển slide sau 4 giây
      disableOnInteraction: false, // Không dừng khi người dùng tương tác
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true, // Cho phép nhấp vào dấu chấm để chuyển slide
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });
</script>
<!-- ... các script khác của bạn như Swiper.js ... -->

<!-- ===== HTML CỦA CHATBOX AI ===== -->
<div class="chat-widget">
    <div class="chat-header">
        <span>NOEL TECH ChatBox</span>
        <span class="close-chat">&times;</span>
    </div>
    <div class="chat-body">
        <!-- Tin nhắn sẽ được thêm vào đây -->
    </div>
    <div class="chat-footer">
        <input type="text" id="chatInput" placeholder="Ask me anything...">
        <button id="sendBtn"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>
<div class="chat-bubble">
    <i class="fas fa-comment-dots"></i>
</div>

<!-- Link đến file JavaScript điều khiển chatbox -->
<script src="js/chatbot.js"></script>
<!-- ===== BẮT ĐẦU PHẦN ÂM NHẠC ===== -->
<audio id="background-music" loop>
    <source src="audio/christmas-music.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>

<div id="music-control-button">
    <i class="fas fa-volume-up"></i>
</div>
<!-- ===== KẾT THÚC PHẦN ÂM NHẠC ===== -->

<!-- Link đến file JavaScript điều khiển âm nhạc -->
<script src="js/music-player.js"></script> <!-- Sẽ tạo file này -->

<!-- ===== SCRIPT TÌM KIẾM GỢI Ý ===== -->v
<script src="js/search.js"></script>


</body>
</html>
