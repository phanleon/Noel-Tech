document.addEventListener('DOMContentLoaded', () => {
    const chatBubble = document.querySelector('.chat-bubble');
    const chatWidget = document.querySelector('.chat-widget');
    const closeChat = document.querySelector('.close-chat');
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');
    const chatBody = document.querySelector('.chat-body');

    chatBubble.addEventListener('click', () => {
        chatWidget.style.display = 'flex';
        chatBubble.style.display = 'none';
    });

    closeChat.addEventListener('click', () => {
        chatWidget.style.display = 'none';
        chatBubble.style.display = 'block';
    });

    const sendMessage = async () => {
        const messageText = chatInput.value.trim();
        if (messageText === '') return;

        appendMessage(messageText, 'user');
        chatInput.value = '';
        appendMessage('...', 'bot', true); // Bot is typing...

        try {
            const response = await fetch('chatbot_api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ message: messageText }),
            });
            const data = await response.json();
            
            const typingIndicator = document.querySelector('.message.bot.typing');
            if (typingIndicator) chatBody.removeChild(typingIndicator);

            appendMessage(data.reply, 'bot');
        } catch (error) {
            console.error('Error:', error);
            const typingIndicator = document.querySelector('.message.bot.typing');
            if (typingIndicator) chatBody.removeChild(typingIndicator);
            appendMessage('Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại.', 'bot');
        }
    };

    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') sendMessage(); });

    function appendMessage(text, sender, isTyping = false) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', sender);
        if(isTyping) messageDiv.classList.add('typing');
        messageDiv.textContent = text;
        chatBody.appendChild(messageDiv);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    appendMessage("Xin chào! Tôi là trợ lý AI của NOEL TECH. Tôi có thể giúp gì cho bạn?", 'bot');
});