    <footer>
        <p>Lost & Found System</p>
        <small>Securely manage lost and found items across your community.</small>
    </footer>
</div>
 
<script>

document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const html = document.documentElement;
    const themeIcon = themeToggle.querySelector('i');

    
    const currentTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', currentTheme);
    updateThemeIcon(currentTheme);

    
    themeToggle.addEventListener('click', function() {
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    });

    function updateThemeIcon(theme) {
        themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
});


function searchItems() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const cards = document.querySelectorAll('.item-card');
    const activeFilter = document.querySelector('.filter-btn.active')?.dataset.filter || 'all';
    const categoryFilter = document.getElementById('category-filter')?.value || 'all';
    let visibleCount = 0;
    let totalCount = cards.length;

    cards.forEach(card => {
        const title = card.dataset.title || '';
        const description = card.dataset.description || '';
        const location = card.dataset.location || '';
        const type = card.dataset.type || '';
        const category = card.dataset.category || '';

        
        const textMatch = searchTerm === '' ||
                         title.includes(searchTerm) ||
                         description.includes(searchTerm) ||
                         location.includes(searchTerm) ||
                         type.includes(searchTerm) ||
                         category.includes(searchTerm);

        
        const typeMatch = activeFilter === 'all' || type === activeFilter;

        const categoryMatch = categoryFilter === 'all' || category === categoryFilter;

        const shouldShow = textMatch && typeMatch && categoryMatch;

        card.style.display = shouldShow ? 'block' : 'none';
        if (shouldShow) visibleCount++;
    });

    
    const resultsDiv = document.getElementById('search-results');
    if (resultsDiv) {
        if (searchTerm || activeFilter !== 'all' || categoryFilter !== 'all') {
            if (visibleCount === 0) {
                resultsDiv.textContent = 'No items found matching your search.';
            } else {
                resultsDiv.textContent = `Showing ${visibleCount} of ${totalCount} items`;
            }
            resultsDiv.style.display = 'block';
        } else {
            resultsDiv.style.display = 'none';
        }
    }


    const clearBtn = document.getElementById('clear-search');
    if (clearBtn) {
        if (searchTerm) {
            clearBtn.style.display = 'block';
        } else {
            clearBtn.style.display = 'none';
        }
    }
}


document.addEventListener('DOMContentLoaded', function() {
    // ... existing theme toggle code ...

   
    const clearBtn = document.getElementById('clear-search');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            document.getElementById('search-input').value = '';
            searchItems();
        });
    }

    
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
           
            filterBtns.forEach(b => b.classList.remove('active'));
            
            this.classList.add('active');
        
            searchItems();
        });
    });

    
    const categoryFilter = document.getElementById('category-filter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            searchItems();
        });
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>