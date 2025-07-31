document.addEventListener('DOMContentLoaded', () => {
    // References for toggling dark mode
    const darkModeToggle = document.getElementById('darkModeToggle');

    // References for default colors
    const defaultColors = {
        // Base Colors
        '--primaryColor': 'hsl(0, 0%, 15%)',
        '--primaryColorHover': 'hsl(0, 0%, 20%)',
        '--secondaryColor': 'hsl(0, 0%, 30%)',
        '--secondaryColorHover': 'hsl(0, 0%, 45%)',
        // Website Colors
        '--pageBackgroundColor': '#ffffff',
        '--textColor': '#000000',
    };

    // References for default colors
    const darkModeColors = {
        // Base Colors
        '--primaryColor': 'hsl(0, 0%, 15%)',
        '--primaryColorHover': 'hsl(0, 0%, 20%)',
        '--secondaryColor': 'hsl(0, 0%, 30%)',
        '--secondaryColorHover': 'hsl(0, 0%, 45%)',
        // Website Colors
        '--pageBackgroundColor': '#0d1117',
        '--textColor': '#ffffff',
    };

    /*
    |--------------------------------------------------------------------------
    | FOR TOGGLING DARK MODE -- START
    |--------------------------------------------------------------------------
    */
    // Load Dark Mode preference
    const savedDarkModeState = localStorage.getItem('darkModeEnabled');
    const isDarkModeEnabled = savedDarkModeState === 'true'; // saved as string

    // Apply dark mode class immediately based on saved state
    if (isDarkModeEnabled) {
        document.body.classList.add('dark-mode');
    } else {
        document.body.classList.remove('dark-mode');
    }

    // If the toggle element exists on this page, set its checked state
    if (darkModeToggle) {
        darkModeToggle.checked = isDarkModeEnabled;
    }

    function toggleDarkMode(isDarkMode) {
    if (isDarkMode) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkModeEnabled', 'true');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkModeEnabled', 'false');
        }
    }

    // Event listener for the dark mode toggle
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', (event) => {
            toggleDarkMode(event.target.checked);
        });
    }
    /*
    |--------------------------------------------------------------------------
    | FOR TOGGLING DARK MODE -- END
    |--------------------------------------------------------------------------
    */
});
