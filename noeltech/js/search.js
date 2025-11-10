// js/search.js
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const searchResultsContainer = document.getElementById('search-results');
    let debounceTimer;

    searchInput.addEventListener('keyup', () => {
        // Hủy timer cũ nếu người dùng gõ tiếp
        clearTimeout(debounceTimer);

        const query = searchInput.value.trim();

        if (query.length < 2) {
            searchResultsContainer.style.display = 'none';
            return;
        }

        // Đặt một timer mới. Chỉ gửi request sau khi người dùng ngừng gõ 300ms
        debounceTimer = setTimeout(() => {
            fetch(`search_handler.php?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    displayResults(data);
                })
                .catch(error => console.error('Error:', error));
        }, 300);
    });

    function displayResults(results) {
        if (results.length === 0) {
            searchResultsContainer.style.display = 'none';
            return;
        }

        let html = '<ul>';
        results.forEach(product => {
            html += `
                <li>
                    <a href="product.php?slug=${product.slug}">
                        <img src="${product.image}" alt="${product.name}" class="result-image">
                        <span class="result-name">${product.name}</span>
                    </a>
                </li>
            `;
        });
        html += '</ul>';

        searchResultsContainer.innerHTML = html;
        searchResultsContainer.style.display = 'block';
    }

    // Ẩn kết quả khi click ra ngoài
    document.addEventListener('click', (e) => {
        if (!searchResultsContainer.contains(e.target) && e.target !== searchInput) {
            searchResultsContainer.style.display = 'none';
        }
    });
});