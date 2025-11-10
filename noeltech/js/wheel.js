// js/wheel.js
document.addEventListener('DOMContentLoaded', () => {
    const wheel = document.getElementById('wheel');
    const spinBtn = document.getElementById('spin-btn');
    const popup = document.getElementById('result-popup');
    const prizeResultText = document.getElementById('prize-result');
    const closePopupBtn = document.getElementById('close-popup');

    const numSlices = prizesData.length;
    const sliceAngle = 360 / numSlices;
    const colors = ['#e74c3c', '#c0392b']; // Hai màu xen kẽ
    let currentRotation = 0;
    let isSpinning = false;

    // --- Vẽ các miếng bánh trên vòng quay ---
    prizesData.forEach((prize, i) => {
        const slice = document.createElement('div');
        slice.classList.add('slice');
        slice.style.backgroundColor = colors[i % 2];
        slice.style.transform = `rotate(${sliceAngle * i}deg)`;
        
        const text = document.createElement('div');
        text.classList.add('slice-text');
        text.textContent = prize;
        
        slice.appendChild(text);
        wheel.appendChild(slice);
    });

    // --- Xử lý sự kiện quay ---
    spinBtn.addEventListener('click', () => {
        if (isSpinning) return;
        isSpinning = true;
        spinBtn.classList.add('disabled');
        
        // Gọi backend để quyết định giải thưởng
        fetch('wheel_handler.php')
            .then(response => response.json())
            .then(data => {
                if(data.error) {
                    alert(data.error);
                    resetWheel();
                    return;
                }
                
                const { prize, prizeIndex } = data;
                
                // Tính toán góc quay
                const targetAngle = sliceAngle * prizeIndex;
                const randomOffset = (Math.random() - 0.5) * (sliceAngle * 0.8); // Quay lệch một chút cho tự nhiên
                const rotation = 360 * 5 + (360 - targetAngle) - randomOffset; // Quay 5 vòng + góc đến giải thưởng
                
                currentRotation += rotation;
                wheel.style.transform = `rotate(${currentRotation}deg)`;
                
                // Hiển thị kết quả sau khi quay xong
                // ...
setTimeout(() => {
    // Thêm một dòng chữ mới vào popup
    let resultHTML = `<p>Bạn đã trúng thưởng:</p>
                      <p id="prize-result" class="prize-text">${prize}</p>`;
    
    // Nếu có mã code, tức là trúng voucher thật
    if (data.code) {
        resultHTML += `<p class="auto-apply-notice">Voucher đã được tự động áp dụng vào giỏ hàng của bạn!</p>`;
    }
    
    // Thay thế nội dung cũ bằng nội dung mới
    popup.querySelector('.popup-content').innerHTML = `
        <h3>Chúc Mừng!</h3>
        ${resultHTML}
        <button id="close-popup">Đóng</button>
    `;

    // Phải thêm lại sự kiện click cho nút đóng mới
    document.getElementById('close-popup').addEventListener('click', () => {
        popup.style.display = 'none';
        resetWheel();
    });

    popup.style.display = 'flex';
}, 5500);
// ...
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại.');
                resetWheel();
            });
    });

    closePopupBtn.addEventListener('click', () => {
        popup.style.display = 'none';
        resetWheel();
    });

    function resetWheel() {
        isSpinning = false;
        spinBtn.classList.remove('disabled');
    }
});