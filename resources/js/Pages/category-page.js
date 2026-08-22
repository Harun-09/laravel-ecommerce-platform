function initCategoryPage() {
    const sortSelect = document.getElementById('category-sort');
    if (!sortSelect) return;

    const categoryUrl = sortSelect.dataset.categoryUrl || window.location.pathname;

    sortSelect.addEventListener('change', () => {
        const params = new URLSearchParams(window.location.search);

        if (sortSelect.value) {
            params.set('sort', sortSelect.value);
        } else {
            params.delete('sort');
        }

        const queryString = params.toString();
        window.location.href = queryString ? `${categoryUrl}?${queryString}` : categoryUrl;
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCategoryPage);
} else {
    initCategoryPage();
}
