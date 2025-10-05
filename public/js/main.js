// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

// Set default dates for date pickers
document.addEventListener('DOMContentLoaded', function() {
    initTheme();

    // Add toggle button to navbar (if not already present)
    const navbar = document.querySelector('.navbar .container-fluid');
    if (navbar && !document.querySelector('.theme-toggle')) {
        const toggleHtml = `
      <div class="theme-toggle">
        <span class="me-2">Theme</span>
        <button class="theme-toggle-btn" onclick="toggleTheme()">
          <i class="fas fa-sun sun"></i>
          <i class="fas fa-moon moon"></i>
          <span class="toggle-thumb"></span>
        </button>
      </div>
    `;
        navbar.insertAdjacentHTML('beforeend', toggleHtml);
    }

    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

    // Set dates for all date inputs
    document.querySelectorAll('input[type="date"]').forEach(input => {
        if (input.id.includes('From')) {
            input.valueAsDate = firstDay;
        } else if (input.id.includes('To') || input.id.includes('Date')) {
            input.valueAsDate = today;
        }
    });
});

// Dark mode toggle functionality
function initTheme() {
    const darkModeSelected = localStorage.getItem('darkMode') === 'true';
    document.body.setAttribute('data-theme', darkModeSelected ? 'dark' : 'light');
    return darkModeSelected;
}

function toggleTheme() {
    const darkModeSelected = initTheme();
    const newTheme = !darkModeSelected;
    localStorage.setItem('darkMode', newTheme);
    document.body.setAttribute('data-theme', newTheme ? 'dark' : 'light');
}
